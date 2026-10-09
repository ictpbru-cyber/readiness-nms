import time
import socket
import ssl
import datetime
from urllib.parse import urlparse
import requests
from sqlalchemy.orm import Session

from app.models import DatabaseTarget, WebTarget

def probe_database(target: DatabaseTarget):
    """
    Probe database services (Oracle, MySQL, PostgreSQL, MSSQL, Redis, MongoDB)
    Measures TCP connection latency, protocol handshakes, and operational readiness.
    """
    t0 = time.perf_counter()
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.settimeout(2.5)
    try:
        s.connect((target.host, target.port))
        latency = round((time.perf_counter() - t0) * 1000, 2)
        version_detected = "Service Listening"

        db_type = target.db_type.upper()

        if "MYSQL" in db_type:
            # MySQL initial handshake packet
            banner = s.recv(1024)
            if len(banner) > 5:
                # Byte 4 is protocol version, followed by null-terminated version string
                ver_str = banner[5:].split(b'\x00')[0].decode('latin-1', 'ignore')
                if ver_str:
                    version_detected = f"MySQL/MariaDB v{ver_str}"
        elif "REDIS" in db_type:
            s.sendall(b"PING\r\n")
            resp = s.recv(128)
            if b"PONG" in resp:
                version_detected = "Redis Active (PONG OK)"
            elif b"NOAUTH" in resp:
                version_detected = "Redis Active (Auth Required)"
        elif "POSTGRE" in db_type:
            # PostgreSQL SSLRequest packet: length 8, code 80877103
            s.sendall(b"\x00\x00\x00\x08\x04\xd2\x16\x2f")
            resp = s.recv(16)
            if resp in (b"S", b"N"):
                version_detected = "PostgreSQL Server Active"
        elif "ORACLE" in db_type:
            # Oracle TNS Listener check
            version_detected = "Oracle TNS Listener Responding"
        elif "MSSQL" in db_type:
            version_detected = "MS SQL Server Port Active"
        elif "MONGO" in db_type:
            version_detected = "MongoDB Instance Listening"

        s.close()

        target.status = "ONLINE" if latency < 400 else "DEGRADED"
        target.latency_ms = latency
        target.version_info = version_detected
    except Exception as e:
        target.status = "OFFLINE"
        target.latency_ms = 0.0
        target.version_info = f"Connection Failed ({type(e).__name__})"
    finally:
        target.last_checked = datetime.datetime.utcnow()


def probe_web_server(target: WebTarget):
    """
    Inspect Web Server health:
    - HTTP status code & TTFB latency
    - Server header banner (Nginx, Apache, IIS, etc.)
    - SSL/TLS certificate validity & expiry days
    - Security headers compliance
    """
    t0 = time.perf_counter()
    headers = {"User-Agent": "Readiness-NMS/2.0 HealthProbe"}

    try:
        resp = requests.get(target.url, headers=headers, timeout=4.0, verify=False, allow_redirects=True)
        latency = round((time.perf_counter() - t0) * 1000, 2)
        target.status_code = resp.status_code
        target.latency_ms = latency

        # Extract Server software banner
        server_hdr = resp.headers.get("Server") or resp.headers.get("X-Powered-By") or "Web Server"
        target.server_software = server_hdr[:95]

        # Check security headers
        sec_headers = ["strict-transport-security", "x-content-type-options", "x-frame-options", "content-security-policy"]
        found_sec = sum(1 for h in sec_headers if h in resp.headers)
        target.security_headers_count = found_sec

        # SSL/TLS Certificate inspection
        parsed = urlparse(target.url)
        if parsed.scheme == "https":
            hostname = parsed.hostname or parsed.netloc.split(":")[0]
            port = parsed.port or 443
            try:
                ctx = ssl.create_default_context()
                with socket.create_connection((hostname, port), timeout=3.0) as sock:
                    with ctx.wrap_socket(sock, server_hostname=hostname) as ssock:
                        cert = ssock.getpeercert()
                        exp_str = cert.get("notAfter", "")
                        if exp_str:
                            exp_date = datetime.datetime.strptime(exp_str, "%b %d %H:%M:%S %Y %Z")
                            now = datetime.datetime.utcnow()
                            target.ssl_days_left = (exp_date - now).days

                        # Issuer
                        issuer_dict = dict(x[0] for x in cert.get("issuer", ()))
                        target.ssl_issuer = issuer_dict.get("organizationName") or issuer_dict.get("commonName") or "Valid CA"
            except Exception:
                target.ssl_days_left = 0
                target.ssl_issuer = "SSL Verification Warning"
        else:
            target.ssl_days_left = -1
            target.ssl_issuer = "No SSL (Plain HTTP)"

        # Classify health status
        if target.status_code == 200:
            if (target.ssl_days_left == -1 or target.ssl_days_left > 30) and latency < 600:
                target.health_status = "EXCELLENT"
            elif (target.ssl_days_left == -1 or target.ssl_days_left > 14) and latency < 1200:
                target.health_status = "GOOD"
            else:
                target.health_status = "WARNING"
        elif 300 <= target.status_code < 400:
            target.health_status = "GOOD"  # Normal Redirect
        elif 400 <= target.status_code < 500:
            target.health_status = "WARNING"
        else:
            target.health_status = "CRITICAL"

    except Exception as e:
        target.status_code = 0
        target.latency_ms = 0.0
        target.server_software = "Unreachable"
        target.health_status = "CRITICAL"
        target.ssl_issuer = str(e)[:50]
    finally:
        target.last_checked = datetime.datetime.utcnow()


def run_all_server_monitors(db: Session):
    """Run check cycle for all database and web server targets."""
    # Databases
    databases = db.query(DatabaseTarget).all()
    for db_target in databases:
        probe_database(db_target)

    # Web servers
    web_targets = db.query(WebTarget).all()
    for web_target in web_targets:
        probe_web_server(web_target)

    db.commit()
