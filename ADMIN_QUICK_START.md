# Admin Dashboard - Quick Start Guide

## ✅ Database Updated!

The database has been updated with:
- `is_admin` column in users table
- `settings` table with default values
- Proper indexes for performance

## 🚀 Create Your Admin Account

### Step 1: Register (if you haven't already)
Visit: **http://intasend-tutorial-series-master.test/auth/register.php**

Create your account with:
- First Name
- Last Name
- Email
- Phone (optional)
- Password

### Step 2: Make Yourself Admin
Visit: **http://intasend-tutorial-series-master.test/admin/make_admin.php**

1. Enter the email you just registered with
2. Click "Make Admin"
3. You'll see success message
4. **IMPORTANT: Delete the `admin/make_admin.php` file for security!**

### Step 3: Login
Visit: **http://intasend-tutorial-series-master.test/auth/login.php**

Or simply: **http://intasend-tutorial-series-master.test/login.php** (redirects automatically)

Login with your credentials - you'll be redirected to the admin dashboard!

## 📊 Admin Dashboard Features

Once logged in as admin, you can:

### Dashboard (`/admin/dashboard.php`)
- View total users, premium users, revenue
- See today's, week's, and month's statistics
- Check recent payments and new users
- Quick access to all admin functions

### Settings (`/admin/settings.php`)
- Change premium subscription price
- Select currency (KES, USD, EUR, GBP)
- Toggle maintenance mode
- Configure site name

### Users (`/admin/users.php`)
- View all registered users
- Grant/revoke premium status
- Make/remove admin privileges
- See user details and join dates

### Subscriptions (`/admin/subscriptions.php`)
- View all premium subscribers
- See activation dates and amounts paid
- Track conversion rates
- Monitor total revenue

## 🔗 Quick Links

| Page | URL |
|------|-----|
| Register | `/auth/register.php` |
| Login | `/auth/login.php` or `/login.php` |
| Make Admin | `/admin/make_admin.php` |
| Admin Dashboard | `/admin/dashboard.php` |
| Settings | `/admin/settings.php` |
| Users | `/admin/users.php` |
| Subscriptions | `/admin/subscriptions.php` |

## 🎯 Common Tasks

### Change Premium Price
1. Go to Admin → Settings
2. Update "Premium Price" field
3. Select currency if needed
4. Click "Save Settings"

### Grant Premium to User
1. Go to Admin → Users
2. Find the user
3. Click the three-dot menu
4. Select "Grant Premium"

### Make Another User Admin
1. Go to Admin → Users
2. Find the user
3. Click the three-dot menu
4. Select "Make Admin"

## 🔒 Security Notes

1. **Delete `admin/make_admin.php`** after creating your first admin
2. Use strong passwords for admin accounts
3. Don't share admin credentials
4. Monitor the activity logs regularly
5. Keep admin accounts to a minimum

## 🎨 Navigation

When logged in as admin, you'll see:
- **Red "Admin" link** in the main navigation header
- Click it to access the admin dashboard
- All admin pages have a back button to return to dashboard

## ⚡ Quick Test

1. Login as admin
2. Go to Settings
3. Change premium price to 600
4. Save
5. Go back to Dashboard
6. Verify the price shows 600 in the orange card

## 🆘 Troubleshooting

### "Access denied" error
- Make sure you ran the make_admin.php step
- Check your email was entered correctly
- Try logging out and back in

### Admin link not showing
- Clear browser cache
- Verify you're logged in
- Check database: `SELECT is_admin FROM users WHERE email='your@email.com'`

### Settings not saving
- Check database connection
- Verify settings table exists
- Check browser console for errors

## 📝 Next Steps

After setting up admin:

1. ✅ Test the dashboard - view all stats
2. ✅ Try changing the premium price
3. ✅ Create a test user and grant them premium
4. ✅ Explore the subscriptions page
5. ✅ Familiarize yourself with user management

## 🎉 You're Ready!

Your admin dashboard is now fully set up and ready to use. You can manage your entire platform from one central location.

---

**Need Help?** Check the full documentation in `ADMIN_DASHBOARD_GUIDE.md`
