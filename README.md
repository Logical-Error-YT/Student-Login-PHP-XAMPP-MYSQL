# StudentPortal — Student Management & Authentication System

A modern, portfolio-ready student portal built with HTML5, CSS3, JavaScript, PHP, and MySQL.

---

## 🚀 Quick Setup (XAMPP)

### Step 1 — Install XAMPP
Download and install XAMPP from https://www.apachefriends.org/
Start both **Apache** and **MySQL** from the XAMPP Control Panel.

### Step 2 — Copy Project Files
Place the entire `student_portal` folder inside:
```
C:\xampp\htdocs\student_portal\      (Windows)
/Applications/XAMPP/htdocs/student_portal/   (macOS)
```

### Step 3 — Create the Database
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **"Import"** in the top menu
3. Click **"Choose File"** → select `database.sql` from the project folder
4. Click **"Go"** at the bottom

That's it! The `student_portal` database and `users` table will be created automatically.
A demo user is also inserted:
- **Email:** `demo@student.com`
- **Password:** `demo123`

### Step 4 — Run the App
Open your browser and visit:
```
http://localhost/student_portal/
```

---

## 📁 File Structure

```
student_portal/
├── index.html          ← Landing page
├── register.php        ← Registration page
├── login.php           ← Login page
├── dashboard.php       ← Student dashboard (protected)
├── edit_profile.php    ← Edit profile (protected)
├── logout.php          ← Session logout handler
├── database.sql        ← Database schema + seed data
│
├── css/
│   └── style.css       ← All styles (global + page-specific)
│
├── js/
│   └── main.js         ← Validation, UI interactions, animations
│
└── php/
    └── config.php      ← Database connection configuration
```

---

## 🔐 Security Features

| Feature | Implementation |
|---|---|
| Password storage | `password_hash()` with bcrypt |
| Password verification | `password_verify()` |
| SQL injection prevention | MySQLi prepared statements |
| XSS prevention | `htmlspecialchars()` on all output |
| Session fixation | `session_regenerate_id(true)` on login |
| Auth guards | Session check on all protected pages |

---

## 🎨 Tech Stack

- **Frontend:** HTML5, CSS3 (custom properties, Grid, Flexbox), Vanilla JS
- **Backend:** PHP 8+ (sessions, prepared statements)
- **Database:** MySQL / MariaDB
- **Fonts:** DM Sans + DM Serif Display (Google Fonts)
- **Icons:** Inline SVG (no external icon library needed)

---

## ⚙️ Configuration

Edit `php/config.php` to update your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Default XAMPP = empty password
define('DB_NAME', 'student_portal');
```

---

## 🧪 Demo Account

| Field | Value |
|---|---|
| Email | demo@student.com |
| Password | demo123 |

You can delete this demo row from phpMyAdmin after testing.

---

Built with ♥ for students and developers.
