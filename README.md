# Wasiliana — Kenyan Identity & Payments Platform

A full-stack PHP SaaS application that lets users generate realistic Kenyan identity profiles and explore live payment flows — hosted checkout links, M-Pesa STK Push, and wallet management — powered by the [IntaSend PHP SDK](https://github.com/IntaSend/intasend-php).

Built as a practical, production-structured reference for developers integrating mobile payments in Kenya.

---

## What it does

| Feature | Description |
|---|---|
| **ID Generator** | Generates realistic Kenyan (and global) identity profiles — names, national IDs, phone numbers, addresses, credit cards, work history. Premium-gated. |
| **Hosted Checkout** | Creates a secure, branded payment page via the IntaSend Checkout API. Customer is redirected to complete payment. |
| **M-Pesa STK Push** | Sends a payment prompt directly to any Kenyan mobile number. Customer approves on their phone. |
| **Wallet Viewer** | Displays IntaSend wallet balances and transaction history in real time. |
| **Transaction Log** | Full audit trail of every payment initiated — stored in MySQL, filterable by status and type. |
| **Premium Subscription** | One-time KES 500 payment unlocks the ID Generator. Handled through the same checkout flow. |
| **Admin Dashboard** | Manage users, view payments, toggle premium status, and monitor platform activity. |

---

## Tech stack

- **PHP 7.4+** — no framework, structured MVC-style
- **MySQL 5.7+** — PDO with prepared statements throughout
- **IntaSend PHP SDK** — Checkout, STK Push, Wallet, Transfer
- **Tailwind CSS + DaisyUI** — utility-first styling
- **Guzzle** — HTTP client used by the SDK

---

## Project structure

```
├── admin/                  # Admin-only pages (dashboard, users, settings)
├── assets/
│   └── css/style.css       # Custom design system (Wasiliana brand)
├── auth/                   # Login, register, logout
├── config/
│   ├── config.php          # Loads .env, exposes Config::get()
│   ├── database.php        # PDO singleton
│   └── session.php         # SessionManager with CSRF support
├── database/
│   └── schema.sql          # Full MySQL schema — run once to set up
├── includes/
│   └── functions.php       # Shared helpers (validation, logging, URLs)
├── middleware/
│   ├── auth.php            # require_auth(), get_logged_in_user()
│   └── admin.php           # require_admin()
├── models/
│   ├── Payment.php         # Payment CRUD + analytics queries
│   ├── User.php            # User CRUD + password verification
│   └── Settings.php        # Key-value site settings
├── pages/                  # Page partials included by app.php
│   ├── home.php            # Dashboard (stats, tools, activity feed)
│   ├── checkout_new.php    # Hosted checkout form
│   ├── stk_new.php         # STK Push form
│   ├── wallet.php          # Wallet viewer
│   ├── services.php        # ID Generator (premium)
│   ├── upgrade_new.php     # Premium upgrade flow
│   ├── payment_history.php # Transaction log
│   └── profile.php         # Account settings
├── services/
│   ├── PaymentService.php  # Wraps IntaSend SDK — checkout, STK, verify
│   └── MockDataService.php # Generates fake identity profile data
├── webhook/
│   └── callback.php        # Payment callback handler (signature verified)
├── app.php                 # Main authenticated router
├── index.php               # Redirects to app.php
├── .env.example            # Environment variable template
└── composer.json
```

---

## Quick start

### Prerequisites

- PHP 7.4+
- MySQL 5.7+
- Composer
- Laragon (or any local server — Apache/Nginx)
- An [IntaSend account](https://intasend.com) with API keys

### 1. Install dependencies

```bash
composer install
```

### 2. Configure environment

```bash
cp .env.example .env
```

Edit `.env`:

```env
# IntaSend — use sandbox keys for development
INTASEND_PUBLISHABLE_KEY=ISPubKey_test_xxxxxxxxxxxxxxxxxxxx
INTASEND_SECRET_KEY=ISSecretKey_test_xxxxxxxxxxxxxxxxxxxx

# Database
DB_HOST=localhost
DB_NAME=wasiliana
DB_USER=root
DB_PASS=

# App
APP_ENV=development
APP_URL=http://wasiliana.test
APP_NAME=Wasiliana
```

> **Sandbox vs Live:** The SDK auto-detects the environment from your key prefix (`ISPubKey_test_` → sandbox, `ISPubKey_live_` → production). Always use sandbox keys during development.

### 3. Create the database

```bash
mysql -u root < database/schema.sql
```

Or using Laragon's MySQL path:

```bash
"C:\laragon\bin\mysql\mysql-8.0.40-winx64\bin\mysql.exe" -u root < database/schema.sql
```

### 4. Access the app

If using Laragon, the virtual host is auto-created:

```
http://Intasend-Tutorial-Series-master.test
```

Register an account at `/auth/register.php`, then log in.

### 5. Create an admin account (optional)

After registering, run this SQL to promote a user:

```sql
UPDATE users SET is_admin = 1 WHERE email = 'your@email.com';
```

Then visit `/admin/dashboard.php`.

---

## Payment flow

```
User clicks "Upgrade"
  → pages/upgrade_new.php
  → PaymentService::createCheckout()
  → IntaSend Checkout API
  → User redirected to hosted payment page
  → User pays
  → IntaSend POSTs to /webhook/callback.php
  → Signature verified
  → User premium status updated in DB
```

---

## Environment variables

| Key | Description |
|---|---|
| `INTASEND_PUBLISHABLE_KEY` | Public key from IntaSend dashboard |
| `INTASEND_SECRET_KEY` | Secret key — never expose client-side |
| `DB_HOST` | Database host (default: `localhost`) |
| `DB_NAME` | Database name |
| `DB_USER` | Database user |
| `DB_PASS` | Database password |
| `APP_ENV` | `development` or `production` |
| `APP_URL` | Full base URL of the app |
| `SESSION_LIFETIME` | Session TTL in seconds (default: 7200) |

---

## Security notes

- Passwords hashed with `bcrypt` via `password_hash()`
- All DB queries use PDO prepared statements — no raw interpolation
- CSRF tokens on every POST form
- Session regeneration on login
- Webhook callbacks verify IntaSend's HMAC signature
- `.env` and `credentials.php` are gitignored — never committed
- Input sanitized with `htmlspecialchars` before output

---

## License

MIT — free to use, modify, and deploy.
