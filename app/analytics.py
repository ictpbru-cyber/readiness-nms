from typing import Dict, Any, List
from sqlalchemy.orm import Session
from app.models import SysDevice, HrDevice, SysTCPUDP, SysInterface, ServiceLog, CPULoadLog

def compute_network_performance(db: Session) -> Dict[str, Any]:
    """
    Compute comprehensive network performance analytics:
    - Latency & Jitter metrics
    - Packet loss & SLA availability
    - Interface bandwidth utilization & Top Talkers
    - Hardware resource strain (CPU/Memory)
    - Bottlenecks & Optimization insights
    """
    services = db.query(SysTCPUDP).all()
    devices = db.query(SysDevice).all()
    servers = db.query(HrDevice).all()
    interfaces = db.query(SysInterface).filter(SysInterface.is_monitored == 1).all()

    # 1. Latency & Responsiveness Analysis
    latencies = [s.responded_ms for s in services if s.status == "respond" and s.responded_ms > 0]
    avg_latency = round(sum(latencies) / len(latencies), 2) if latencies else 0.0
    min_latency = min(latencies) if latencies else 0.0
    max_latency = max(latencies) if latencies else 0.0
    # Jitter (standard latency variance)
    jitter = round((max_latency - min_latency) / 2, 2) if latencies else 0.0

    total_probes = len(services)
    failed_probes = sum(1 for s in services if s.status != "respond")
    packet_loss_pct = round((failed_probes / total_probes * 100), 1) if total_probes > 0 else 0.0

    # 2. Interface Traffic & Error Counters
    total_in_bytes = sum(inf.interfaceinput for inf in interfaces)
    total_out_bytes = sum(inf.interfaceoutput for inf in interfaces)
    total_errors = sum(inf.interfaceerror for inf in interfaces)

    total_in_mb = round(total_in_bytes / (1024 * 1024), 2)
    total_out_mb = round(total_out_bytes / (1024 * 1024), 2)

    # Top traffic interfaces
    sorted_interfaces = sorted(interfaces, key=lambda x: (x.interfaceinput + x.interfaceoutput), reverse=True)
    top_interfaces = [
        {
            "name": inf.interfacename or f"Port {inf.infid}",
            "device": inf.ipaddress,
            "speed": inf.interfacespeed,
            "in_mb": round(inf.interfaceinput / (1024 * 1024), 2),
            "out_mb": round(inf.interfaceoutput / (1024 * 1024), 2),
            "errors": int(inf.interfaceerror),
            "status": "Up" if inf.interfacestatus == "1" else "Down"
        }
        for inf in sorted_interfaces[:6]
    ]

    # 3. CPU & Memory Resource Strain
    cpu_loads = [d.sys_cpu_load for d in devices if d.status == 1]
    avg_cpu = round(sum(cpu_loads) / len(cpu_loads), 1) if cpu_loads else 0.0
    high_cpu_count = sum(1 for d in devices if d.sys_cpu_load >= 80)
    warning_cpu_count = sum(1 for d in devices if 50 <= d.sys_cpu_load < 80)
    normal_cpu_count = sum(1 for d in devices if d.sys_cpu_load < 50)

    # 4. Network Health & Quality Score (0 - 100)
    # Starts at 100, penalized by high latency, packet loss, high CPU, and interface errors
    health_score = 100
    if avg_latency > 150:
        health_score -= 20
    elif avg_latency > 60:
        health_score -= 10

    health_score -= int(packet_loss_pct * 1.2)
    health_score -= (high_cpu_count * 15)
    health_score -= (warning_cpu_count * 5)
    if total_errors > 0:
        health_score -= min(15, int(total_errors * 2))

    health_score = max(0, min(100, health_score))

    # 5. Bottleneck Identifications & Insights
    bottlenecks: List[Dict[str, str]] = []

    if packet_loss_pct > 0:
        bottlenecks.append({
            "severity": "CRITICAL" if packet_loss_pct > 20 else "WARNING",
            "component": "Service Availability",
            "metric": f"{packet_loss_pct}% Probe Failure",
            "impact": "End-users may experience service degradation or timeouts.",
            "recommendation": "Check DNS routing and firewall ACLs for unresponsive targets."
        })

    for d in devices:
        if d.sys_cpu_load >= 80:
            bottlenecks.append({
                "severity": "CRITICAL",
                "component": f"Device CPU: {d.sys_name} ({d.ipaddress})",
                "metric": f"{d.sys_cpu_load}% CPU Load",
                "impact": "Switch or router packet buffer exhaustion and dropped packets.",
                "recommendation": "Investigate high traffic flows, broadcast storms, or CPU-intensive processes."
            })

    if total_errors > 0:
        bottlenecks.append({
            "severity": "WARNING",
            "component": "Interface Packet Errors",
            "metric": f"{int(total_errors)} CRC / Packet Errors",
            "impact": "Retransmissions, duplex mismatches, or physical cabling faults.",
            "recommendation": "Inspect physical fiber/copper patch cords and verify speed/duplex negotiation."
        })

    if avg_latency > 100:
        bottlenecks.append({
            "severity": "WARNING",
            "component": "Latency Performance",
            "metric": f"{avg_latency} ms Avg RTT",
            "impact": "Noticeable latency delay on web and application requests.",
            "recommendation": "Review uplink saturation and hop routing."
        })

    if not bottlenecks:
        bottlenecks.append({
            "severity": "OPTIMAL",
            "component": "Network Health",
            "metric": "All Systems Normal",
            "impact": "Network latency and throughput operating within optimal thresholds.",
            "recommendation": "Continue periodic monitoring."
        })

    # 6. Chart datasets
    service_labels = [s.service_name or s.server for s in services]
    service_rtt = [s.responded_ms if s.status == "respond" else 0.0 for s in services]

    device_labels = [d.sys_name or d.ipaddress for d in devices]
    device_cpus = [d.sys_cpu_load for d in devices]
    device_mems = [d.sys_memory_use for d in devices]

    return {
        "score": health_score,
        "avg_latency": avg_latency,
        "min_latency": min_latency,
        "max_latency": max_latency,
        "jitter": jitter,
        "packet_loss_pct": packet_loss_pct,
        "total_in_mb": total_in_mb,
        "total_out_mb": total_out_mb,
        "total_errors": total_errors,
        "avg_cpu": avg_cpu,
        "high_cpu_count": high_cpu_count,
        "warning_cpu_count": warning_cpu_count,
        "normal_cpu_count": normal_cpu_count,
        "top_interfaces": top_interfaces,
        "bottlenecks": bottlenecks,
        "chart_services": {"labels": service_labels, "rtt": service_rtt},
        "chart_devices": {"labels": device_labels, "cpus": device_cpus, "mems": device_mems}
    }
