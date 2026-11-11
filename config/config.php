<?php
// Configuration Management

class Config {
    private static $config = null;
    
    public static function load() {
        if (self::$config !== null) {
            return self::$config;
        }
        
        // Load .env file
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                }
            }
        }
        
        self::$config = [
            'intasend' => [
                'publishable_key' => self::env('INTASEND_PUBLISHABLE_KEY', ''),
                'secret_key' => self::env('INTASEND_SECRET_KEY', ''),
            ],
            'app' => [
                'env' => self::env('APP_ENV', 'development'),
                'url' => self::env('APP_URL', 'http://localhost'),
                'name' => self::env('APP_NAME', 'IntaSend Demo'),
            ],
            'db' => [
                'host' => self::env('DB_HOST', 'localhost'),
                'name' => self::env('DB_NAME', 'intasend_demo'),
                'user' => self::env('DB_USER', 'root'),
                'pass' => self::env('DB_PASS', ''),
            ],
            'session' => [
                'lifetime' => (int)self::env('SESSION_LIFETIME', 7200),
                'name' => self::env('SESSION_NAME', 'INTASEND_SESSION'),
            ],
        ];
        
        return self::$config;
    }
    
    public static function get($key, $default = null) {
        $config = self::load();
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    private static function env($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
    
    public static function isProduction() {
        return self::get('app.env') === 'production';
    }
}
