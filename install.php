<!DOCTYPE html>
<html data-theme="forest" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntaSend - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-base-300">
    <div class="container mx-auto p-8 max-w-4xl">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-2">IntaSend Installation</h1>
            <p class="opacity-70">Follow these steps to set up your application</p>
        </div>

        <?php
        $errors = [];
        $success = [];
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            $errors[] = "PHP 7.4 or higher required. Current: " . PHP_VERSION;
        } else {
            $success[] = "PHP version: " . PHP_VERSION;
        }
        
        // Check required extensions
        $required_extensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'mbstring'];
        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $errors[] = "Missing PHP extension: $ext";
            } else {
                $success[] = "Extension loaded: $ext";
            }
        }
        
        // Check .env file
        if (!file_exists('.env')) {
            $errors[] = ".env file not found. Copy .env.example to .env";
        } else {
            $success[] = ".env file exists";
        }
        
        // Check vendor directory
        if (!is_dir('vendor')) {
            $errors[] = "Vendor directory not found. Run: composer install";
        } else {
            $success[] = "Composer dependencies installed";
        }
        
        // Check logs directory
        if (!is_dir('logs')) {
            if (mkdir('logs', 0755, true)) {
                $success[] = "Created logs directory";
            } else {
                $errors[] = "Could not create logs directory";
            }
        } else {
            $success[] = "Logs directory exists";
        }
        
        // Check database connection
        if (file_exists('.env')) {
            require_once 'config/config.php';
            Config::load();
            
            try {
                $host = Config::get('db.host');
                $dbname = Config::get('db.name');
                $user = Config::get('db.user');
                $pass = Config::get('db.pass');
                
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                $success[] = "Database connection successful";
                
                // Check if tables exist
                $tables = ['users', 'payments', 'generated_profiles', 'activity_logs', 'webhook_events'];
                $stmt = $pdo->query("SHOW TABLES");
                $existing_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                $missing_tables = array_diff($tables, $existing_tables);
                if (!empty($missing_tables)) {
                    $errors[] = "Missing database tables: " . implode(', ', $missing_tables) . ". Run database/schema.sql";
                } else {
                    $success[] = "All database tables exist";
                }
                
            } catch (PDOException $e) {
                $errors[] = "Database connection failed: " . $e->getMessage();
            }
        }
        
        // Check IntaSend credentials
        if (file_exists('.env')) {
            $pub_key = Config::get('intasend.publishable_key');
            $secret_key = Config::get('intasend.secret_key');
            
            if (empty($pub_key) || empty($secret_key)) {
                $errors[] = "IntaSend API credentials not configured in .env";
            } else {
                $success[] = "IntaSend API credentials configured";
            }
        }
        ?>

        <!-- Success Messages -->
        <?php if (!empty($success)): ?>
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <h2 class="card-title text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Checks Passed
                    </h2>
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($success as $msg): ?>
                            <li class="text-sm"><?php echo htmlspecialchars($msg); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="card bg-base-100 shadow-xl mb-6 border-2 border-error">
                <div class="card-body">
                    <h2 class="card-title text-error">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Issues Found
                    </h2>
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $msg): ?>
                            <li class="text-sm text-error"><?php echo htmlspecialchars($msg); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Installation Steps -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Installation Steps</h2>
                <div class="space-y-4">
                    <div class="collapse collapse-arrow bg-base-200">
                        <input type="checkbox" checked /> 
                        <div class="collapse-title font-medium">
                            1. Install Composer Dependencies
                        </div>
                        <div class="collapse-content"> 
                            <pre class="bg-base-300 p-4 rounded text-sm">composer install</pre>
                        </div>
                    </div>

                    <div class="collapse collapse-arrow bg-base-200">
                        <input type="checkbox" /> 
                        <div class="collapse-title font-medium">
                            2. Create Database
                        </div>
                        <div class="collapse-content"> 
                            <pre class="bg-base-300 p-4 rounded text-sm">mysql -u root -p
CREATE DATABASE intasend_demo;
exit;

mysql -u root -p intasend_demo < database/schema.sql</pre>
                        </div>
                    </div>

                    <div class="collapse collapse-arrow bg-base-200">
                        <input type="checkbox" /> 
                        <div class="collapse-title font-medium">
                            3. Configure Environment
                        </div>
                        <div class="collapse-content"> 
                            <pre class="bg-base-300 p-4 rounded text-sm">cp .env.example .env
# Edit .env with your credentials</pre>
                        </div>
                    </div>

                    <div class="collapse collapse-arrow bg-base-200">
                        <input type="checkbox" /> 
                        <div class="collapse-title font-medium">
                            4. Set Permissions
                        </div>
                        <div class="collapse-content"> 
                            <pre class="bg-base-300 p-4 rounded text-sm">mkdir logs
chmod 755 logs</pre>
                        </div>
                    </div>
                </div>

                <?php if (empty($errors)): ?>
                    <div class="card-actions justify-end mt-6">
                        <a href="auth/register.php" class="btn btn-primary">
                            Continue to Registration
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mt-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Please fix the issues above before continuing</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
