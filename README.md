# Hargeisa Village Restaurant

<p align="center">
  <img src="Screenshot 2026-06-03 145236.png" width="900"/>
</p>


[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-CDN-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/License-Proprietary-red)](LICENSE)

A full-stack restaurant website for **Hargeisa Village** — public pages for guests, a secure admin panel for staff, and MySQL-backed content management. Built with plain PHP (no framework), optimized for local development on **XAMPP** (Apache + PHP + MySQL).

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Project Structure](#project-structure)
- [Email Notifications](#email-notifications)
- [Security](#security)
- [License](#license)

---

## Overview

This project delivers a production-ready restaurant web presence:

| Area | Description |
|------|-------------|
| **Public site** | Marketing pages, menu, reservations, gallery, and contact |
| **Admin panel** | Dashboard, CRUD for menu/gallery/reservations, CMS pages, settings |
| **Data layer** | MySQL schema with roles, settings, and relational menu/reservation data |

Timezone defaults to `Africa/Mogadishu`. The public UI supports **light/dark mode** with persisted theme preference.

---

## Features

### Public website

- **Home** — Hero, promotions, and featured content
- **Menu** — Categories, pricing, availability, dietary tags
- **Reservations** — Date/time/guest booking with email flow
- **About** — CMS-driven page content
- **Gallery** — Categorized images with lightbox
- **Contact** — Inquiry form and embedded map

### Admin panel

| Module | Capabilities |
|--------|----------------|
| Dashboard | Pending reservations, unread messages |
| Menu | Categories and items (images, tags, featured flag) |
| Reservations | Status workflow: pending → confirmed → seated / declined / cancelled |
| Gallery | Upload and order images |
| Pages | WYSIWYG editing (Quill) for About and custom slugs |
| Messages | Contact form inbox |
| Settings | Restaurant name, hours, contact, notification email |
| Users | Admin and editor roles with bcrypt passwords |

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8+ (strict types, session auth, PDO) |
| Database | MySQL 5.7+ / MariaDB (`utf8mb4`) |
| Frontend | Tailwind CSS (CDN), vanilla JavaScript |
| Server | Apache (`mod_rewrite` optional for clean URLs) |
| Local dev | [XAMPP](https://www.apachefriends.org/) recommended |

---

## Requirements

- PHP **8.0** or newer with extensions: `pdo_mysql`, `mbstring`, `json`
- MySQL **5.7+** or MariaDB **10.3+**
- Apache with document root access to the project folder
- Writable `uploads/` directory for image uploads

---

## Quick Start

### 1. Clone and place the project

```bash
git clone https://github.com/pindhe/Hargeisa-Village.git
```

Copy the `Village` folder into your web root, for example:

```text
C:\xampp\htdocs\Village
```

### 2. Start services

Open **XAMPP Control Panel** and start **Apache** and **MySQL**.

### 3. Run the installer

Browse to:

```text
http://localhost/Village/install.php
```

Click **Run Installation** to create the database, seed defaults, and configure the admin user.

### 4. Sign in to admin

| Field | Default (change after first login) |
|-------|-------------------------------------|
| URL | `http://localhost/Village/admin/login.php` |
| Username | `admin` |
| Password | `Admin@123` (or value set during install) |

### 5. Post-install

> **Important:** Delete `install.php` from the server after a successful setup.

### Manual database setup (optional)

If you prefer phpMyAdmin or the CLI:

1. Import `database/schema.sql`
2. Set the admin password hash:

```sql
UPDATE users SET password_hash = '$2y$10$...' WHERE username = 'admin';
```

Generate a bcrypt hash in PHP:

```php
echo password_hash('YourSecurePassword', PASSWORD_BCRYPT);
```

Additional migrations (if needed): `database/hours_update.sql`, `database/footer_settings.sql`.

---

## Configuration

### Database — `config/database.php`

```php
'host' => '127.0.0.1',
'dbname' => 'hargeisa_village',
'username' => 'root',
'password' => '',
```

### Application — `config/app.php`

```php
'url' => 'http://localhost/Village',
'timezone' => 'Africa/Mogadishu',
```

Adjust `url`, `upload_url`, and `admin_path` when deploying under a different base path or domain.

---

## Project Structure

```text
Village/
├── admin/              # Authenticated admin panel
│   ├── includes/       # Layout, helpers, navigation
│   ├── login.php
│   └── …               # CRUD modules (menu, gallery, etc.)
├── assets/
│   ├── css/            # Public, admin, dark mode, contact styles
│   └── js/             # Theme, gallery, admin, experience scripts
├── config/             # app.php, database.php (protect via .htaccess)
├── database/           # schema.sql and incremental updates
├── includes/           # Bootstrap, auth, Mailer, Settings, layouts
├── uploads/            # User-uploaded images (web-accessible)
├── index.php           # Home and routed public pages
├── install.php         # One-time installer (remove after use)
└── .htaccess           # Apache rules
```

---

## Email Notifications

Reservation confirmations and status updates use PHP `mail()`. On localhost, configure XAMPP **sendmail** or test on a host with a real SMTP relay. Set the notification address under **Admin → Settings**.

For production, consider replacing `includes/Mailer.php` with SMTP (e.g. PHPMailer + your provider).

---

## Security

| Practice | Reason |
|----------|--------|
| Change the default admin password immediately | Prevents unauthorized admin access |
| Remove `install.php` after setup | Blocks re-initialization attacks |
| Use HTTPS in production | Protects sessions and form data |
| Restrict `config/` on VPS deployments | Keep credentials outside the public docroot when possible |
| Use strong passwords for `admin` and `editor` roles | Bcrypt hashes stored in `users` table |

---

## License

**Proprietary** — © Hargeisa Village Restaurant. All rights reserved.

Unauthorized copying, distribution, or commercial use without permission is prohibited.

---

<p align="center">
  <sub>Built for Hargeisa Village · Plain PHP · MySQL · Tailwind CSS</sub>
</p>
