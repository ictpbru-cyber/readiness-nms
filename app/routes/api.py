from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.database import get_db
from app.models import SysDevice, HrDevice, SysTCPUDP, DatabaseTarget, WebTarget
from app.poller import run_poll_all

router = APIRouter(prefix="/api")

@router.get("/stats")
def get_stats(db: Session = Depends(get_db)):
    dev_count = db.query(SysDevice).count()
    dev_up = db.query(SysDevice).filter(SysDevice.status == 1).count()

    srv_count = db.query(HrDevice).count()
    srv_up = db.query(HrDevice).filter(HrDevice.status == 1).count()

    svc_count = db.query(SysTCPUDP).count()
    svc_up = db.query(SysTCPUDP).filter(SysTCPUDP.status == "respond").count()

    return {
        "devices": {"total": dev_count, "up": dev_up, "down": dev_count - dev_up},
        "servers": {"total": srv_count, "up": srv_up, "down": srv_count - srv_up},
        "services": {"total": svc_count, "up": svc_up, "down": svc_count - svc_up},
    }

@router.get("/devices")
def list_devices(db: Session = Depends(get_db)):
    devices = db.query(SysDevice).all()
    return [
        {
            "ip": d.ipaddress,
            "name": d.sys_name,
            "status": "UP" if d.status == 1 else "DOWN",
            "cpu_load": d.sys_cpu_load,
            "memory_use": d.sys_memory_use,
            "memory_free": d.sys_memory_free,
            "uptime": d.sys_uptime,
            "last_updated": d.last_updated.isoformat() if d.last_updated else None
        }
        for d in devices
    ]

@router.get("/services")
def list_services(db: Session = Depends(get_db)):
    services = db.query(SysTCPUDP).all()
    return [
        {
            "id": s.id,
            "name": s.service_name,
            "server": s.server,
            "port": s.port,
            "status": s.status,
            "latency_ms": s.responded_ms,
            "icmp": "alive" if s.icmp_status == 0 else "timeout"
        }
        for s in services
    ]

@router.post("/poll")
def trigger_poll():
    run_poll_all()
    return {"status": "ok", "message": "Poll cycle executed successfully"}

@router.get("/security-audit")
def api_security_audit(db: Session = Depends(get_db)):
    from app.security_scanner import run_security_audit
    return run_security_audit(db)

@router.get("/analytics")
def api_analytics(db: Session = Depends(get_db)):
    from app.analytics import compute_network_performance
    return compute_network_performance(db)

@router.get("/databases")
def api_databases(db: Session = Depends(get_db)):
    dbs = db.query(DatabaseTarget).all()
    return [
        {
            "id": d.id,
            "name": d.name,
            "type": d.db_type,
            "host": d.host,
            "port": d.port,
            "status": d.status,
            "latency_ms": d.latency_ms,
            "version": d.version_info
        }
        for d in dbs
    ]

@router.get("/web-servers")
def api_web_servers(db: Session = Depends(get_db)):
    webs = db.query(WebTarget).all()
    return [
        {
            "id": w.id,
            "name": w.name,
            "url": w.url,
            "status_code": w.status_code,
            "latency_ms": w.latency_ms,
            "server": w.server_software,
            "ssl_days_left": w.ssl_days_left,
            "health": w.health_status
        }
        for w in webs
    ]



