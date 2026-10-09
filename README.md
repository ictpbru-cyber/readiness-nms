# Readiness 2.0 - Network & System Monitoring Suite (Python Edition)

ระบบตรวจสอบสถานะเครือข่าย เซิร์ฟเวอร์ และเซอร์วิส (Network & System Monitoring System: NMS) ที่ได้รับการพัฒนาใหม่จากระบบ PHP เดิม มาเป็นสถาปัตยกรรม **Python (FastAPI + SQLAlchemy + APScheduler)** เต็มรูปแบบ พร้อมระบบความปลอดภัยระดับสากล

---

## 📁 โครงสร้างโปรเจกต์ (Project Structure)

```text
readiness/
├── Old_PHP/                 # โฟลเดอร์สำรองโค้ด PHP เดิมทั้งหมด (แยกเก็บอย่างปลอดภัย)
│   ├── admin/
│   ├── charts/
│   ├── index.php
│   ├── config.inc.php
│   └── ...
├── app/
│   ├── config.py            # การตั้งค่าระบบและการอ่าน .env
│   ├── database.py          # SQLAlchemy Engine & Session Factory
│   ├── models.py            # Database Models (ORM) & Bcrypt
│   ├── auth.py              # ระบบ Session Authentication & Security
│   ├── poller.py            # โมดูล Probe: Safe Ping, TCP Port & SNMP
│   ├── routes/
│   │   ├── web.py           # หน้าเว็บหลัก (Dashboard, Network, Servers, Services)
│   │   ├── admin.py         # หน้า Admin CRUD (Devices, Servers, Services, Users)
│   │   └── api.py           # REST API สำหรับ AJAX & Webhook
│   ├── templates/           # หน้าเว็บ Jinja2 + Bootstrap 5
│   └── static/              # ไฟล์ CSS และ JavaScript
├── main.py                  # FastAPI Application & Background Scheduler Entrypoint
├── run.py                   # สคริปต์สั่งรันระบบ
├── seed.py                  # สคริปต์สร้างฐานข้อมูลและเพิ่มข้อมูลเริ่มต้น
├── requirements.txt         # รายการแพ็กเกจที่จำเป็น
└── .env                     # ไฟล์ Configuration (Database URL, Secret Key)
```

---

## 🔒 การปรับปรุงความปลอดภัย (Security Hardening)

| รายการ | โค้ดเดิม (PHP) | ระบบใหม่ (Python) |
|---|---|---|
| **SQL Injection** | มีการต่อสตริง SQL โดยตรงในทุกฟอร์ม | ใช้ **SQLAlchemy ORM (Parameterized Queries)** 100% ป้องกัน SQLi ถาวร |
| **Password Storage** | บันทึกรหัสผ่านเป็นตัวอักษรธรรมดา (Plaintext) | เข้ารหัสด้วย **Bcrypt + Salt** ตามมาตรฐานความปลอดภัย |
| **Command Injection** | ใช้ `exec("ping $host ...")` เสี่ยง OS Command Injection | ใช้ **Subprocess Array Arguments (shell=False)** พร้อมตรวจสอบ IP/Host Regex |
| **Cross-Site Scripting (XSS)** | `echo $_SERVER[...]` และพิมพ์ตัวแปรตรงๆ | ใช้ **Jinja2 Auto-Escaping** ทุกหน้าแสดงผล |
| **CSRF & Session** | ไม่มี CSRF Token และไม่มีการป้องกัน Session Fixation | ใช้ **Signed HttpOnly & SameSite Session Cookies** |
| **Credentials รั่วไหล** | Hardcode รหัสผ่านฐานข้อมูลในไฟล์ PHP | แยกการตั้งค่าและรหัสผ่านไว้ใน **`.env`** |

---

## 🚀 วิธีการติดตั้งและเริ่มต้นใช้งาน (Getting Started)

### 1. ติดตั้ง Dependencies (หากยังไม่มี)
```bash
pip install -r requirements.txt
```

### 2. สร้างฐานข้อมูลและสร้างข้อมูลผู้ใช้เริ่มต้น
```bash
python seed.py
```
> **บัญชีผู้ดูแลระบบเริ่มต้น (Default Admin):**
> * **Username:** `admin`
> * **Password:** `admin123`

### 3. รันระบบ (Start Application)
```bash
python run.py
```
เปิดเว็บบราวเซอร์ไปที่: **`http://127.0.0.1:8000`**

## 📦 การติดตั้งผ่านแพ็กเกจ (Package Installation & Deployment)

### ตัวเลือกที่ 1: ติดตั้งผ่าน Python Wheel Package
สามารถนำไฟล์แพ็กเกจที่คอมไพล์แล้วไปติดตั้งในเครื่องปลายทางได้ทันทีโดยไม่ต้องคอมไพล์ใหม่:
```bash
# ติดตั้งแพ็กเกจ Wheel
pip install dist/readiness_nms-2.0.0-py3-none-any.whl

# สั่งรันผ่าน Console Script ได้ทันที
readiness-nms
```

### ตัวเลือกที่ 2: รันผ่าน Docker & Docker Compose
```bash
# รันผ่าน docker-compose คำสั่งเดียว
docker-compose up -d

# ดูบันทึกการทำงาน
docker-compose logs -f
```

---

## 🌐 การเผยแพร่ขึ้น GitHub (Publish to GitHub)

หากต้องการนำโปรเจกต์นี้ขึ้น GitHub Repository ส่วนตัวหรือองค์กร สามารถทำได้ตามขั้นตอนดังนี้:

```bash
# 1. เริ่มต้น Git Repository
git init

# 2. เพิ่มไฟล์ทั้งหมดเข้า Staging (ไฟล์ .env, .db, แคช จะถูกกันออกอัตโนมัติตาม .gitignore)
git add .

# 3. Commit โค้ดทั้งหมด
git commit -m "feat: Readiness NMS 2.0 release with responsive UI, security scanner & multi-vendor telemetry"

# 4. เปลี่ยนชื่อ Branch หลักเป็น main
git branch -M main

# 5. เชื่อมต่อกับ Remote Repository บน GitHub ของคุณ (แทนที่ <username> ด้วยชื่อบัญชี GitHub)
git remote add origin https://github.com/<your-username>/readiness-nms.git

# 6. Push โค้ดขึ้นสู่ GitHub
git push -u origin main
```
