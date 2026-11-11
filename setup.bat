@echo off
echo ========================================
echo IntaSend PHP Project Setup
echo ========================================
echo.

echo Step 1: Checking PHP...
php -v
if errorlevel 1 (
    echo ERROR: PHP not found. Please install PHP 7.4 or higher.
    pause
    exit /b 1
)
echo.

echo Step 2: Checking Composer...
composer --version
if errorlevel 1 (
    echo ERROR: Composer not found. Please install Composer.
    pause
    exit /b 1
)
echo.

echo Step 3: Installing dependencies...
composer install
if errorlevel 1 (
    echo ERROR: Failed to install dependencies.
    pause
    exit /b 1
)
echo.

echo Step 4: Creating logs directory...
if not exist "logs" mkdir logs
echo Logs directory created.
echo.

echo Step 5: Checking .env file...
if not exist ".env" (
    echo .env file already exists. Skipping copy.
) else (
    echo .env file found.
)
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Edit .env file with your credentials
echo 2. Create MySQL database: intasend_demo
echo 3. Import schema: mysql -u root -p intasend_demo ^< database/schema.sql
echo 4. Visit: http://localhost/your-project/install.php
echo 5. Register at: http://localhost/your-project/auth/register.php
echo.
echo Press any key to open installation checker...
pause
start http://localhost/install.php
