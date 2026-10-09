from fastapi import APIRouter, Depends, Request, Form
from fastapi.responses import HTMLResponse, RedirectResponse
from fastapi.templating import Jinja2Templates
from sqlalchemy.orm import Session
from pathlib import Path

from app.config import settings
from app.database import get_db
from app.models import Member, SysDevice, HrDevice, SysTCPUDP, SysInterface, OidProfile, DeviceCustomOid
from app.auth import get_current_user, require_admin, set_flash, get_flash

router = APIRouter(prefix="/admin")
templates = Jinja2Templates(directory=str(Path(__file__).resolve().parent.parent / "templates"))

def render_admin(request: Request, db: Session, template_name: str, context: dict = None):
    user = require_admin(request, db)
    if not user:
        set_flash(request, "Access denied. Administrator privileges required.", "danger")
        return RedirectResponse("/login", status_code=303)

    ctx = {
        "request": request,
        "app_name": settings.APP_NAME,
        "current_user": user,
        "flash": get_flash(request),
        "client_ip": request.client.host if request.client else "127.0.0.1",
    }
    if context:
        ctx.update(context)
    return templates.TemplateResponse(request=request, name=template_name, context=ctx)


@router.get("", response_class=HTMLResponse)
def admin_dashboard(request: Request, db: Session = Depends(get_db)):
    return render_admin(request, db, "admin/dashboard.html", {"active_page": "admin_dashboard"})


# --- DEVICES CRUD ---
@router.get("/devices", response_class=HTMLResponse)
def admin_devices(request: Request, db: Session = Depends(get_db)):
    devices = db.query(SysDevice).all()
    profiles = db.query(OidProfile).all()
    return render_admin(request, db, "admin/devices.html", {
        "active_page": "admin_devices",
        "devices": devices,
        "profiles": profiles,
    })

@router.post("/devices/add")
def admin_devices_add(
    request: Request,
    ipaddress: str = Form(...),
    sys_name: str = Form(""),
    community: str = Form("public"),
    manufacturer: str = Form("Unknown"),
    profile_name: str = Form("Cisco Systems"),
    sys_location: str = Form(""),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    ip = ipaddress.strip()
    existing = db.query(SysDevice).filter(SysDevice.ipaddress == ip).first()
    if existing:
        set_flash(request, f"Device with IP {ip} already exists.", "warning")
    else:
        dev = SysDevice(
            ipaddress=ip,
            sys_name=sys_name.strip(),
            community=community.strip(),
            manufacturer=manufacturer.strip() or "Unknown",
            profile_name=profile_name.strip() or "Cisco Systems",
            sys_location=sys_location.strip(),
            status=2
        )
        db.add(dev)
        db.commit()
        set_flash(request, f"Device {ip} added successfully.", "success")
    return RedirectResponse("/admin/devices", status_code=303)

@router.post("/devices/delete")
def admin_devices_delete(
    request: Request,
    ipaddress: str = Form(...),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    dev = db.query(SysDevice).filter(SysDevice.ipaddress == ipaddress.strip()).first()
    if dev:
        db.delete(dev)
        db.commit()
        set_flash(request, f"Device {ipaddress} deleted.", "info")
    return RedirectResponse("/admin/devices", status_code=303)


# --- DEVICE INTERFACES / OBJECTS SELECTION ---
@router.get("/devices/{ip}/interfaces", response_class=HTMLResponse)
def admin_device_interfaces(ip: str, request: Request, db: Session = Depends(get_db)):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    dev = db.query(SysDevice).filter(SysDevice.ipaddress == ip).first()
    if not dev:
        set_flash(request, f"Device {ip} not found.", "warning")
        return RedirectResponse("/admin/devices", status_code=303)

    interfaces = db.query(SysInterface).filter(SysInterface.ipaddress == ip).order_by(SysInterface.infid.asc()).all()
    if not interfaces:
        from app.poller import discover_device_interfaces
        interfaces = discover_device_interfaces(dev, db)

    monitored_count = sum(1 for inf in interfaces if inf.is_monitored == 1)
    ignored_count = len(interfaces) - monitored_count
    profiles = db.query(OidProfile).all()

    return render_admin(request, db, "admin/interfaces.html", {
        "active_page": "admin_devices",
        "device": dev,
        "interfaces": interfaces,
        "monitored_count": monitored_count,
        "ignored_count": ignored_count,
        "profiles": profiles,
        "custom_sensors": dev.custom_oids
    })


@router.post("/devices/{ip}/interfaces/save")
async def admin_device_interfaces_save(ip: str, request: Request, db: Session = Depends(get_db)):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    dev = db.query(SysDevice).filter(SysDevice.ipaddress == ip).first()
    if not dev:
        return RedirectResponse("/admin/devices", status_code=303)

    form_data = await request.form()
    selected_raw = form_data.getlist("monitored_ids")
    selected_ids = {int(x) for x in selected_raw if str(x).isdigit()}

    interfaces = db.query(SysInterface).filter(SysInterface.ipaddress == ip).all()
    active_count = 0
    for inf in interfaces:
        if inf.id in selected_ids:
            inf.is_monitored = 1
            active_count += 1
        else:
            inf.is_monitored = 0

    db.commit()
    set_flash(request, f"Monitoring selection saved! {active_count} of {len(interfaces)} interfaces active.", "success")
    return RedirectResponse(f"/admin/devices/{ip}/interfaces", status_code=303)


@router.post("/devices/{ip}/discover")
def admin_device_discover(ip: str, request: Request, db: Session = Depends(get_db)):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    dev = db.query(SysDevice).filter(SysDevice.ipaddress == ip).first()
    if not dev:
        return RedirectResponse("/admin/devices", status_code=303)

    from app.poller import discover_device_interfaces
    discovered = discover_device_interfaces(dev, db)
    set_flash(request, f"Discovery scan complete! Found {len(discovered)} interfaces on {dev.ipaddress}.", "success")
    return RedirectResponse(f"/admin/devices/{ip}/interfaces", status_code=303)


@router.post("/devices/{ip}/interfaces/add")
def admin_device_interface_add(
    ip: str,
    request: Request,
    infid: str = Form(...),
    interfacename: str = Form(...),
    interfacespeed: str = Form("1000000000"),
    is_monitored: str = Form(None),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    existing = db.query(SysInterface).filter(
        SysInterface.ipaddress == ip,
        SysInterface.infid == infid.strip()
    ).first()

    mon_val = 1 if is_monitored else 0
    if existing:
        existing.interfacename = interfacename.strip()
        existing.interfacespeed = interfacespeed
        existing.is_monitored = mon_val
        set_flash(request, f"Interface #{infid} updated.", "info")
    else:
        new_inf = SysInterface(
            ipaddress=ip,
            infid=infid.strip(),
            interfacename=interfacename.strip(),
            interfacespeed=interfacespeed,
            interfacestatus_admin="1",
            interfacestatus="1",
            is_monitored=mon_val
        )
        db.add(new_inf)
        set_flash(request, f"Custom interface '{interfacename}' added successfully.", "success")

    db.commit()
    return RedirectResponse(f"/admin/devices/{ip}/interfaces", status_code=303)


@router.post("/devices/{ip}/interfaces/delete")
def admin_device_interface_delete(
    ip: str,
    request: Request,
    inf_id: int = Form(...),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    inf = db.query(SysInterface).filter(SysInterface.id == inf_id, SysInterface.ipaddress == ip).first()
    if inf:
        name = inf.interfacename
        db.delete(inf)
        db.commit()
        set_flash(request, f"Interface '{name}' removed from database.", "info")

    return RedirectResponse(f"/admin/devices/{ip}/interfaces", status_code=303)


# --- SERVERS CRUD ---
@router.get("/servers", response_class=HTMLResponse)
def admin_servers(request: Request, db: Session = Depends(get_db)):
    servers = db.query(HrDevice).all()
    return render_admin(request, db, "admin/servers.html", {
        "active_page": "admin_servers",
        "servers": servers,
    })

@router.post("/servers/add")
def admin_servers_add(
    request: Request,
    ipaddress: str = Form(...),
    sys_name: str = Form(""),
    community: str = Form("public"),
    sys_description: str = Form(""),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    ip = ipaddress.strip()
    existing = db.query(HrDevice).filter(HrDevice.ipaddress == ip).first()
    if existing:
        set_flash(request, f"Server with IP {ip} already exists.", "warning")
    else:
        srv = HrDevice(
            ipaddress=ip,
            sys_name=sys_name.strip(),
            community=community.strip(),
            sys_description=sys_description.strip(),
            status=2
        )
        db.add(srv)
        db.commit()
        set_flash(request, f"Server {ip} added successfully.", "success")
    return RedirectResponse("/admin/servers", status_code=303)

@router.post("/servers/delete")
def admin_servers_delete(
    request: Request,
    ipaddress: str = Form(...),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    srv = db.query(HrDevice).filter(HrDevice.ipaddress == ipaddress.strip()).first()
    if srv:
        db.delete(srv)
        db.commit()
        set_flash(request, f"Server {ipaddress} deleted.", "info")
    return RedirectResponse("/admin/servers", status_code=303)


# --- SERVICES CRUD ---
@router.get("/services", response_class=HTMLResponse)
def admin_services(request: Request, db: Session = Depends(get_db)):
    services = db.query(SysTCPUDP).all()
    return render_admin(request, db, "admin/services.html", {
        "active_page": "admin_services",
        "services": services,
    })

@router.post("/services/add")
def admin_services_add(
    request: Request,
    service_name: str = Form(...),
    server: str = Form(...),
    port: int = Form(...),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    svc = SysTCPUDP(
        service_name=service_name.strip(),
        server=server.strip(),
        port=port,
        status="not respond"
    )
    db.add(svc)
    db.commit()
    set_flash(request, f"Service '{service_name}' added successfully.", "success")
    return RedirectResponse("/admin/services", status_code=303)

@router.post("/services/delete")
def admin_services_delete(
    request: Request,
    service_id: int = Form(...),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    svc = db.query(SysTCPUDP).filter(SysTCPUDP.id == service_id).first()
    if svc:
        db.delete(svc)
        db.commit()
        set_flash(request, f"Service deleted.", "info")
    return RedirectResponse("/admin/services", status_code=303)


# --- USERS CRUD ---
@router.get("/users", response_class=HTMLResponse)
def admin_users(request: Request, db: Session = Depends(get_db)):
    users = db.query(Member).order_by(Member.username.asc()).all()
    return render_admin(request, db, "admin/users.html", {
        "active_page": "admin_users",
        "users": users,
    })

@router.post("/users/add")
def admin_users_add(
    request: Request,
    username: str = Form(...),
    password: str = Form(...),
    name: str = Form(""),
    status: str = Form("USER"),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    u = username.strip()
    if len(password) < 6:
        set_flash(request, "Password must be at least 6 characters long.", "warning")
        return RedirectResponse("/admin/users", status_code=303)

    existing = db.query(Member).filter(Member.username == u).first()
    if existing:
        set_flash(request, f"Username '{u}' is already taken.", "warning")
    else:
        member = Member(username=u, name=name.strip(), status=status)
        member.set_password(password)
        db.add(member)
        db.commit()
        set_flash(request, f"User '{u}' created successfully.", "success")
    return RedirectResponse("/admin/users", status_code=303)

@router.post("/users/delete")
def admin_users_delete(
    request: Request,
    user_id: int = Form(...),
    db: Session = Depends(get_db)
):
    current = require_admin(request, db)
    if not current:
        return RedirectResponse("/login", status_code=303)

    if current.id == user_id:
        set_flash(request, "Cannot delete your own active account.", "warning")
        return RedirectResponse("/admin/users", status_code=303)

    target = db.query(Member).filter(Member.id == user_id).first()
    if target:
        db.delete(target)
        db.commit()
        set_flash(request, f"User '{target.username}' deleted.", "info")
    return RedirectResponse("/admin/users", status_code=303)


# --- DEVICE OID PROFILE & CUSTOM SENSORS ---
@router.post("/devices/{ip}/profile/save")
def admin_device_profile_save(
    ip: str,
    request: Request,
    profile_name: str = Form("Cisco Systems"),
    custom_oid_cpu: str = Form(""),
    custom_oid_mem_used: str = Form(""),
    custom_oid_mem_free: str = Form(""),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    dev = db.query(SysDevice).filter(SysDevice.ipaddress == ip).first()
    if not dev:
        set_flash(request, f"Device {ip} not found.", "warning")
        return RedirectResponse("/admin/devices", status_code=303)

    dev.profile_name = profile_name.strip()
    dev.custom_oid_cpu = custom_oid_cpu.strip()
    dev.custom_oid_mem_used = custom_oid_mem_used.strip()
    dev.custom_oid_mem_free = custom_oid_mem_free.strip()
    db.commit()
    set_flash(request, f"OID Profile and custom MIB settings updated for {ip}!", "success")
    return RedirectResponse(f"/admin/devices/{ip}/interfaces", status_code=303)


@router.post("/devices/{ip}/sensors/add")
def admin_device_sensor_add(
    ip: str,
    request: Request,
    name: str = Form(...),
    oid: str = Form(...),
    unit: str = Form(""),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    dev = db.query(SysDevice).filter(SysDevice.ipaddress == ip).first()
    if not dev:
        set_flash(request, f"Device {ip} not found.", "warning")
        return RedirectResponse("/admin/devices", status_code=303)

    clean_oid = oid.strip().lstrip(".")
    sensor = DeviceCustomOid(
        ipaddress=ip,
        name=name.strip(),
        oid=clean_oid,
        unit=unit.strip(),
        last_value="Waiting poll..."
    )
    db.add(sensor)
    db.commit()
    set_flash(request, f"Custom sensor '{name}' (OID: {clean_oid}) added successfully!", "success")
    return RedirectResponse(f"/admin/devices/{ip}/interfaces", status_code=303)


@router.post("/devices/{ip}/sensors/delete")
def admin_device_sensor_delete(
    ip: str,
    request: Request,
    sensor_id: int = Form(...),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    sensor = db.query(DeviceCustomOid).filter(DeviceCustomOid.id == sensor_id, DeviceCustomOid.ipaddress == ip).first()
    if sensor:
        name = sensor.name
        db.delete(sensor)
        db.commit()
        set_flash(request, f"Sensor '{name}' removed.", "info")
    return RedirectResponse(f"/admin/devices/{ip}/interfaces", status_code=303)


# --- OID PROFILES & MIB MANAGEMENT ---
@router.get("/oid-profiles", response_class=HTMLResponse)
def admin_oid_profiles(request: Request, db: Session = Depends(get_db)):
    profiles = db.query(OidProfile).order_by(OidProfile.vendor.asc(), OidProfile.name.asc()).all()
    devices = db.query(SysDevice).all()
    return render_admin(request, db, "admin/oid_profiles.html", {
        "active_page": "admin_oid_profiles",
        "profiles": profiles,
        "devices": devices,
    })


@router.post("/oid-profiles/add")
def admin_oid_profiles_add(
    request: Request,
    vendor: str = Form(...),
    name: str = Form(...),
    oid_cpu: str = Form(""),
    oid_mem_used: str = Form(""),
    oid_mem_free: str = Form(""),
    description: str = Form(""),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    clean_name = name.strip()
    existing = db.query(OidProfile).filter(OidProfile.name == clean_name).first()
    if existing:
        existing.vendor = vendor.strip()
        existing.oid_cpu = oid_cpu.strip()
        existing.oid_mem_used = oid_mem_used.strip()
        existing.oid_mem_free = oid_mem_free.strip()
        existing.description = description.strip()
        set_flash(request, f"Profile '{clean_name}' updated successfully.", "info")
    else:
        new_prof = OidProfile(
            vendor=vendor.strip(),
            name=clean_name,
            oid_cpu=oid_cpu.strip(),
            oid_mem_used=oid_mem_used.strip(),
            oid_mem_free=oid_mem_free.strip(),
            description=description.strip()
        )
        db.add(new_prof)
        set_flash(request, f"OID Profile '{clean_name}' added successfully.", "success")
    db.commit()
    return RedirectResponse("/admin/oid-profiles", status_code=303)


@router.post("/oid-profiles/delete")
def admin_oid_profiles_delete(
    request: Request,
    profile_id: int = Form(...),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    prof = db.query(OidProfile).filter(OidProfile.id == profile_id).first()
    if prof:
        name = prof.name
        db.delete(prof)
        db.commit()
        set_flash(request, f"OID Profile '{name}' deleted.", "info")
    return RedirectResponse("/admin/oid-profiles", status_code=303)


# --- LIVE SNMP OID TESTER ---
@router.get("/snmp-tester", response_class=HTMLResponse)
def admin_snmp_tester_page(request: Request, db: Session = Depends(get_db)):
    devices = db.query(SysDevice).all()
    profiles = db.query(OidProfile).all()
    return render_admin(request, db, "admin/snmp_tester.html", {
        "active_page": "admin_snmp_tester",
        "devices": devices,
        "profiles": profiles,
        "probe_result": None,
        "query_params": {
            "host": "",
            "community": "public",
            "port": 161,
            "oid": "1.3.6.1.2.1.1.1.0",
            "mode": "get"
        }
    })


@router.post("/snmp-tester/query", response_class=HTMLResponse)
def admin_snmp_tester_query(
    request: Request,
    host: str = Form(...),
    community: str = Form("public"),
    port: int = Form(161),
    oid: str = Form(...),
    mode: str = Form("get"),
    db: Session = Depends(get_db)
):
    if not require_admin(request, db):
        return RedirectResponse("/login", status_code=303)

    from app.snmp_tester import probe_snmp
    probe_res = probe_snmp(
        host=host.strip(),
        community=community.strip(),
        port=port,
        oid=oid.strip(),
        mode=mode.strip()
    )

    devices = db.query(SysDevice).all()
    profiles = db.query(OidProfile).all()
    return render_admin(request, db, "admin/snmp_tester.html", {
        "active_page": "admin_snmp_tester",
        "devices": devices,
        "profiles": profiles,
        "probe_result": probe_res,
        "query_params": {
            "host": host.strip(),
            "community": community.strip(),
            "port": port,
            "oid": oid.strip(),
            "mode": mode.strip()
        }
    })
