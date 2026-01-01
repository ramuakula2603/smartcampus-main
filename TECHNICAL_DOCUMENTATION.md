---
# SmartCampus Main – Technical Documentation

## Table of Contents
1. Project Overview
2. Technology Stack
3. Local Setup Guide
4. Cloud Deployment Guide
5. Modules & Features
6. Application Flows
7. Codebase Structure & Conventions
8. Troubleshooting & FAQ
9. Onboarding Checklist

---

## Project Overview
SmartCampus is a modular, extensible educational management system built with CodeIgniter (PHP MVC). It supports multi-institution deployments (colleges, schools) and provides features for administration, communication, document generation, and more.

## Technology Stack
- **Backend:** PHP (CodeIgniter framework)
- **Frontend:** HTML, CSS, JavaScript, jQuery
- **Database:** MySQL/MariaDB
- **Web Server:** Apache (XAMPP for local, LAMP for cloud)
- **PDF Generation:** mPDF library
- **File Storage:** Local filesystem (uploads/)

## Local Setup Guide
1. **Install XAMPP** (https://www.apachefriends.org/)
2. **Copy Codebase:** Place the project in `C:/xampp/htdocs/amt`.
3. **Start Services:** Launch Apache and MySQL from XAMPP Control Panel.
4. **Database Setup:**
   - Open `http://localhost/phpmyadmin`.
   - Create a database (e.g., `amt`).
   - Import SQL files: `amt_table_structure.sql`, `QUICK_INSTALL.sql`, etc.
5. **Configure Database:**
   - Edit `application/config/database.php` with your DB credentials.
6. **Set Permissions:** Ensure `uploads/` and subfolders are writable.
7. **Access App:** Go to `http://localhost/amt` in your browser.

## Cloud Deployment Guide (Contabo or Similar)
1. **Provision VPS:** Deploy Ubuntu server on Contabo.
2. **Install LAMP Stack:**
   - `sudo apt install apache2 php libapache2-mod-php php-mysql mysql-server`
3. **Upload Codebase:** Use SFTP/Git to `/var/www/html/amt`.
4. **Database:** Create DB and import SQL as above.
5. **Configure Apache Virtual Host:** Point domain/subdomain to app folder.
6. **Set Permissions:** `sudo chown -R www-data:www-data /var/www/html/amt/uploads`
7. **Configure DB:** Edit `application/config/database.php`.
8. **Secure Server:** Set up firewall, SSL, and regular updates.

## Modules & Features
- **Admin Panel:** User management, settings, content, reports.
- **Chat System:** Real-time chat, message persistence, AI integration, PDF export.
- **Generate Paper:** Create, save, and export question papers as PDFs.
- **Student/Staff Management:** CRUD for students, staff, attendance, and more.
- **Document Management:** Uploads, certificates, ID cards, hall tickets.
- **Reports:** Attendance, results, payments, and more.
- **Media Management:** Gallery, uploads, and file storage.

## Application Flows
### Example: Chat to PDF
1. User chats in the UI (`application/views/user/chat/index.php`).
2. Messages are saved via AJAX to `admin/Generatepaper/save_message`.
3. To export, user selects messages and clicks 'Generate PDF'.
4. Selected messages are sent to `admin/Generatepaper/generate_pdf`.
5. Controller renders PDF using mPDF and returns it (base64 for embed or download).

### Example: Admin Content Management
1. Admin logs in via dashboard.
2. Navigates to content section (controller in `application/controllers/admin`).
3. Performs CRUD operations, which update the database and reflect in the UI.

## Codebase Structure & Conventions
- **Controllers:** `application/controllers/` (admin controllers in `admin/` subfolder)
- **Models:** `application/models/`
- **Views:** `application/views/`
- **Libraries:** `application/libraries/`, `application/third_party/`
- **Uploads:** `uploads/` (media, documents, etc.)
- **Assets:** `assets/` (CSS, JS, images)
- **Config:** `application/config/`
- **Naming:** Use lowercase, underscores for files; PascalCase for classes.
- **AJAX:** Endpoints return JSON; use `embed=true` for PDF base64 responses.

## Troubleshooting & FAQ
- **Blank Page/Error:** Check `application/logs/` and PHP error logs.
- **PDF Not Generating:** Ensure mPDF is installed and writable, check logo paths.
- **Uploads Fail:** Check permissions on `uploads/`.
- **DB Errors:** Verify credentials in `database.php` and DB import status.
- **AJAX Issues:** Use browser DevTools to inspect network requests.

## Onboarding Checklist
- [ ] Install XAMPP and set up local environment
- [ ] Clone repo and place in `htdocs`
- [ ] Import database and configure credentials
- [ ] Review `README.md` and this documentation
- [ ] Explore main modules: admin, chat, generate paper
- [ ] Test PDF generation and uploads
- [ ] Check logs for errors
- [ ] Read code conventions and folder structure
- [ ] Join team communication channels
- [ ] Ask for access to production/staging if needed

---

For further help, contact the project maintainer or check the GitHub issues page.