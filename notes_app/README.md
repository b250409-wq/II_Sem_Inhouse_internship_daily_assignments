# Notes App with Tags (NoteFlow)

A production-ready PHP 8 + MySQL notes application with tags, favorites, archive, dark mode, dashboard analytics and an admin panel. Bootstrap 5 UI, Font Awesome icons, Google Fonts, Chart.js, animated hero and glassmorphism.

## Features
- Modern responsive landing page (Hero, Features, About, Contact)
- Secure auth: bcrypt, CSRF tokens, prepared statements, session hardening, session timeout
- Notes: CRUD, pin, favorite, archive, restore, soft delete, multi-tag, search, sort, live filter, export JSON
- Tags: create/edit/delete, color, filter by tag
- Profile: username/email/theme, avatar upload, change password
- Dashboard: stats cards, Chart.js, recent + pinned
- Admin panel: users, notes, tags, contact messages, statistics
- Dark / Light mode, smooth scroll, AOS-style reveals

## Tech Stack
- PHP 8+ (PDO, prepared statements, password_hash)
- MySQL 5.7+ / MariaDB
- Bootstrap 5.3, Font Awesome 6, Google Fonts (Inter + Space Grotesk), Chart.js

## Folder Structure
```
notes_app/
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   ├── images/
│   └── uploads/           (avatars)
├── config/
│   ├── config.php         (env-like constants)
│   └── database.php       (PDO singleton)
├── includes/
│   └── helpers.php        (session, CSRF, auth, flash, e())
├── controllers/
│   ├── AuthController.php
│   ├── NoteController.php
│   ├── TagController.php
│   ├── ContactController.php
│   └── ProfileController.php
├── models/
│   ├── User.php  Note.php  Tag.php  Contact.php
├── views/
│   ├── partials/          (head, navbar, sidebar, topbar, footer, flash)
│   ├── auth/              (login, register, forgot)
│   ├── dashboard/         (index, notes, note-form, tags, favorites, archive, profile, settings)
│   └── admin/             (index)
├── database/sql/notes_app.sql
├── index.php  privacy.php  terms.php
├── .htaccess
└── README.md
```

## Installation (XAMPP)
1. Install **XAMPP** (PHP 8+). Start **Apache** and **MySQL** from the XAMPP control panel.
2. Copy the `notes_app/` folder into `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (macOS).
3. Open **phpMyAdmin** → `http://localhost/phpmyadmin` → **Import** → select `database/sql/notes_app.sql` → **Go**.
4. If your MySQL uses a password, edit `config/config.php` and set `DB_USER` / `DB_PASS`.
5. If you deploy under a different path, update `APP_URL` in `config/config.php`.
6. Make `assets/uploads/` writable:
   - macOS/Linux: `chmod -R 777 assets/uploads`
   - Windows: right-click → Properties → Security → allow write for Users.
7. Visit `http://localhost/notes_app/`.

## Default Accounts
- **Admin** → `admin@notesapp.local` / `Admin@123`
- Register any user account from the Register page.

## Security
- All queries use PDO **prepared statements** (SQL injection safe)
- CSRF token on every state-changing form (`csrf_field()` / `csrf_verify()`)
- All rendered output escaped with `e()` (XSS safe)
- Passwords hashed with `password_hash()` (bcrypt)
- Session cookie is HttpOnly + SameSite=Lax, regenerated on login, 2h timeout
- `.htaccess` blocks direct access to `.sql` / `.md` files and disables directory indexes

## Database Diagram (simplified ER)
```
users (id PK)
   ├─< notes (user_id FK)
   │      └─< note_tags >─┐
   ├─< tags  (user_id FK) ─┘
   ├─< notifications (user_id FK)
   └─< sessions (user_id FK)

contact_messages (standalone)
password_resets  (standalone)
```

## Project Flow
```
Landing (index.php)
   ├─ Register / Login  → controllers/AuthController.php
   │                         └─ session set → Dashboard
   └─ Contact form      → controllers/ContactController.php → contact_messages

Dashboard (views/dashboard/index.php)
   ├─ Notes  (list/create/edit) → controllers/NoteController.php → notes + note_tags
   ├─ Tags                        → controllers/TagController.php → tags
   ├─ Favorites / Archive         → NoteController toggles
   ├─ Profile                     → controllers/ProfileController.php → users (+ avatar upload)
   └─ Settings (theme toggle - client only)

Admin (views/admin/index.php)  [role=admin]
   └─ Stats, users, contact messages
```
