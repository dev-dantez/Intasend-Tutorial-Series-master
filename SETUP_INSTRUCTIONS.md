# Quick Setup Instructions

## Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Composer
- IntaSend Account

## 5-Minute Setup

### Step 1: Install Dependencies
```bash
composer install
```

### Step 2: Create Database
```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE intasend_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Import schema
mysql -u root -p intasend_demo < database/schema.sql
```

### Step 3: Configure Environment
```bash
# Copy example file
cp .env.example .env

# Edit .env with your details
nano .env  # or use any text editor
```

Required `.env` values:
```env
INTASEND_PUBLISHABLE_KEY=ISPubKey_test_xxxxx
INTASEND_SECRET_KEY=ISSecretKey_test_xxxxx
DB_NAME=intasend_demo
DB_USER=root
DB_PASS=your_mysql_password
```

### Step 4: Set Permissions
```bash
mkdir logs
chmod 755 logs
```

### Step 5: Test Installation
Visit: `http://localhost/your-project/install.php`

This will check all requirements and show any issues.

### Step 6: Create Account
Visit: `http://localhost/your-project/auth/register.php`

Create your first user account.

### Step 7: Start Using
Login and explore:
- STK Push payments
- Checkout links
- Wallet management
- Payment history
- Premium upgrade demo

## Getting IntaSend Credentials

1. Sign up at https://intasend.com
2. Go to Dashboard → Settings → API Keys
3. Copy your Publishable Key and Secret Key
4. Use TEST keys for development
5. Switch to LIVE keys for production

## Troubleshooting

### "Database connection failed"
- Check MySQL is running
- Verify DB credentials in `.env`
- Ensure database exists

### "Class not found"
- Run `composer install`
- Check vendor directory exists

### "Permission denied" on logs
- Run `chmod 755 logs`
- Ensure web server can write to logs/

### "CSRF token validation failed"
- Clear browser cookies
- Check session is working
- Verify session.save_path is writable

## Security Checklist

Before going live:

- [ ] Change `APP_ENV=production` in `.env`
- [ ] Use LIVE IntaSend keys (not test)
- [ ] Set strong database password
- [ ] Enable HTTPS
- [ ] Set `session.cookie_secure=1`
- [ ] Review file permissions
- [ ] Enable error logging (not display)
- [ ] Backup database regularly

## Next Steps

1. Test payment flows with test credentials
2. Customize the UI to match your brand
3. Add email notifications (configure SMTP in `.env`)
4. Set up webhooks for async payment updates
5. Review security settings before production

## Support

- IntaSend Docs: https://developers.intasend.com/
- PHP SDK: https://github.com/IntaSend/intasend-php
- Issues: Check logs/activity.log

## Quick Commands

```bash
# View logs
tail -f logs/activity.log

# Reset database
mysql -u root -p intasend_demo < database/schema.sql

# Clear sessions (if needed)
rm -rf /tmp/sess_*

# Check PHP version
php -v

# Check loaded extensions
php -m
```

That's it! You're ready to accept payments. 🚀
