# Hargeisa Village Restaurant Website

Plain PHP restaurant website with MySQL, Tailwind CSS, and a secure admin panel. Built for XAMPP (Apache + PHP 8.x + MySQL).

## Features

**Public site:** Home, Menu (categorized + dietary tags), Reservations, About, Gallery (lightbox), Contact (form + map).

**Admin panel:** Dashboard, menu categories/items, reservations, gallery, CMS pages (Quill editor), contact messages, settings, user management (admin role).

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache with `mod_rewrite` (optional clean URLs)
- XAMPP recommended

## Installation (XAMPP)

1. Place project in `C:\xampp\htdocs\Village`
2. Start **Apache** and **MySQL** in XAMPP Control Panel
3. Open `http://localhost/Village/install.php` and click **Run Installation**
4. Log in at `http://localhost/Village/admin/login.php`
   - Username: `admin`
   - Password: `Admin@123` (or what you set during install)
5. **Delete `install.php`** after setup

### Manual database import

Import `database/schema.sql` via phpMyAdmin, then set admin password:

```sql
UPDATE users SET password_hash = '$2y$10$...' WHERE username = 'admin';
```

Generate hash in PHP: `echo password_hash('YourPassword', PASSWORD_BCRYPT);`

## Configuration

Edit `config/database.php` for MySQL credentials and `config/app.php` for base URL:

```php
'url' => 'http://localhost:80/Village',
```

## Project Structure

```
Village/
├── admin/           # Admin panel
├── assets/          # CSS, JS
├── config/          # App & database config
├── database/        # schema.sql
├── includes/        # Bootstrap, auth, mail, layouts
├── uploads/         # Uploaded images
├── index.php        # Public pages
└── install.php      # One-time setup
```

## Email

Reservation and status emails use PHP `mail()`. On localhost, configure XAMPP sendmail or use a production SMTP relay. Notification address is set in **Admin → Settings**.

## Security Notes

- Change default admin password immediately
- Remove `install.php` after installation
- Use HTTPS in production
- Keep `config/` outside web root if deploying to VPS (adjust paths)

## License

Proprietary — Hargeisa Village Restaurant.
