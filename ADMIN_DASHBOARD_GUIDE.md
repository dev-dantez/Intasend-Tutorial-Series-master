# Admin Dashboard - Complete Guide

## Overview

A comprehensive admin dashboard for managing users, subscriptions, pricing, and viewing site analytics.

## Features Implemented

### 1. Admin Dashboard (`/admin/dashboard.php`)
**Main Overview Page:**
- Real-time statistics (Total Users, Premium Users, Revenue, Current Price)
- Period-based analytics (Today, This Week, This Month)
- Recent payments table
- Recent users table
- Quick action buttons
- Visual stats with color-coded cards

**Metrics Displayed:**
- Total user count
- Premium user count with conversion rate
- Total revenue (all time)
- Current premium price
- Today's signups and revenue
- Weekly signups and revenue
- Monthly signups and revenue

### 2. Settings Management (`/admin/settings.php`)
**Configurable Settings:**
- Site name
- Maintenance mode toggle
- Premium subscription price
- Currency selection (KES, USD, EUR, GBP)
- View all settings in database table

**Features:**
- Real-time price updates
- Currency switching
- Maintenance mode for site-wide access control
- Settings history with last updated timestamps

### 3. User Management (`/admin/users.php`)
**User Administration:**
- View all users with pagination (20 per page)
- User details (ID, name, email, phone, status, join date)
- Toggle premium status for any user
- Grant/revoke admin privileges
- Visual indicators for premium and admin users
- Avatar placeholders with initials

**Actions Available:**
- Grant Premium - Instantly upgrade user to premium
- Remove Premium - Downgrade user to free
- Make Admin - Grant admin access
- Remove Admin - Revoke admin access

### 4. Subscriptions Management (`/admin/subscriptions.php`)
**Subscription Overview:**
- List all premium subscribers
- Subscription activation dates
- Amount paid per subscription
- Conversion rate statistics
- Total revenue tracking

**Metrics:**
- Premium user count
- Conversion rate (Free to Premium)
- Total revenue from subscriptions
- Individual subscription details

### 5. Admin Middleware (`/middleware/admin.php`)
**Security Features:**
- `require_admin()` - Protects admin pages
- `is_admin()` - Check admin status
- 403 error for unauthorized access
- Session-based authentication

### 6. Settings Model (`/models/Settings.php`)
**Database Operations:**
- `get($key, $default)` - Retrieve setting value
- `set($key, $value, $description)` - Update/create setting
- `getAll()` - Get all settings
- `delete($key)` - Remove setting

## Database Schema

### Users Table Updates
```sql
is_admin TINYINT(1) DEFAULT 0  -- Admin flag
INDEX idx_admin (is_admin)      -- Performance index
```

### Settings Table
```sql
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    description VARCHAR(255) NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
);
```

**Default Settings:**
- `premium_price` = 500 (KES)
- `currency` = KES
- `site_name` = IntaSend Identity Generator
- `maintenance_mode` = 0 (off)

## Setup Instructions

### Step 1: Update Database
Run the updated schema to add admin fields and settings table:
```bash
mysql -u root -p intasend_demo < database/schema.sql
```

### Step 2: Create First Admin
Visit: `http://your-site.test/admin/make_admin.php`

1. Enter your registered email address
2. Click "Make Admin"
3. **Delete the make_admin.php file for security**

### Step 3: Login as Admin
1. Go to login page
2. Login with your admin account
3. You'll be redirected to admin dashboard automatically

## Access URLs

| Page | URL | Description |
|------|-----|-------------|
| Dashboard | `/admin/dashboard.php` | Main admin overview |
| Settings | `/admin/settings.php` | Configure site settings |
| Users | `/admin/users.php` | Manage users |
| Subscriptions | `/admin/subscriptions.php` | View subscriptions |
| Make Admin | `/admin/make_admin.php` | Create first admin (delete after use) |

## User Interface

### Navigation
- **Admin Link** in main header (red color, visible only to admins)
- **Back to Site** button in admin pages
- **Breadcrumb navigation** with back buttons

### Design Elements
- **Color-coded stats cards:**
  - Blue: Total Users
  - Green: Premium Users
  - Purple: Revenue
  - Orange: Current Price

- **Status Badges:**
  - Success (Green): Premium, Active, Complete
  - Ghost (Gray): Free users
  - Error (Red): Admin users
  - Warning (Yellow): Pending status

### Responsive Design
- Mobile-friendly tables
- Collapsible navigation
- Adaptive grid layouts
- Touch-friendly buttons

## Security Features

### Authentication
- ✅ Session-based authentication required
- ✅ Admin middleware protection
- ✅ CSRF token validation on forms
- ✅ Password hashing (bcrypt)

### Authorization
- ✅ Admin-only page access
- ✅ Cannot modify own admin status
- ✅ Activity logging for admin actions
- ✅ 403 error for unauthorized access

### Best Practices
- ✅ Input sanitization
- ✅ SQL injection prevention (PDO)
- ✅ XSS protection (htmlspecialchars)
- ✅ Secure session management

## Analytics & Reporting

### Available Metrics
1. **User Metrics:**
   - Total user count
   - Premium user count
   - Conversion rate
   - Daily/weekly/monthly signups

2. **Revenue Metrics:**
   - Total revenue (all time)
   - Daily revenue
   - Weekly revenue
   - Monthly revenue

3. **Subscription Metrics:**
   - Active subscriptions
   - Subscription dates
   - Payment amounts
   - User details

### Data Visualization
- Real-time stat cards
- Period-based comparisons
- Percentage calculations
- Trend indicators

## Admin Actions

### User Management Actions
```php
// Grant Premium
POST /admin/users.php
action=toggle_premium&user_id=123

// Make Admin
POST /admin/users.php
action=toggle_admin&user_id=123
```

### Settings Updates
```php
// Update Settings
POST /admin/settings.php
premium_price=500
currency=KES
site_name=My Site
maintenance_mode=1
```

## Customization

### Adding New Settings
```php
$settingsModel = new Settings();
$settingsModel->set('new_setting', 'value', 'Description');
```

### Adding New Admin Pages
1. Create file in `/admin/` directory
2. Add `require_admin()` at top
3. Include header and footer
4. Add link in dashboard

### Modifying Pricing
1. Go to Settings page
2. Update "Premium Price" field
3. Select currency
4. Save changes
5. New price applies immediately

## Troubleshooting

### "Access denied" Error
- Ensure you're logged in
- Verify admin status in database
- Check session is active

### Settings Not Saving
- Verify database connection
- Check settings table exists
- Ensure proper permissions

### Admin Link Not Showing
- Clear browser cache
- Check session has `is_admin` set
- Verify header.php includes admin check

### Cannot Make User Admin
- Cannot modify your own admin status
- Check user exists in database
- Verify CSRF token is valid

## Performance Considerations

### Database Optimization
- Indexed columns for fast queries
- Pagination for large datasets
- Efficient JOIN queries
- Cached settings retrieval

### Page Load Times
- Minimal database queries
- Efficient data fetching
- Lazy loading for tables
- Optimized SQL queries

## Future Enhancements

### Potential Features
- [ ] Export data to CSV/Excel
- [ ] Advanced analytics charts
- [ ] Email notifications for admin events
- [ ] Bulk user operations
- [ ] Custom roles and permissions
- [ ] Activity audit log viewer
- [ ] Revenue forecasting
- [ ] User engagement metrics
- [ ] A/B testing for pricing
- [ ] Automated reports

### Technical Improvements
- [ ] API endpoints for admin actions
- [ ] Real-time updates (WebSockets)
- [ ] Advanced filtering and search
- [ ] Data visualization charts
- [ ] Mobile admin app
- [ ] Two-factor authentication for admins

## Best Practices

### Security
1. **Always delete** `make_admin.php` after first use
2. **Use strong passwords** for admin accounts
3. **Enable HTTPS** in production
4. **Regular backups** of database
5. **Monitor admin activity** logs

### Operations
1. **Review settings** regularly
2. **Monitor conversion rates** for pricing optimization
3. **Check user feedback** before price changes
4. **Test in staging** before production changes
5. **Keep admin accounts** to minimum necessary

### Maintenance
1. **Update dependencies** regularly
2. **Review logs** for errors
3. **Optimize database** periodically
4. **Monitor performance** metrics
5. **Backup before** major changes

## Support

### Getting Help
- Check logs: `logs/activity.log`
- Review database: Check settings table
- Test queries: Use phpMyAdmin
- Debug: Enable error reporting

### Common Issues
1. **Pricing not updating**: Clear cache, check database
2. **Users not showing**: Check pagination, verify query
3. **Stats incorrect**: Verify date ranges, check timezone
4. **Access denied**: Confirm admin status, check session

---

**Status**: ✅ Fully Functional
**Security**: ✅ Protected with middleware
**Performance**: ✅ Optimized queries
**Mobile**: ✅ Responsive design
**Documentation**: ✅ Complete
