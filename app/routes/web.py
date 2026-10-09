from fastapi import APIRouter, Depends, Request, Form
from fastapi.responses import HTMLResponse, RedirectResponse
from fastapi.templating import Jinja2Templates
from sqlalchemy.orm import Session
from pathlib import Path

from app.config import settings
from app.database import get_db
from app.models import Member, SysDevice, HrDevice, SysTCPUDP, CPULoadLog, MemoryLog, DatabaseTarget, WebTarget
from app.auth import get_current_user, set_flash, get_flash
from app.poller import run_poll_all

router = APIRouter()
templates = Jinja2Templates(directory=str(Path(__file__).resolve().parent.parent / "templates"))

def render(request: Request, db: Session, template_name: str, context: dict = None):
    ctx = {
        "request": request,
        "app_name": settings.APP_NAME,
        "current_user": get_current_user(request, db),
        "flash": get_flash(request),
        "client_ip": request.client.host if request.client else "127.0.0.1",
    }
    if context:
        ctx.update(context)
    return templates.TemplateResponse(request=request, name=template_name, context=ctx)


@router.get("/", response_class=HTMLResponse)
def index_page(request: Request, db: Session = Depends(get_db)):
    devices = db.query(SysDevice).order_by(SysDevice.status.asc(), SysDevice.sys_name.asc()).all()
    servers = db.query(HrDevice).order_by(HrDevice.status.asc()).all()
    services = db.query(SysTCPUDP).order_by(SysTCPUDP.service_name.asc()).all()

    dev_up = sum(1 for d in devices if d.status == 1)
    dev_down = len(devices) - dev_up
    srv_up = sum(1 for s in servers if s.status == 1)
    srv_down = len(servers) - srv_up
    svc_up = sum(1 for s in services if s.status == "respond")
    svc_down = len(services) - svc_up

    total_items = len(devices) + len(servers) + len(services)
    total_up = dev_up + srv_up + svc_up
    health_pct = round((total_up / total_items * 100), 1) if total_items > 0 else 100.0

    return render(request, db, "index.html", {
        "active_page": "home",
        "devices": devices,
        "servers": servers,
        "services": services,
        "dev_up_count": dev_up,
        "dev_down_count": dev_down,
        "srv_up_count": srv_up,
        "srv_down_count": srv_down,
        "svc_up_count": svc_up,
        "svc_down_count": svc_down,
        "health_pct": health_pct,
    })


@router.get("/network", response_class=HTMLResponse)
def network_page(request: Request, db: Session = Depends(get_db)):
    devices = db.query(SysDevice).order_by(SysDevice.status.asc(), SysDevice.ipaddress.asc()).all()
    return render(request, db, "network.html", {
        "active_page": "network",
        "devices": devices,
    })


@router.get("/servers", response_class=HTMLResponse)
def servers_page(request: Request, db: Session = Depends(get_db)):
    servers = db.query(HrDevice).order_by(HrDevice.status.asc(), HrDevice.ipaddress.asc()).all()
    databases = db.query(DatabaseTarget).order_by(DatabaseTarget.name.asc()).all()
    web_targets = db.query(WebTarget).order_by(WebTarget.name.asc()).all()

    srv_up = sum(1 for s in servers if s.status == 1)
    srv_down = len(servers) - srv_up

    db_online = sum(1 for d in databases if d.status == "ONLINE")
    db_degraded = sum(1 for d in databases if d.status == "DEGRADED")
    db_offline = len(databases) - db_online - db_degraded

    web_healthy = sum(1 for w in web_targets if w.health_status in ("EXCELLENT", "GOOD"))
    web_warning = sum(1 for w in web_targets if w.health_status == "WARNING")
    web_critical = len(web_targets) - web_healthy - web_warning

    ssl_expiring = sum(1 for w in web_targets if 0 <= w.ssl_days_left <= 30)

    return render(request, db, "server.html", {
        "active_page": "servers",
        "servers": servers,
        "databases": databases,
        "web_targets": web_targets,
        "srv_up": srv_up,
        "srv_down": srv_down,
        "db_online": db_online,
        "db_offline": db_offline + db_degraded,
        "web_healthy": web_healthy,
        "web_critical": web_critical + web_warning,
        "ssl_expiring": ssl_expiring,
    })


@router.post("/servers/check-now")
def servers_check_now(request: Request, db: Session = Depends(get_db)):
    from app.server_monitor import run_all_server_monitors
    run_all_server_monitors(db)
    set_flash(request, "Database and Web Server diagnostics completed!", "success")
    return RedirectResponse("/servers", status_code=303)


@router.post("/servers/db/add")
def servers_db_add(
    request: Request,
    name: str = Form(...),
    db_type: str = Form(...),
    host: str = Form(...),
    port: int = Form(...),
    details: str = Form(""),
    db: Session = Depends(get_db)
):
    target = DatabaseTarget(
        name=name.strip(),
        db_type=db_type.strip(),
        host=host.strip(),
        port=port,
        details=details.strip(),
        status="OFFLINE"
    )
    from app.server_monitor import probe_database
    probe_database(target)
    db.add(target)
    db.commit()
    set_flash(request, f"Database target '{name}' added and probed successfully.", "success")
    return RedirectResponse("/servers", status_code=303)


@router.post("/servers/db/delete")
def servers_db_delete(request: Request, db_id: int = Form(...), db: Session = Depends(get_db)):
    target = db.query(DatabaseTarget).filter(DatabaseTarget.id == db_id).first()
    if target:
        db.delete(target)
        db.commit()
        set_flash(request, f"Database target '{target.name}' deleted.", "info")
    return RedirectResponse("/servers", status_code=303)


@router.post("/servers/web/add")
def servers_web_add(
    request: Request,
    name: str = Form(...),
    url: str = Form(...),
    db: Session = Depends(get_db)
):
    target = WebTarget(name=name.strip(), url=url.strip())
    from app.server_monitor import probe_web_server
    probe_web_server(target)
    db.add(target)
    db.commit()
    set_flash(request, f"Web target '{name}' added and checked successfully.", "success")
    return RedirectResponse("/servers", status_code=303)


@router.post("/servers/web/delete")
def servers_web_delete(request: Request, web_id: int = Form(...), db: Session = Depends(get_db)):
    target = db.query(WebTarget).filter(WebTarget.id == web_id).first()
    if target:
        db.delete(target)
        db.commit()
        set_flash(request, f"Web target '{target.name}' deleted.", "info")
    return RedirectResponse("/servers", status_code=303)



@router.get("/services", response_class=HTMLResponse)
def services_page(request: Request, db: Session = Depends(get_db)):
    services = db.query(SysTCPUDP).order_by(SysTCPUDP.service_name.asc()).all()
    return render(request, db, "service.html", {
        "active_page": "services",
        "services": services,
    })


@router.get("/device/{ip}", response_class=HTMLResponse)
def device_detail_page(ip: str, request: Request, db: Session = Depends(get_db)):
    device = db.query(SysDevice).filter(SysDevice.ipaddress == ip).first()
    if not device:
        set_flash(request, f"Device with IP {ip} not found.", "warning")
        return RedirectResponse("/network", status_code=303)

    # Get recent CPU logs
    cpu_logs = db.query(CPULoadLog).filter(CPULoadLog.ipaddress == ip).order_by(CPULoadLog.timestamp.desc()).limit(15).all()
    cpu_logs = list(reversed(cpu_logs))

    # Get recent Memory logs
    mem_logs = db.query(MemoryLog).filter(MemoryLog.ipaddress == ip).order_by(MemoryLog.timestamp.desc()).limit(15).all()
    mem_logs = list(reversed(mem_logs))

    chart_labels = [log.timestamp.strftime("%H:%M:%S") for log in cpu_logs]
    chart_cpu = [log.cpu_load for log in cpu_logs]
    chart_mem = [log.memory_use for log in mem_logs]

    return render(request, db, "device_detail.html", {
        "active_page": "network",
        "device": device,
        "chart_labels": chart_labels,
        "chart_cpu": chart_cpu,
        "chart_mem": chart_mem,
    })


@router.post("/poll-now")
def poll_now(request: Request):
    run_poll_all()
    set_flash(request, "Poll completed! Device statuses and metrics have been refreshed.", "success")
    referer = request.headers.get("referer", "/")
    return RedirectResponse(referer, status_code=303)


@router.get("/login", response_class=HTMLResponse)
def login_page(request: Request, db: Session = Depends(get_db)):
    if get_current_user(request, db):
        return RedirectResponse("/", status_code=303)
    return render(request, db, "login.html", {"active_page": "login"})


@router.post("/login")
def login_post(
    request: Request,
    username: str = Form(...),
    password: str = Form(...),
    db: Session = Depends(get_db)
):
    member = db.query(Member).filter(Member.username == username.strip()).first()
    if not member or not member.check_password(password):
        set_flash(request, "Invalid username or password.", "danger")
        return RedirectResponse("/login", status_code=303)

    request.session["user_id"] = member.id
    set_flash(request, f"Welcome back, {member.name or member.username}!", "success")
    return RedirectResponse("/", status_code=303)


@router.get("/logout")
def logout(request: Request):
    request.session.clear()
    set_flash(request, "You have been logged out safely.", "info")
    return RedirectResponse("/login", status_code=303)


@router.get("/security-audit", response_class=HTMLResponse)
def security_audit_page(request: Request, db: Session = Depends(get_db)):
    from app.security_scanner import run_security_audit
    audit_results = run_security_audit(db)
    return render(request, db, "security_audit.html", {
        "active_page": "security",
        "audit": audit_results,
    })


@router.post("/security-audit/scan")
def security_audit_scan(request: Request):
    set_flash(request, "Security audit re-run successfully! Latest posture findings are shown below.", "success")
    return RedirectResponse("/security-audit", status_code=303)


@router.get("/analytics", response_class=HTMLResponse)
def analytics_page(request: Request, db: Session = Depends(get_db)):
    from app.analytics import compute_network_performance
    perf = compute_network_performance(db)
    return render(request, db, "analytics.html", {
        "active_page": "analytics",
        "perf": perf,
    })


@router.post("/analytics/refresh")
def analytics_refresh(request: Request):
    run_poll_all()
    set_flash(request, "Network performance analysis refreshed with latest telemetry metrics.", "success")
    return RedirectResponse("/analytics", status_code=303)


