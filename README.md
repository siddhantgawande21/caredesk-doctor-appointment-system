# CareDesk — Doctor Appointment & Patient Consultation Management System

A full-stack healthcare scheduling web application built with PHP, MySQL, HTML5, CSS3, and JavaScript. Engineered for portfolio showcase, demonstrating secure session handling, 3-tier role-based access control, normalized relational database architecture, and defensive backend programming using PDO prepared statements.

---

## Key Features

1. **Role-Based Access Control (RBAC):**
   - **Patients:** Register, log in, browse specialists across departments, book consultation slots, monitor status lifecycle, and cancel bookings.
   - **Doctors:** Dedicated personal portal to review incoming patient queues, inspect recorded symptoms, and update appointment statuses (`Confirmed`, `Completed`, `Cancelled`).
   - **Administrators:** Comprehensive management console with system-wide KPI metrics, full appointment schedules, and doctor-patient directory management.

2. **Security & Defensive Programming:**
   - 100% prepared statements via PHP Data Objects (PDO) to protect against SQL Injection.
   - Password encryption using native Bcrypt algorithms (`password_hash` / `password_verify`).
   - Session fixation defense via strict session ID regeneration (`session_regenerate_id`).
   - Output sanitization with `htmlspecialchars()` to eliminate Cross-Site Scripting (XSS).
   - Server-side validation preventing overlapping doctor schedule collisions.

3. **Responsive Architecture:**
   - Pure CSS3 Grid and Flexbox styling structured for mobile, tablet, and desktop viewports without external framework dependencies.

---

## Tech Stack

- **Backend:** PHP 8.x (Compatible with PHP 7.4+)
- **Database:** MySQL 8.x / MariaDB
- **Frontend:** Semantic HTML5, Modern CSS3, Vanilla ES6 JavaScript
- **Data Access:** PHP Data Objects (PDO) with strict error handling

---

## Pre-Configured Test Credentials

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@caredesk.com` | `password` | System-wide admin metrics and schedule controls |
| **Doctor (Cardiology)** | `aisha@caredesk.com` | `password` | Individual consultation queue & status controls |
| **Doctor (Dermatology)** | `vikram@caredesk.com` | `password` | Individual consultation queue & status controls |
| **Patient** | `patient@caredesk.com` | `password` | Patient appointment booking & history dashboard |

---

## Deployment & Production Hosting Guide

This application utilizes relative paths and standard environment patterns, allowing it to be deployed directly to cloud hosting providers, shared hosting (cPanel/DirectAdmin), or Linux VPS instances (Apache/Nginx).

### 1. File Deployment
Upload the project source code to your web server root directory (e.g., `public_html/` or `/var/www/html/`).

### 2. Database Provisioning
1. Create a MySQL database and assign a database user with full privileges.
2. Import `database/schema.sql` via your hosting control panel phpMyAdmin or CLI:
   ```bash
   mysql -u <db_user> -p <db_name> < database/schema.sql