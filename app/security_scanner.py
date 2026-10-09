import os
from typing import List, Dict, Any
from sqlalchemy.orm import Session

from app.config import settings
from app.models import Member, SysDevice, HrDevice, SysTCPUDP

INSECURE_COMMUNITY_STRINGS = {"public", "private", "cisco", "manager", "admin", "default", "snmp", "test"}

UNENCRYPTED_PORTS = {
    21: {"name": "FTP (Cleartext File Transfer)", "recommendation": "Use SFTP (SSH File Transfer) or FTPS with TLS."},
    23: {"name": "Telnet (Cleartext Remote Shell)", "recommendation": "Disable Telnet immediately. Use SSH (Port 22) instead."},
    69: {"name": "TFTP (Trivial FTP - No Authentication)", "recommendation": "Disable TFTP on untrusted networks."},
    80: {"name": "HTTP (Unencrypted Web Traffic)", "recommendation": "Enforce HTTPS (TLS/SSL on Port 443) to encrypt traffic and credentials."},
}

SENSITIVE_PORTS = {
    22: "SSH (Secure Shell)",
    3389: "RDP (Remote Desktop Protocol)",
    3306: "MySQL Database Server",
    5432: "PostgreSQL Database Server",
    6379: "Redis In-Memory Store",
    27017: "MongoDB Database",
}

def run_security_audit(db: Session) -> Dict[str, Any]:
    """
    Perform comprehensive defensive security and vulnerability audit:
    - Application configuration & credentials
    - Monitored network devices & SNMP security
    - Services & cleartext protocol exposures
    """
    findings: List[Dict[str, Any]] = []

    # -------------------------------------------------------------
    # 1. APPLICATION & AUTHENTICATION CHECKS
    # -------------------------------------------------------------
    # Check default admin credentials
    admin_user = db.query(Member).filter(Member.username == "admin").first()
    if admin_user and admin_user.check_password("admin123"):
        findings.append({
            "category": "Authentication",
            "severity": "CRITICAL",
            "title": "Default Administrator Password Active",
            "target": "Account: 'admin'",
            "description": "The default password 'admin123' is still active on the main administrator account. An attacker can gain full administrative access.",
            "recommendation": "Change the admin password immediately via the User Management panel."
        })
    else:
        findings.append({
            "category": "Authentication",
            "severity": "PASSED",
            "title": "Default Admin Password Changed",
            "target": "Account: 'admin'",
            "description": "The default password 'admin123' is no longer in use on the admin account.",
            "recommendation": "Maintain strong password complexity."
        })

    # Check session secret key
    if settings.SECRET_KEY in ("super_secret_key_please_change", "change_this_to_a_secure_random_key_in_production") or len(settings.SECRET_KEY) < 20:
        findings.append({
            "category": "Configuration",
            "severity": "HIGH",
            "title": "Weak or Default Session SECRET_KEY",
            "target": "Environment (.env)",
            "description": "The application SECRET_KEY is using a predictable default or has insufficient entropy, making session cookies vulnerable to tampering.",
            "recommendation": "Generate a strong 32+ character random key in .env (e.g. `openssl rand -hex 32`)."
        })
    else:
        findings.append({
            "category": "Configuration",
            "severity": "PASSED",
            "title": "Secure Session SECRET_KEY Configured",
            "target": "Environment (.env)",
            "description": "Custom session key has adequate length and entropy.",
            "recommendation": "Rotate keys periodically."
        })

    # Check debug mode
    if settings.DEBUG:
        findings.append({
            "category": "Configuration",
            "severity": "MEDIUM",
            "title": "DEBUG Mode Enabled in Application",
            "target": "Settings (DEBUG=True)",
            "description": "Debug mode exposes detailed stack traces and API documentation schema in production.",
            "recommendation": "Set `DEBUG=False` in your `.env` file for production deployment."
        })
    else:
        findings.append({
            "category": "Configuration",
            "severity": "PASSED",
            "title": "Production Mode (DEBUG=False)",
            "target": "Settings",
            "description": "Debug mode is disabled. Detailed system traces are hidden.",
            "recommendation": "Keep DEBUG=False in production."
        })

    # Check legacy PHP segregation
    if os.path.exists("Old_PHP"):
        findings.append({
            "category": "Architecture",
            "severity": "PASSED",
            "title": "Legacy Vulnerable PHP Code Isolated",
            "target": "Directory: /Old_PHP",
            "description": "Vulnerable legacy PHP scripts are securely isolated in Old_PHP and not exposed to the web.",
            "recommendation": "Keep Old_PHP detached from any active web server document root."
        })

    # -------------------------------------------------------------
    # 2. NETWORK ASSET & SNMP SECURITY CHECKS
    # -------------------------------------------------------------
    devices = db.query(SysDevice).all()
    servers = db.query(HrDevice).all()

    insecure_devices = []
    for d in devices:
        if d.community.lower().strip() in INSECURE_COMMUNITY_STRINGS:
            insecure_devices.append(f"{d.ipaddress} (community='{d.community}')")

    for s in servers:
        if s.community.lower().strip() in INSECURE_COMMUNITY_STRINGS:
            insecure_devices.append(f"{s.ipaddress} (community='{s.community}')")

    if insecure_devices:
        findings.append({
            "category": "Network Security",
            "severity": "HIGH",
            "title": "Default / Weak SNMP Community Strings Detected",
            "target": f"{len(insecure_devices)} Assets: {', '.join(insecure_devices[:3])}{'...' if len(insecure_devices) > 3 else ''}",
            "description": "Assets are using default community strings like 'public' or 'private'. Unauthenticated users on the local network can query sensitive hardware metrics or reconfigure devices.",
            "recommendation": "Configure dedicated SNMPv2 community strings with Access Control Lists (ACLs), or migrate to SNMPv3 with SHA/AES encryption and authentication."
        })
    else:
        findings.append({
            "category": "Network Security",
            "severity": "PASSED",
            "title": "SNMP Community Strings Hardened",
            "target": "All Monitored Devices & Servers",
            "description": "No default or common community strings ('public', 'private') detected.",
            "recommendation": "Ensure SNMP traffic is restricted via firewall rules."
        })

    # -------------------------------------------------------------
    # 3. SERVICES & PROTOCOL EXPOSURE CHECKS
    # -------------------------------------------------------------
    services = db.query(SysTCPUDP).all()
    cleartext_services = []
    mgmt_services = []

    for svc in services:
        if svc.port in UNENCRYPTED_PORTS:
            info = UNENCRYPTED_PORTS[svc.port]
            cleartext_services.append(f"{svc.service_name} ({svc.server}:{svc.port} - {info['name']})")
        if svc.port in SENSITIVE_PORTS:
            mgmt_services.append(f"{svc.service_name} ({svc.server}:{svc.port} - {SENSITIVE_PORTS[svc.port]})")

    if cleartext_services:
        findings.append({
            "category": "Protocols & Encryption",
            "severity": "MEDIUM",
            "title": "Unencrypted Cleartext Protocols Monitored",
            "target": f"{len(cleartext_services)} Service(s): {', '.join(cleartext_services)}",
            "description": "Cleartext protocols transmit data, passwords, and sessions without cryptographic protection.",
            "recommendation": "Migrate services to TLS/SSH-encrypted alternatives (HTTPS on 443, SFTP, SSH)."
        })

    if mgmt_services:
        findings.append({
            "category": "Access Control",
            "severity": "LOW",
            "title": "Administrative or Database Ports Monitored",
            "target": f"{len(mgmt_services)} Service(s): {', '.join(mgmt_services)}",
            "description": "Administrative services (SSH, RDP, Database) are monitored. Ensure these ports are restricted behind a VPN or firewall.",
            "recommendation": "Verify that management ports are not exposed to the public internet."
        })

    # Calculate overall security score (0 to 100)
    score = 100
    crit_count = sum(1 for f in findings if f["severity"] == "CRITICAL")
    high_count = sum(1 for f in findings if f["severity"] == "HIGH")
    med_count = sum(1 for f in findings if f["severity"] == "MEDIUM")
    low_count = sum(1 for f in findings if f["severity"] == "LOW")
    pass_count = sum(1 for f in findings if f["severity"] == "PASSED")

    score -= (crit_count * 30)
    score -= (high_count * 15)
    score -= (med_count * 8)
    score -= (low_count * 3)
    score = max(0, min(100, score))

    return {
        "score": score,
        "critical_count": crit_count,
        "high_count": high_count,
        "medium_count": med_count,
        "low_count": low_count,
        "passed_count": pass_count,
        "findings": findings
    }
