# Jaribu — Kenyan Developer Sandbox

A self-hosted PHP tool for developers building payment integrations in Kenya. Generate realistic synthetic identity data, trigger live M-Pesa STK Push requests, create hosted checkout sessions, and inspect wallet balances — all from a single authenticated web interface.

The name means *"to test, to try"* in Swahili. That's exactly what this is for.

Built on the [IntaSend PHP SDK](https://github.com/IntaSend/intasend-php) with a production-grade structure — proper auth, CSRF protection, PDO, webhook signature verification — so the patterns you see here translate directly into real applications.

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

## Roadmap

Ordered by ease of implementation. Items at the top can be shipped in an afternoon; items at the bottom are multi-day efforts.

### ✅ Done

- [x] User authentication (register, login, logout)
- [x] Premium subscription via hosted checkout (KES 500 one-time)
- [x] M-Pesa STK Push
- [x] Wallet balance viewer
- [x] Transaction history log
- [x] ID Generator with free/premium field gating
- [x] Admin dashboard (users, payments, settings)
- [x] Webhook callback with signature verification
- [x] CSRF protection, bcrypt passwords, PDO prepared statements

---

### 🟡 Quick wins (hours, not days)

- [ ] **Copy profile to clipboard** — single button copies the full generated profile as JSON. Currently you copy field by field.
- [ ] **Profile history in session** — keep the last 5 generated profiles in session so a page refresh doesn't wipe everything.
- [ ] **Email on premium activation** — SMTP config is already in `.env`, just never wired up. Send a confirmation email when `is_premium` flips to 1 in the webhook callback.
- [ ] **Webhook event log in UI** — the `webhook_events` table already exists and is populated. Surface it as a readable feed in the admin dashboard so developers can see raw payloads without hitting MySQL directly.
- [ ] **Unify ID Generator UI with the rest of the app** — the matrix/hacker theme on `services.php` is jarring against the Wasiliana design system. Rebuild it in the same surface-card style.
- [ ] **Dark/light mode toggle** — the CSS tokens are already set up; just wire a `<html data-theme>` toggle to localStorage.

---

### 🟠 Medium effort (1–2 days each)

- [ ] **Copy-to-clipboard + JSON/CSV export** — extend the profile copy button to offer structured download formats. Prerequisite for the bulk generation feature below.
- [ ] **Deeper Kenya-specific data** — expand `MockDataService`:
  - Kenyan ID numbers with correct format (8-digit, year-range validated)
  - M-Pesa number prefixes by carrier: Safaricom `07[0-9]{2}`, Airtel `0730–0739`, Telkom `0770–0779`
  - Real Kenyan counties, constituencies, and ward names instead of a 5-item street list
  - Real Kenyan university names, company names, and employer sectors
- [ ] **Saved profiles** — add a `saved_profiles` table, let users bookmark up to 10 generated identities. Free limit: 3 saved. Premium: unlimited. No page refresh anxiety.
- [ ] **Payment link builder** — form-based no-code tool: enter amount, description, optional expiry → generates a shareable IntaSend checkout URL the user can send to anyone. No integration code required. Natural extension of the existing checkout page.
- [ ] **STK Push status polling** — after initiating an STK Push, poll the IntaSend invoice status every 3 seconds via a lightweight AJAX endpoint and update the UI without a full page reload. Currently the user has to refresh manually.

---

### 🔴 Larger features (multi-day, schema changes)

- [ ] **Bulk profile generation** — generate 10–500 profiles at once, download as CSV or JSON. Free tier: max 10. Premium tier: up to 500. This is the highest-value addition — anyone seeding a test database or building a demo app needs volume.
- [ ] **REST API with API keys** — expose `GET /api/v1/generate` with query params (`country`, `gender`, `count`, `fields`). Each user gets an API key from their profile page. Rate-limit: 10 req/day free, 1000 req/day premium. This is what turns the platform from a website into a SaaS product developers actually integrate.
- [ ] **Webhook testing tool** — assign each user a unique inbound URL (e.g. `/hook/{token}`). Any HTTP POST to that URL is captured and displayed in a live feed. Like a mini RequestBin baked into the platform. Directly useful for anyone building payment integrations.
- [ ] **Team accounts** — one owner account with multiple member seats sharing a profile library and API quota. This is the path from individual developers to agencies and QA teams — where real recurring revenue comes from.
- [ ] **Subscription model** — replace the one-time KES 500 with monthly/annual tiers. Free → Pro (KES 500/month) → Team (KES 2,000/month). The current model sells access once and has nothing left to offer.

---

> **Suggested first three:** copy-to-clipboard, saved profiles, and the REST API. Together they make the platform genuinely useful for a developer's daily workflow rather than just a demo.

---

## License

MIT — free to use, modify, and deploy.

---