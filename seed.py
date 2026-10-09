import os
from app.database import engine, Base, SessionLocal
from app.models import Member, SysDevice, HrDevice, SysTCPUDP, SysInterface, CPULoadLog, MemoryLog

def seed_database():
    print("Initializing database tables...")
    Base.metadata.create_all(bind=engine)

    db = SessionLocal()
    try:
        # 1. Create Default Admin
        admin_user = db.query(Member).filter(Member.username == "admin").first()
        if not admin_user:
            admin = Member(
                username="admin",
                name="System Administrator",
                status="ADMIN"
            )
            admin.set_password("admin123")
            db.add(admin)
            print("Created default admin user: admin / admin123")

        # 2. Add Initial Sample Network Devices
        sample_devices = [
            {
                "ipaddress": "127.0.0.1",
                "community": "public",
                "sys_name": "Localhost-Gateway",
                "sys_description": "Local Host Loopback Interface / Gateway",
                "sys_oid": "1.3.6.1.4.1.9.1.1",
                "sys_ios": "Linux / Windows Host",
                "sys_location": "Data Center Rack 01",
                "sys_contact": "network-admin@pbru.ac.th",
                "status": 1,
                "sys_cpu_load": 12.5,
                "sys_memory_use": 4096.0,
                "sys_memory_free": 12288.0,
                "sys_uptime": "45 days, 12:34:56",
                "manufacturer": "Cisco Systems"
            },
            {
                "ipaddress": "8.8.8.8",
                "community": "public",
                "sys_name": "Core-DNS-Gateway",
                "sys_description": "Public Core Gateway Router",
                "sys_oid": "1.3.6.1.4.1.9.1.200",
                "sys_ios": "Cisco IOS 15.4(2)T",
                "sys_location": "Main Network Core",
                "sys_contact": "noc@pbru.ac.th",
                "status": 1,
                "sys_cpu_load": 28.0,
                "sys_memory_use": 8192.0,
                "sys_memory_free": 8192.0,
                "sys_uptime": "120 days, 04:10:00",
                "manufacturer": "Cisco Systems"
            }
        ]

        for dev_data in sample_devices:
            if not db.query(SysDevice).filter(SysDevice.ipaddress == dev_data["ipaddress"]).first():
                dev = SysDevice(**dev_data)
                db.add(dev)
                print(f"Added device: {dev.ipaddress} ({dev.sys_name})")

                # Add sample interfaces for the device
                inf1 = SysInterface(
                    ipaddress=dev.ipaddress,
                    infid="1",
                    interfacename="GigabitEthernet0/0 (Uplink)",
                    interfacespeed="1000000000",
                    interfacestatus_admin="1",
                    interfacestatus="1",
                    interfaceinput=45892300.0,
                    interfaceoutput=98234100.0,
                    interfaceerror=0.0
                )
                inf2 = SysInterface(
                    ipaddress=dev.ipaddress,
                    infid="2",
                    interfacename="GigabitEthernet0/1 (LAN Core)",
                    interfacespeed="1000000000",
                    interfacestatus_admin="1",
                    interfacestatus="1",
                    interfaceinput=128923000.0,
                    interfaceoutput=148234100.0,
                    interfaceerror=2.0
                )
                db.add_all([inf1, inf2])

                # Sample historical logs
                db.add_all([
                    CPULoadLog(ipaddress=dev.ipaddress, cpu_load=15.0),
                    CPULoadLog(ipaddress=dev.ipaddress, cpu_load=22.0),
                    CPULoadLog(ipaddress=dev.ipaddress, cpu_load=dev.sys_cpu_load),
                    MemoryLog(ipaddress=dev.ipaddress, memory_use=4000.0, memory_free=12000.0),
                    MemoryLog(ipaddress=dev.ipaddress, memory_use=dev.sys_memory_use, memory_free=dev.sys_memory_free),
                ])

        # 3. Add Monitored Servers
        sample_servers = [
            {
                "ipaddress": "127.0.0.1",
                "community": "public",
                "sys_name": "Main-Application-Server",
                "sys_description": "Ubuntu Linux NMS Application Host",
                "status": 1,
                "cpu_load": 18.2,
                "storage_size_mb": 512000.0,
                "storage_used_mb": 145000.0,
                "storage_desc": "/dev/sda1 (Root Partition)",
                "sys_uptime": "30 days, 10:20:00"
            }
        ]

        for srv_data in sample_servers:
            if not db.query(HrDevice).filter(HrDevice.ipaddress == srv_data["ipaddress"]).first():
                srv = HrDevice(**srv_data)
                db.add(srv)
                print(f"Added server: {srv.ipaddress} ({srv.sys_name})")

        # 4. Add Monitored Services
        sample_services = [
            {"service_name": "Google Web (HTTP)", "server": "www.google.com", "port": 80},
            {"service_name": "Google DNS (Port 53)", "server": "8.8.8.8", "port": 53},
            {"service_name": "Cloudflare DNS", "server": "1.1.1.1", "port": 53},
            {"service_name": "Localhost Web Port", "server": "127.0.0.1", "port": 8000},
        ]

        for svc_data in sample_services:
            if not db.query(SysTCPUDP).filter(SysTCPUDP.server == svc_data["server"], SysTCPUDP.port == svc_data["port"]).first():
                svc = SysTCPUDP(**svc_data, status="not respond")
                db.add(svc)
                print(f"Added service: {svc.service_name} ({svc.server}:{svc.port})")

        # 5. Add Monitored Databases (Oracle, MySQL, PostgreSQL, Redis, MSSQL)
        from app.models import DatabaseTarget, WebTarget
        sample_dbs = [
            {
                "name": "Production Core MySQL DB",
                "db_type": "MySQL",
                "host": "127.0.0.1",
                "port": 3306,
                "details": "Main Application & Student Registry Database"
            },
            {
                "name": "Enterprise ERP Oracle DB",
                "db_type": "Oracle",
                "host": "192.168.1.50",
                "port": 1521,
                "details": "Oracle 19c Enterprise Edition TNS Listener"
            },
            {
                "name": "Analytics PostgreSQL Cluster",
                "db_type": "PostgreSQL",
                "host": "127.0.0.1",
                "port": 5432,
                "details": "PostgreSQL TimescaleDB Analytics Engine"
            },
            {
                "name": "Session Cache Redis Node",
                "db_type": "Redis",
                "host": "127.0.0.1",
                "port": 6379,
                "details": "Redis In-Memory Key-Value & Session Cache"
            }
        ]

        for db_item in sample_dbs:
            if not db.query(DatabaseTarget).filter(DatabaseTarget.host == db_item["host"], DatabaseTarget.port == db_item["port"]).first():
                db_target = DatabaseTarget(**db_item, status="ONLINE", latency_ms=1.5, version_info="Ready")
                db.add(db_target)
                print(f"Added database target: {db_target.name} ({db_target.db_type} on {db_target.host}:{db_target.port})")

        # 6. Add Monitored Web Servers
        sample_webs = [
            {
                "name": "PBRU Main Portal",
                "url": "https://www.pbru.ac.th",
            },
            {
                "name": "Google Gateway Health Check",
                "url": "https://www.google.com",
            },
            {
                "name": "Localhost Readiness Web App",
                "url": "http://127.0.0.1:8000",
            }
        ]

        for web_item in sample_webs:
            if not db.query(WebTarget).filter(WebTarget.url == web_item["url"]).first():
                web_target = WebTarget(**web_item, status_code=200, latency_ms=45.0, health_status="EXCELLENT", server_software="nginx/1.24")
                db.add(web_target)
                print(f"Added web target: {web_target.name} ({web_target.url})")

        # 7. Add Standard Vendor OID Profiles
        from app.models import OidProfile, DeviceCustomOid
        sample_profiles = [
            {
                "vendor": "Cisco",
                "name": "Cisco IOS Switch/Router Profile",
                "oid_cpu": "1.3.6.1.4.1.9.9.109.1.1.1.1.8.1",
                "oid_mem_used": "1.3.6.1.4.1.9.9.48.1.1.1.5.1",
                "oid_mem_free": "1.3.6.1.4.1.9.9.48.1.1.1.6.1",
                "description": "Standard Cisco IOS / IOS-XE Enterprise Switches and Routers"
            },
            {
                "vendor": "MikroTik",
                "name": "MikroTik RouterOS Profile",
                "oid_cpu": "1.3.6.1.4.1.14988.1.1.1.3.1.0",
                "oid_mem_used": "1.3.6.1.2.1.25.2.3.1.6.65536",
                "oid_mem_free": "1.3.6.1.2.1.25.2.3.1.5.65536",
                "description": "MikroTik Cloud Router Switch (CRS) & RouterOS (CCR/RB Series)"
            },
            {
                "vendor": "Huawei",
                "name": "Huawei VRP Core Switch Profile",
                "oid_cpu": "1.3.6.1.4.1.2011.5.25.31.1.1.1.1.5",
                "oid_mem_used": "1.3.6.1.4.1.2011.5.25.31.1.1.1.1.7",
                "oid_mem_free": "1.3.6.1.2.1.25.2.3.1.5.1",
                "description": "Huawei Quidway, CloudEngine & VRP Switch Infrastructure"
            },
            {
                "vendor": "Linux",
                "name": "Linux Net-SNMP Host Profile",
                "oid_cpu": "1.3.6.1.4.1.2021.10.1.3.1",
                "oid_mem_used": "1.3.6.1.4.1.2021.4.6.0",
                "oid_mem_free": "1.3.6.1.4.1.2021.4.11.0",
                "description": "Linux Net-SNMP Daemon (Ubuntu, Debian, RHEL, CentOS)"
            },
            {
                "vendor": "Fortinet",
                "name": "Fortinet FortiGate Firewall Profile",
                "oid_cpu": "1.3.6.1.4.1.12356.101.4.1.1.0",
                "oid_mem_used": "1.3.6.1.4.1.12356.101.4.1.4.0",
                "oid_mem_free": "1.3.6.1.4.1.12356.101.4.1.5.0",
                "description": "Fortinet FortiGate Next-Gen Firewall (NGFW)"
            },
            {
                "vendor": "Windows",
                "name": "Windows Server Host Resources Profile",
                "oid_cpu": "1.3.6.1.2.1.25.3.3.1.2.1",
                "oid_mem_used": "1.3.6.1.2.1.25.2.3.1.6.1",
                "oid_mem_free": "1.3.6.1.2.1.25.2.3.1.5.1",
                "description": "Microsoft Windows Server SNMP Service"
            },
            {
                "vendor": "Generic",
                "name": "Generic MIB-II / RFC-1213 Device",
                "oid_cpu": "1.3.6.1.2.1.25.3.3.1.2",
                "oid_mem_used": "1.3.6.1.2.1.25.2.3.1.6",
                "oid_mem_free": "1.3.6.1.2.1.25.2.3.1.5",
                "description": "Standard RFC 1213 / Host Resources MIB Generic Device"
            }
        ]

        for p_item in sample_profiles:
            if not db.query(OidProfile).filter(OidProfile.name == p_item["name"]).first():
                prof = OidProfile(**p_item)
                db.add(prof)
                print(f"Added OID Profile: {prof.name} ({prof.vendor})")

        # 8. Sample Custom OID Sensors for Localhost Gateway
        sample_sensors = [
            {
                "ipaddress": "127.0.0.1",
                "name": "Chassis Core Temperature",
                "oid": "1.3.6.1.4.1.9.9.13.1.3.1.3.1",
                "unit": "°C",
                "last_value": "38.5"
            },
            {
                "ipaddress": "127.0.0.1",
                "name": "Cooling Fan RPM",
                "oid": "1.3.6.1.4.1.9.9.13.1.4.1.3.1",
                "unit": "RPM",
                "last_value": "4200"
            },
            {
                "ipaddress": "8.8.8.8",
                "name": "Active Gateway BGP Peers",
                "oid": "1.3.6.1.2.1.15.3.1.2",
                "unit": "Peers",
                "last_value": "4"
            }
        ]

        for s_item in sample_sensors:
            if not db.query(DeviceCustomOid).filter(DeviceCustomOid.ipaddress == s_item["ipaddress"], DeviceCustomOid.name == s_item["name"]).first():
                sensor = DeviceCustomOid(**s_item)
                db.add(sensor)
                print(f"Added custom OID sensor: {sensor.name} on {sensor.ipaddress}")

        db.commit()
        print("Database seeded successfully with MIB/OID profiles and custom sensors!")
    except Exception as e:
        db.rollback()
        print(f"Error seeding database: {e}")
    finally:
        db.close()

if __name__ == "__main__":
    seed_database()
