from datetime import datetime
from sqlalchemy import Column, Integer, String, Float, DateTime, ForeignKey, Text
from sqlalchemy.orm import relationship
import bcrypt
from app.database import Base

class Member(Base):
    __tablename__ = "members"

    id = Column(Integer, primary_key=True, index=True)
    username = Column(String(50), unique=True, nullable=False, index=True)
    password_hash = Column(String(255), nullable=False)
    name = Column(String(100), nullable=True)
    status = Column(String(20), default="USER")  # "ADMIN" or "USER"
    created_at = Column(DateTime, default=datetime.utcnow)

    def set_password(self, plain_password: str):
        salt = bcrypt.gensalt()
        self.password_hash = bcrypt.hashpw(plain_password.encode("utf-8"), salt).decode("utf-8")

    def check_password(self, plain_password: str) -> bool:
        if not self.password_hash:
            return False
        try:
            return bcrypt.checkpw(plain_password.encode("utf-8"), self.password_hash.encode("utf-8"))
        except Exception:
            return False


class Manufacturer(Base):
    __tablename__ = "manufacturers"

    id = Column(Integer, primary_key=True, index=True)
    hr_oid = Column(String(100), unique=True, index=True)
    hr_name = Column(String(100), nullable=False)


class SysDevice(Base):
    __tablename__ = "sys_devices"

    ipaddress = Column(String(45), primary_key=True, index=True)
    community = Column(String(100), default="public")
    sys_name = Column(String(150), default="")
    sys_description = Column(Text, default="")
    sys_oid = Column(String(100), default="")
    sys_ios = Column(String(100), default="")
    sys_location = Column(String(150), default="")
    sys_contact = Column(String(150), default="")
    status = Column(Integer, default=2)  # 1 = Up, 2 = Down
    sys_cpu_load = Column(Float, default=0.0)
    sys_memory_use = Column(Float, default=0.0)
    sys_memory_free = Column(Float, default=0.0)
    sys_uptime = Column(String(100), default="")
    manufacturer = Column(String(100), default="Unknown")
    profile_name = Column(String(100), default="Cisco Systems")
    custom_oid_cpu = Column(String(150), default="")
    custom_oid_mem_used = Column(String(150), default="")
    custom_oid_mem_free = Column(String(150), default="")
    last_updated = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    interfaces = relationship("SysInterface", back_populates="device", cascade="all, delete-orphan")
    custom_oids = relationship("DeviceCustomOid", back_populates="device", cascade="all, delete-orphan")


class OidProfile(Base):
    __tablename__ = "oid_profiles"

    id = Column(Integer, primary_key=True, index=True)
    vendor = Column(String(50), nullable=False)  # Cisco, MikroTik, Huawei, Linux, Fortinet, Windows, etc.
    name = Column(String(100), nullable=False)
    oid_cpu = Column(String(150), default="")
    oid_mem_used = Column(String(150), default="")
    oid_mem_free = Column(String(150), default="")
    description = Column(String(255), default="")


class DeviceCustomOid(Base):
    __tablename__ = "device_custom_oids"

    id = Column(Integer, primary_key=True, index=True)
    ipaddress = Column(String(45), ForeignKey("sys_devices.ipaddress"), nullable=False, index=True)
    name = Column(String(100), nullable=False)  # e.g. "Chassis Temp", "Fan Speed", "Active PPPoE"
    oid = Column(String(150), nullable=False)
    unit = Column(String(20), default="")       # e.g. "°C", "RPM", "%", "Users"
    last_value = Column(String(100), default="")
    last_updated = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    device = relationship("SysDevice", back_populates="custom_oids")


class HrDevice(Base):
    __tablename__ = "hr_devices"

    ipaddress = Column(String(45), primary_key=True, index=True)
    community = Column(String(100), default="public")
    sys_name = Column(String(150), default="")
    sys_description = Column(Text, default="")
    status = Column(Integer, default=2)  # 1 = Up, 2 = Down
    cpu_load = Column(Float, default=0.0)
    storage_size_mb = Column(Float, default=0.0)
    storage_used_mb = Column(Float, default=0.0)
    storage_desc = Column(String(150), default="")
    sys_uptime = Column(String(100), default="")
    last_updated = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)


class SysTCPUDP(Base):
    __tablename__ = "sys_tcp_udp"

    id = Column(Integer, primary_key=True, index=True)
    server = Column(String(150), nullable=False, index=True)
    port = Column(Integer, nullable=False)
    service_name = Column(String(100), default="")
    responded_ms = Column(Float, default=0.0)
    icmp_status = Column(Integer, default=1)  # 0 = alive, 1 = error/dead
    icmp6_status = Column(Integer, default=1)
    status = Column(String(20), default="not respond")  # "respond" or "not respond"
    last_updated = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)


class SysInterface(Base):
    __tablename__ = "sys_interfaces"

    id = Column(Integer, primary_key=True, index=True)
    ipaddress = Column(String(45), ForeignKey("sys_devices.ipaddress"), nullable=False, index=True)
    infid = Column(String(50), nullable=False)
    interfacename = Column(String(100), default="")
    interfacespeed = Column(String(50), default="0")
    interfacestatus_admin = Column(String(20), default="2")  # 1 = up, 2 = down
    interfacestatus = Column(String(20), default="2")        # 1 = up, 2 = down
    interfaceinput = Column(Float, default=0.0)
    interfaceoutput = Column(Float, default=0.0)
    interfaceerror = Column(Float, default=0.0)
    is_monitored = Column(Integer, default=1)  # 1 = active monitoring, 0 = disabled / unmonitored
    last_updated = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    device = relationship("SysDevice", back_populates="interfaces")


class CPULoadLog(Base):
    __tablename__ = "cpm_cpu_load_log"

    id = Column(Integer, primary_key=True, index=True)
    ipaddress = Column(String(45), index=True)
    cpu_load = Column(Float, default=0.0)
    timestamp = Column(DateTime, default=datetime.utcnow, index=True)


class MemoryLog(Base):
    __tablename__ = "cpm_memory_log"

    id = Column(Integer, primary_key=True, index=True)
    ipaddress = Column(String(45), index=True)
    memory_use = Column(Float, default=0.0)
    memory_free = Column(Float, default=0.0)
    timestamp = Column(DateTime, default=datetime.utcnow, index=True)


class ServiceLog(Base):
    __tablename__ = "service_tcp_udp_log"

    id = Column(Integer, primary_key=True, index=True)
    server = Column(String(150), index=True)
    port = Column(Integer)
    responded_ms = Column(Float, default=0.0)
    icmp_status = Column(Integer, default=1)
    status = Column(String(20), default="not respond")
    timestamp = Column(DateTime, default=datetime.utcnow, index=True)


class DatabaseTarget(Base):
    __tablename__ = "database_targets"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(100), nullable=False)
    db_type = Column(String(50), nullable=False)  # Oracle, MySQL, PostgreSQL, MSSQL, Redis, MongoDB, Other
    host = Column(String(150), nullable=False)
    port = Column(Integer, nullable=False)
    status = Column(String(20), default="OFFLINE")  # ONLINE, DEGRADED, OFFLINE
    latency_ms = Column(Float, default=0.0)
    version_info = Column(String(200), default="")
    details = Column(String(255), default="")
    last_checked = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)


class WebTarget(Base):
    __tablename__ = "web_targets"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(100), nullable=False)
    url = Column(String(255), nullable=False)
    status_code = Column(Integer, default=0)
    latency_ms = Column(Float, default=0.0)
    server_software = Column(String(100), default="Unknown")
    ssl_days_left = Column(Integer, default=-1)  # -1 if HTTP or no cert
    ssl_issuer = Column(String(150), default="")
    health_status = Column(String(20), default="CRITICAL")  # EXCELLENT, GOOD, WARNING, CRITICAL
    security_headers_count = Column(Integer, default=0)
    last_checked = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

