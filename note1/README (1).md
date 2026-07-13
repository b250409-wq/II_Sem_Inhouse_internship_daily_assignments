# Notes App — PHP + MySQL Backend

This adds a real backend to the Notes App front end: user accounts (signup/login/logout),
notes stored per-user in MySQL, and contact form submissions saved to the database.

## What changed

- **New:** `config.php`, `db.php`, `schema.sql`, and everything in `api/`
- **Updated:** `index.html` (navbar now shows Login/Sign Up or a user greeting + Log Out;
  the notes panel is locked behind login), `script.js` (notes/auth/contact now talk to the
  PHP API via `fetch` instead of `localStorage`), `style.css` (a few small additions for the
  new login-prompt and user-greeting elements)

## Requirements

- PHP 7.4+ (8.x recommended) with the `pdo_mysql` extension
- MySQL 5.7+ or MariaDB
- Any web server that runs PHP (Apache, Nginx+PHP-FPM, or PHP's built-in server for local testing)

## Setup

1. **Create the database.**
   ```bash
   mysql -u root -p < schema.sql
   ```
   This creates a `notes_app` database with `users`, `notes`, and `contact_messages` tables.

2. **Set your DB credentials.** Edit `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'notes_app');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password_here');
   ```
   Set `APP_ENV` to `'production'` once you deploy — this hides detailed PHP error messages.

3. **Serve the project root** (the folder containing `index.html`, `config.php`, `api/`, etc.)
   with PHP. For local testing:
   ```bash
   php -S localhost:8000
   ```
   Then open `http://localhost:8000`.

   On Apache/Nginx, just point the document root at this folder — no special rewrite
   rules are needed since the front end calls `api/*.php` directly.

## How it works

- **Auth:** `api/register.php` and `api/login.php` create a PHP session (`$_SESSION['user_id']`)
  stored in a cookie; `api/session.php` is polled on page load to restore that session in the UI;
  `api/logout.php` destroys it. Passwords are hashed with `password_hash()` (bcrypt) — never stored
  in plain text.
- **Notes:** Every note row has a `user_id` foreign key. All of `api/notes_*.php` call
  `requireLogin()` first, so a note can only ever be read/edited/deleted by the user who owns it.
- **Contact form:** `api/contact.php` inserts submissions into `contact_messages` — check that
  table (or hook in a mail library like PHPMailer if you'd rather get emails) to see messages.
- **Security basics already in place:** prepared statements everywhere (no raw SQL concatenation),
  password hashing, `httponly`/`SameSite=Lax` session cookies, and server-side validation on every
  endpoint (not just the client-side checks in the UI).

## Not included (optional next steps)

- Email verification / password-reset flow
- CSRF tokens on the forms (fine for a single-origin app like this, but worth adding if you expand it)
- Rate limiting on login/signup to slow down brute-force attempts
- An admin view for `contact_messages`
