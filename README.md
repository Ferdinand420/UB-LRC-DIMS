# UB LRC-DIMS

UB Learning Resource Center Discussion Integrated Management System (DIMS)

## Programming and Development Tools

For the backend, we use PHP 8.x with `mysqli` prepared statements to provide a secure and clean code base, running on Apache via XAMPP on Windows for a straightforward local setup. The database is MySQL (`utf8mb4`) with schema and demo data managed under `database/` to simplify onboarding and testing.

Frontend is built with HTML5, CSS3, and JavaScript (vanilla). Instead of Bootstrap 5, we rely on a custom responsive design implemented in `assets/css/style.css` to deliver consistent interfaces across multiple platforms. Page-level JavaScript modules in `assets/js/` interact with lightweight REST-style PHP endpoints in `api/`, returning JSON for a clear separation of concerns.

Development efficiency is supported by Integrated Development Environments such as VS Code (recommended) or PhpStorm, with Git-based versioning (GitHub repository: `UB-LRC-DIMS`) enabling collaborative programming and code management.

## Quick Start

1) Install XAMPP and start Apache + MySQL
2) Place the project at `C:\xampp\htdocs\ub-lrc-dims`
3) Create DB `ub_lrc_dims` and import demo data:

```powershell
mysql -u root -p ub_lrc_dims < "C:\xampp\htdocs\ub-lrc-dims\database\seed_demo.sql"
```

4) Visit `http://localhost/ub-lrc-dims/`
5) Test logins (password: `password123`):
- Student: `student@ub.edu.ph`
- Librarian: `staff@ub.edu.ph`

If your MySQL root user has a password, update `config/db.php` accordingly.

## Architecture

- Entry: `index.php` (public landing with login modals)
- Auth: `auth/login.php` + helpers in `includes/auth.php` (session-based, role-aware)
- Pages: Student and Librarian views in `pages/` (dashboard, reservations, rooms, feedback, history, profile, approvals, violations, reports)
- APIs: JSON endpoints in `api/` consumed by page scripts in `assets/js/`
- Styling: Single theme in `assets/css/style.css` (responsive, rounded UI, maroon/gold)

## Folder Overview

```
ub-lrc-dims/
├── index.php
├── api/                 # JSON endpoints
├── assets/              # CSS, JS, media
├── auth/                # login/logout, debug
├── config/              # db connection
├── database/            # schema + seeds
├── includes/            # header, sidebar, auth helpers
└── pages/               # student + librarian pages
```

## Security & Data

- Prepared statements everywhere (`mysqli`)
- Bcrypt password hashing (`password_hash`/`password_verify`)
- Session regeneration on login
- Role-based access controls

## Notes

This implementation intentionally avoids heavy frameworks to keep setup fast and the footprint small for student/demo environments. If a future migration to Laravel or another framework is desired, we recommend adding environment configs (`.env`), introducing a router, and planning a phased migration of endpoints.