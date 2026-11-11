# IntaSend PHP Payment Integration - Upgraded

A secure, production-ready PHP application demonstrating IntaSend payment gateway integration with proper authentication, database persistence, and security best practices.

## Features

### Payment Methods
- **Checkout Links** - Hosted payment page redirects
- **M-Pesa STK Push** - Direct mobile payment prompts
- **Wallet Management** - View balances and transactions

### Security Features
- ✅ Environment-based configuration (.env)
- ✅ Secure session management with CSRF protection
- ✅ User authentication and authorization
- ✅ Input validation and sanitization
- ✅ Password hashing (bcrypt)
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ Activity logging
- ✅ Payment verification with signatures

### Application Features
- User registration and login
- Premium subscription system
- Payment history tracking
- Database persistence for all transactions
- Proper error handling
- Responsive UI with DaisyUI/Tailwind

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- IntaSend account with API credentials

### Setup Steps

1. **Clone and Install Dependencies**
```bash
composer install
```

2. **Database Setup**
```bash
mysql -u root -p < database/schema.sql
```

3. **Environment Configuration**
```bash
cp .env.example .env
```

Edit `.env` and add your credentials:
```env
INTASEND_PUBLISHABLE_KEY=your_publishable_key
INTASEND_SECRET_KEY=your_secret_key
DB_NAME=intasend_demo
DB_USER=root
DB_PASS=your_password
```

4. **Set Permissions**
```bash
chmod 755 logs/
```

5. **Access Application**
- Navigate to `http://localhost/your-project/auth/register.php`
- Create an account
- Start using the payment features

## Project Structure

```
├── auth/                  # Authentication pages
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── config/               # Configuration files
│   ├── config.php
│   ├── database.php
│   └── session.php
├── database/             # Database schemas
│   └── schema.sql
├── includes/             # Helper functions
│   └── functions.php
├── middleware/           # Authentication middleware
│   └── auth.php
├── models/              # Data models
│   ├── User.php
│   └── Payment.php
├── pages/               # Application pages
│   ├── checkout_new.php
│   ├── stk_new.php
│   ├── upgrade_new.php
│   ├── payment_history.php
│   └── profile.php
├── services/            # Business logic
│   └── PaymentService.php
├── webhook/             # Payment callbacks
│   └── callback.php
├── .env.example         # Environment template
├── .gitignore          # Git ignore rules
└── app.php             # Main application
```

## Security Improvements

### Fixed Vulnerabilities
1. **Exposed Credentials** - Moved to .env file (gitignored)
2. **Payment Verification** - Added signature validation
3. **Session Security** - Implemented secure session handling
4. **Input Validation** - Server-side validation for all inputs
5. **Authentication** - Full user authentication system
6. **CSRF Protection** - Token-based CSRF prevention
7. **Error Handling** - Production-safe error messages
8. **Database Persistence** - All payments tracked in database

## Usage

### Making a Payment
1. Login to your account
2. Navigate to "STK Push" or "Checkout"
3. Enter payment details
4. Complete payment via M-Pesa or hosted page
5. View confirmation and payment history

### Upgrading to Premium
1. Go to "Mock Platform" page
2. Click "Try PRO"
3. Complete payment (500 KES)
4. Access unlocked premium features

## API Integration

The application uses IntaSend PHP SDK. Key services:

- `PaymentService` - Handles all payment operations
- `Checkout` - Creates hosted payment sessions
- `Collection` - Initiates STK push requests
- `Wallet` - Retrieves wallet information

## Development

### Environment Modes
- `development` - Shows detailed errors
- `production` - Hides errors, logs to file

### Logging
Activity logs stored in `logs/activity.log`

### Database Migrations
Run `database/schema.sql` to create/update tables

## License

MIT License - Free to use and modify

## Support

For IntaSend API documentation: https://developers.intasend.com/
