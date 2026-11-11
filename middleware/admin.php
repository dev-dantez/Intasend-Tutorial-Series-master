<?php
// Admin Middleware

function require_admin() {
    require_auth();
    
    if (!SessionManager::get('is_admin', false)) {
        // Show proper error page instead of plain text
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html data-theme="forest" lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Access Denied</title>
            <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-base-300">
            <div class="container mx-auto p-8 max-w-2xl mt-20">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body text-center">
                        <div class="text-error mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="card-title text-3xl justify-center mb-4">Access Denied</h2>
                        <p class="text-lg mb-6">Admin privileges required to access this page.</p>
                        
                        <div class="alert alert-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="text-left">
                                <p class="font-bold">Need admin access?</p>
                                <p class="text-sm">Visit <code>/admin/make_admin.php</code> to make yourself an admin, then logout and login again.</p>
                            </div>
                        </div>
                        
                        <div class="card-actions justify-center mt-6 gap-4">
                            <a href="/admin/make_admin.php" class="btn btn-primary">Make Me Admin</a>
                            <a href="/app.php" class="btn btn-ghost">Back to Site</a>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

function is_admin() {
    return SessionManager::get('is_admin', false);
}
