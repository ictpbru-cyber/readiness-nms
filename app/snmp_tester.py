import time
import asyncio
from typing import Dict, Any, List
from app.poller import is_valid_host

async def run_snmp_get(host: str, community: str, port: int, oid: str, timeout: float = 2.0) -> Dict[str, Any]:
    """Execute single SNMP GET query asynchronously."""
    t_start = time.perf_counter()
    try:
        from pysnmp.hlapi.asyncio import (
            SnmpEngine, CommunityData, UdpTransportTarget,
            ContextData, ObjectType, ObjectIdentity, getCmd
        )
        snmpEngine = SnmpEngine()
        target = UdpTransportTarget((host, port), timeout=timeout, retries=1)
        errorIndication, errorStatus, errorIndex, responseBinds = await getCmd(
            snmpEngine,
            CommunityData(community),
            target,
            ContextData(),
            ObjectType(ObjectIdentity(oid))
        )
        elapsed_ms = round((time.perf_counter() - t_start) * 1000.0, 2)

        if errorIndication:
            return {"success": False, "error": str(errorIndication), "elapsed_ms": elapsed_ms, "results": []}
        if errorStatus:
            err = f"{errorStatus.prettyPrint()} at {errorIndex and responseBinds[int(errorIndex) - 1][0] or '?'}"
            return {"success": False, "error": err, "elapsed_ms": elapsed_ms, "results": []}

        results = []
        for var in responseBinds:
            name, val = var
            results.append({
                "oid": str(name),
                "type": val.__class__.__name__,
                "value": str(val)
            })

        return {
            "success": True,
            "elapsed_ms": elapsed_ms,
            "results": results
        }
    except Exception as e:
        elapsed_ms = round((time.perf_counter() - t_start) * 1000.0, 2)
        return {"success": False, "error": str(e), "elapsed_ms": elapsed_ms, "results": []}


async def run_snmp_walk(host: str, community: str, port: int, root_oid: str, max_rows: int = 30, timeout: float = 2.0) -> Dict[str, Any]:
    """Execute SNMP WALK / NEXT query asynchronously up to max_rows."""
    t_start = time.perf_counter()
    results: List[Dict[str, str]] = []
    try:
        from pysnmp.hlapi.asyncio import (
            SnmpEngine, CommunityData, UdpTransportTarget,
            ContextData, ObjectType, ObjectIdentity, nextCmd
        )
        snmpEngine = SnmpEngine()
        target = UdpTransportTarget((host, port), timeout=timeout, retries=1)
        clean_root = root_oid.strip().lstrip(".")
        curr_oid = ObjectIdentity(clean_root)

        for _ in range(max_rows):
            errorIndication, errorStatus, errorIndex, varBinds = await nextCmd(
                snmpEngine,
                CommunityData(community),
                target,
                ContextData(),
                ObjectType(curr_oid)
            )
            if errorIndication:
                if not results:
                    elapsed_ms = round((time.perf_counter() - t_start) * 1000.0, 2)
                    return {"success": False, "error": str(errorIndication), "elapsed_ms": elapsed_ms, "results": []}
                break
            if errorStatus:
                break
            if not varBinds:
                break

            var = varBinds[0]
            oid_str = str(var[0]).lstrip(".")
            if not oid_str.startswith(clean_root):
                break

            results.append({
                "oid": str(var[0]),
                "type": var[1].__class__.__name__,
                "value": str(var[1])
            })
            curr_oid = var[0]

        elapsed_ms = round((time.perf_counter() - t_start) * 1000.0, 2)
        return {
            "success": True,
            "elapsed_ms": elapsed_ms,
            "results": results
        }
    except Exception as e:
        elapsed_ms = round((time.perf_counter() - t_start) * 1000.0, 2)
        return {"success": False, "error": str(e), "elapsed_ms": elapsed_ms, "results": results}


def probe_snmp(host: str, community: str = "public", port: int = 161, oid: str = "1.3.6.1.2.1.1.1.0", mode: str = "get", max_rows: int = 30) -> Dict[str, Any]:
    """Synchronous entry point that runs the async probe in an event loop."""
    if not is_valid_host(host):
        return {"success": False, "error": "Invalid host or IP address format.", "elapsed_ms": 0, "results": []}
    
    clean_oid = oid.strip()
    if not clean_oid:
        return {"success": False, "error": "OID cannot be empty.", "elapsed_ms": 0, "results": []}

    loop = asyncio.new_event_loop()
    asyncio.set_event_loop(loop)
    try:
        if mode == "walk":
            res = loop.run_until_complete(run_snmp_walk(host, community, port, clean_oid, max_rows=max_rows))
        else:
            res = loop.run_until_complete(run_snmp_get(host, community, port, clean_oid))
        return res
    finally:
        loop.close()
