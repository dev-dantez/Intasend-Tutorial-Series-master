# Upgrade Guide

## What Was Fixed

### Critical Security Issues

1. **Exposed API Credentials**
   - **Before**: Hardcoded in `credentials.php`
   - **After**: Stored in `.env` file (gitignored)
   - **Action**: Copy `.env.example` to `.env` and add your keys

2. **No Payment Verification**
   - **Before**: Callback just displayed GET params
   - **After**: Full signature verification via IntaSend SDK
   - **Location**: `webhook/callback.php`

3. **Session Vulnerabilities**
   - **Before**: Basic session_start() with no security
   - **After**: Secure session management with regeneration, timeouts, CSRF
   - **Location**: `config/session.php`

4. **Missing Input Validation**
   - **Before**: Direct use of POST data
   - **After**: Server-side validation for phone, email, amounts
   - **Location**: `includes/functions.php`

5. **No Authentication**
   - **Before**: Anyone could access everything
   - **After**: Full user registration/login system
   - **Location**: `auth/` directory

### Architecture Improvements

6. **Database Persistence**
   - **Added**: MySQL database for users and payments
   - **Schema**: `database/schema.sql`
   - **Models**: `models/User.php`, `models/Payment.php`

7. **Service Layer**
   - **Added**: `PaymentService` for business logic
   - **Benefit**: Separation of concerns, easier testing

8. **Configuration Management**
   - **Added**: `Config` class for environment-based settings
   - **Location**: `config/config.php`

9. **Middleware**
   - **Added**: Authentication middleware
   - **Location**: `middleware/auth.php`

10. **Activity Logging**
    - **Added**: File-based logging for auditing
    - **Location**: `logs/activity.log`

## Migration Steps

### 1. Backup Current Data
```bash
# Backup your old credentials
cp credentials.php credentials.php.backup
```

### 2. Install Database
```bash
mysql -u root -p
CREATE DATABASE intasend_demo;
exit;

mysql -u root -p intasend_demo < database/schema.sql
```

### 3. Configure Environment
```bash
cp .env.example .env
# Edit .env with your credentials
```

### 4. Update Composer (if needed)
```bash
composer update
```

### 5. Set Permissions
```bash
mkdir logs
chmod 755 logs
```

### 6. Test Authentication
1. Visit `auth/register.php`
2. Create test account
3. Login and test features

## Breaking Changes

### File Locations
- Old: `checkout.php` → New: `pages/checkout_new.php`
- Old: `stk.php` → New: `pages/stk_new.php`
- Old: `upgrade.php` → New: `pages/upgrade_new.php`
- Old: `callback.php` → New: `webhook/callback.php`

### Session Variables
- Old: `$_SESSION['is_premium']`
- New: `SessionManager::get('is_premium')`

### Database Required
- Old: Session-only storage
- New: MySQL database required

## New Features

1. **User Accounts**
   - Registration/Login system
   - Password hashing
   - Profile management

2. **Payment History**
   - View all past transactions
   - Filter by status
   - Detailed payment info

3. **Enhanced Security**
   - CSRF tokens on all forms
   - Input validation
   - SQL injection prevention
   - XSS protection

4. **Better UX**
   - User dropdown menu
   - Payment confirmation pages
   - Error messages
   - Loading states

## Removed Features

- `b2c.php` - Removed (dangerous auto-approve)
- `paybutton.php` - Removed (use checkout instead)
- `payment_links.php` - Removed (use checkout instead)

These can be re-added with proper security if needed.

## Testing Checklist

- [ ] User registration works
- [ ] User login works
- [ ] STK Push sends to phone
- [ ] Checkout creates payment link
- [ ] Callback verifies payment
- [ ] Premium upgrade works
- [ ] Payment history displays
- [ ] Session timeout works
- [ ] CSRF protection active
- [ ] Logs are being written

## Rollback Plan

If issues occur:

1. Restore old files from backup
2. Keep database (no harm done)
3. Report issues for fixes

## Support

Check `README.md` for full documentation.
