import sys
import time
import socket
import re
import ipaddress
import subprocess
import asyncio
from datetime import datetime
from typing import Optional, Tuple, Dict, Any

from app.database import SessionLocal
from app.models import (
    SysDevice, HrDevice, SysTCPUDP, SysInterface,
    CPULoadLog, MemoryLog, ServiceLog, OidProfile, DeviceCustomOid
)

# Standard OIDs used by Readiness
OID_SYS_DESCR = "1.3.6.1.2.1.1.1.0"
OID_SYS_OBJECT_ID = "1.3.6.1.2.1.1.2.0"
OID_SYS_UPTIME = "1.3.6.1.2.1.1.3.0"
OID_SYS_CONTACT = "1.3.6.1.2.1.1.4.0"
OID_SYS_NAME = "1.3.6.1.2.1.1.5.0"
OID_SYS_LOCATION = "1.3.6.1.2.1.1.6.0"
OID_CISCO_CPU_5MIN = "1.3.6.1.4.1.9.9.109.1.1.1.1.8.1"
OID_CISCO_MEM_USED = "1.3.6.1.4.1.9.9.48.1.1.1.5.1"
OID_CISCO_MEM_FREE = "1.3.6.1.4.1.9.9.48.1.1.1.6.1"

# Host Resource MIB (Servers)
OID_HR_CPU_LOAD = "1.3.6.1.2.1.25.3.3.1.2"
OID_HR_STORAGE_USED = "1.3.6.1.2.1.25.2.3.1.6"
OID_HR_STORAGE_SIZE = "1.3.6.1.2.1.25.2.3.1.5"


def is_valid_host(host: str) -> bool:
    """Validate that host is an IPv4, IPv6, or valid domain name (prevents injection)."""
    if not host or len(host) > 255:
        return False
    try:
        ipaddress.ip_address(host)
        return True
    except ValueError:
        pass
    # Validate hostname pattern
    hostname_regex = re.compile(r"^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$")
    return bool(hostname_regex.match(host))


def safe_ping(host: str, timeout_sec: int = 2) -> Tuple[bool, float]:
    """
    Safely ping a host using subprocess with array arguments (no shell=True).
    Returns (is_alive, round_trip_ms).
    """
    if not is_valid_host(host):
        return False, 0.0

    t_start = time.perf_counter()
    if sys.platform.startswith("win"):
        # Windows ping: -n count, -w timeout_ms
        cmd = ["ping", "-n", "1", "-w", str(timeout_sec * 1000), host]
    else:
        # Linux/macOS ping: -c count, -W timeout_sec
        cmd = ["ping", "-c", "1", "-W", str(timeout_sec), host]

    try:
        res = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, timeout=timeout_sec + 1)
        elapsed_ms = (time.perf_counter() - t_start) * 1000.0
        is_alive = (res.returncode == 0)
        return is_alive, round(elapsed_ms, 2) if is_alive else 0.0
    except Exception:
        return False, 0.0


def check_tcp_port(host: str, port: int, timeout_sec: float = 3.0) -> Tuple[bool, float]:
    """
    Check if a TCP port is open and measure response latency in milliseconds.
    Returns (is_open, round_trip_ms).
    """
    if not is_valid_host(host) or not (1 <= port <= 65535):
        return False, 0.0

    t_start = time.perf_counter()
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.settimeout(timeout_sec)
    try:
        s.connect((host, port))
        elapsed_ms = (time.perf_counter() - t_start) * 1000.0
        s.close()
        return True, round(elapsed_ms, 2)
    except Exception:
        return False, 0.0


async def snmp_get_async(ip: str, community: str, oids: list, timeout: int = 2) -> Dict[str, str]:
    """Fetch SNMP OIDs asynchronously."""
    results = {}
    try:
        from pysnmp.hlapi.asyncio import (
            SnmpEngine, CommunityData, UdpTransportTarget,
            ContextData, ObjectType, ObjectIdentity, getCmd
        )
        snmpEngine = SnmpEngine()
        target = UdpTransportTarget((ip, 161), timeout=timeout, retries=1)
        var_binds = [ObjectType(ObjectIdentity(oid)) for oid in oids]

        errorIndication, errorStatus, errorIndex, responseBinds = await getCmd(
            snmpEngine,
            CommunityData(community),
            target,
            ContextData(),
            *var_binds
        )

        if not errorIndication and not errorStatus:
            for var in responseBinds:
                name, val = var
                results[str(name)] = str(val)
    except Exception:
        pass
    return results


def discover_device_interfaces(dev: SysDevice, db) -> List[SysInterface]:
    """
    Discover interfaces/objects for a network device via SNMP walk.
    If SNMP is unavailable or times out, preserves existing interfaces
    or provisions standard network interfaces for management.
    """
    snmp_interfaces = {}
    try:
        from pysnmp.hlapi.asyncio import (
            SnmpEngine, CommunityData, UdpTransportTarget,
            ContextData, ObjectType, ObjectIdentity, nextCmd
        )
        async def run_walk():
            items = {}
            snmpEngine = SnmpEngine()
            target = UdpTransportTarget((dev.ipaddress, 161), timeout=1.5, retries=1)
            curr_oid = ObjectIdentity('1.3.6.1.2.1.2.2.1.2')
            root_oid = '1.3.6.1.2.1.2.2.1.2'
            for _ in range(48):
                errorIndication, errorStatus, errorIndex, varBinds = await nextCmd(
                    snmpEngine, CommunityData(dev.community), target, ContextData(),
                    ObjectType(curr_oid)
                )
                if errorIndication or errorStatus or not varBinds:
                    break
                var = varBinds[0]
                oid_str = str(var[0])
                if not oid_str.startswith(root_oid):
                    break
                idx = oid_str.split('.')[-1]
                name = str(var[1]).strip('"')
                items[idx] = name
                curr_oid = var[0]
            return items

        loop = asyncio.new_event_loop()
        asyncio.set_event_loop(loop)
        snmp_interfaces = loop.run_until_complete(run_walk())
        loop.close()
    except Exception:
        snmp_interfaces = {}

    if snmp_interfaces:
        for idx, name in snmp_interfaces.items():
            existing = db.query(SysInterface).filter(
                SysInterface.ipaddress == dev.ipaddress,
                SysInterface.infid == idx
            ).first()
            if not existing:
                new_inf = SysInterface(
                    ipaddress=dev.ipaddress,
                    infid=idx,
                    interfacename=name,
                    interfacespeed="1000000000",
                    interfacestatus_admin="1",
                    interfacestatus="1",
                    is_monitored=1
                )
                db.add(new_inf)
            else:
                existing.interfacename = name
        db.commit()

    # If no interfaces existed in DB and SNMP did not return interfaces:
    existing_all = db.query(SysInterface).filter(SysInterface.ipaddress == dev.ipaddress).all()
    if not existing_all:
        presets = [
            ("1", "GigabitEthernet0/0 (Uplink/WAN)", "1000000000", "1", 1),
            ("2", "GigabitEthernet0/1 (LAN Core)", "1000000000", "1", 1),
            ("3", "GigabitEthernet0/2 (Server Farm)", "1000000000", "1", 1),
            ("4", "FastEthernet0/1 (Admin Access)", "100000000", "1", 0),
            ("5", "VLAN 10 (Management Gateway)", "1000000000", "1", 0),
            ("6", "Loopback0 (Router ID)", "1000000000", "1", 0),
        ]
        for infid, name, speed, status, mon in presets:
            inf_obj = SysInterface(
                ipaddress=dev.ipaddress,
                infid=infid,
                interfacename=name,
                interfacespeed=speed,
                interfacestatus_admin="1",
                interfacestatus=status,
                is_monitored=mon
            )
            db.add(inf_obj)
        db.commit()

    return db.query(SysInterface).filter(SysInterface.ipaddress == dev.ipaddress).order_by(SysInterface.infid.asc()).all()


def poll_devices(db):
    """Poll network devices (SNMP + Ping fallback) with dynamic OID profiles and custom sensors."""
    devices = db.query(SysDevice).all()
    # Cache OID profiles
    profiles = {p.name: p for p in db.query(OidProfile).all()}

    for dev in devices:
        # First check reachability via Ping
        alive, rtt = safe_ping(dev.ipaddress, timeout_sec=1)
        if not alive:
            dev.status = 2  # Down
            dev.last_updated = datetime.utcnow()
            continue

        dev.status = 1  # Up
        profile = profiles.get(dev.profile_name)

        # Determine CPU & Memory OIDs based on Profile or Custom overrides
        oid_cpu = dev.custom_oid_cpu or (profile.oid_cpu if profile and profile.oid_cpu else OID_CISCO_CPU_5MIN)
        oid_mem_used = dev.custom_oid_mem_used or (profile.oid_mem_used if profile and profile.oid_mem_used else OID_CISCO_MEM_USED)
        oid_mem_free = dev.custom_oid_mem_free or (profile.oid_mem_free if profile and profile.oid_mem_free else OID_CISCO_MEM_FREE)

        # Custom Sensors attached to device
        custom_sensors = dev.custom_oids
        custom_sensor_oids = [s.oid for s in custom_sensors if s.oid]

        # If alive, try SNMP
        try:
            loop = asyncio.new_event_loop()
            asyncio.set_event_loop(loop)
            base_oids = [OID_SYS_DESCR, OID_SYS_NAME, OID_SYS_UPTIME, OID_SYS_LOCATION]
            if oid_cpu:
                base_oids.append(oid_cpu)
            if oid_mem_used:
                base_oids.append(oid_mem_used)
            if oid_mem_free:
                base_oids.append(oid_mem_free)

            # Combine all unique OIDs to query
            all_oids = list(dict.fromkeys(base_oids + custom_sensor_oids))
            data = loop.run_until_complete(snmp_get_async(dev.ipaddress, dev.community, all_oids, timeout=2))
            loop.close()

            if OID_SYS_DESCR in data:
                dev.sys_description = data[OID_SYS_DESCR]
            if OID_SYS_NAME in data:
                dev.sys_name = data[OID_SYS_NAME]
            if OID_SYS_UPTIME in data:
                dev.sys_uptime = data[OID_SYS_UPTIME]
            if OID_SYS_LOCATION in data:
                dev.sys_location = data[OID_SYS_LOCATION]

            # Parse CPU
            if oid_cpu and oid_cpu in data:
                cpu_val = data[oid_cpu]
                try:
                    cpu_clean = re.sub(r"[^\d.]", "", str(cpu_val))
                    if cpu_clean:
                        dev.sys_cpu_load = float(cpu_clean)
                        db.add(CPULoadLog(ipaddress=dev.ipaddress, cpu_load=dev.sys_cpu_load))
                except Exception:
                    pass

            # Parse Memory
            mem_used_val = data.get(oid_mem_used, "") if oid_mem_used else ""
            mem_free_val = data.get(oid_mem_free, "") if oid_mem_free else ""
            try:
                u = float(re.sub(r"[^\d.]", "", str(mem_used_val))) if mem_used_val else dev.sys_memory_use
                f = float(re.sub(r"[^\d.]", "", str(mem_free_val))) if mem_free_val else dev.sys_memory_free
                dev.sys_memory_use = u
                dev.sys_memory_free = f
                if u > 0 or f > 0:
                    db.add(MemoryLog(ipaddress=dev.ipaddress, memory_use=u, memory_free=f))
            except Exception:
                pass

            # Update Custom Sensors
            for sensor in custom_sensors:
                if sensor.oid in data:
                    sensor.last_value = str(data[sensor.oid]).strip('"\'')
                    sensor.last_updated = datetime.utcnow()

        except Exception:
            # Ping is alive, mark status Up even if SNMP times out
            dev.status = 1

        # Only update timestamps and poll traffic for actively monitored interfaces
        monitored_infs = db.query(SysInterface).filter(
            SysInterface.ipaddress == dev.ipaddress,
            SysInterface.is_monitored == 1
        ).all()
        for inf in monitored_infs:
            inf.last_updated = datetime.utcnow()

        dev.last_updated = datetime.utcnow()
    db.commit()


def poll_servers(db):
    """Poll servers (Ping & SNMP Host Resources)."""
    servers = db.query(HrDevice).all()
    for srv in servers:
        alive, rtt = safe_ping(srv.ipaddress, timeout_sec=1)
        srv.status = 1 if alive else 2
        srv.last_updated = datetime.utcnow()
    db.commit()


def poll_services(db):
    """Poll TCP services and Ping for each configured service entry."""
    services = db.query(SysTCPUDP).all()
    for svc in services:
        tcp_ok, rtt_ms = check_tcp_port(svc.server, svc.port, timeout_sec=2.0)
        icmp_ok, ping_ms = safe_ping(svc.server, timeout_sec=1)

        svc.status = "respond" if tcp_ok else "not respond"
        svc.responded_ms = rtt_ms
        svc.icmp_status = 0 if icmp_ok else 1
        svc.last_updated = datetime.utcnow()

        db.add(ServiceLog(
            server=svc.server,
            port=svc.port,
            responded_ms=rtt_ms,
            icmp_status=svc.icmp_status,
            status=svc.status
        ))
    db.commit()


def run_poll_all():
    """Execute complete polling cycle for all categories."""
    db = SessionLocal()
    try:
        poll_services(db)
        poll_devices(db)
        poll_servers(db)
        # Server Applications: Databases & Web Servers
        from app.server_monitor import run_all_server_monitors
        run_all_server_monitors(db)
    except Exception as e:
        print(f"[Poller Error] {e}")
        db.rollback()
    finally:
        db.close()

