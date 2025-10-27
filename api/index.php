<?php

/**
 * Vercel Serverless Function Entry Point
 */

// Enable error reporting for debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Log function for debugging
function logDebug($message, $data = null) {
    error_log($message . ($data ? ': ' . json_encode($data) : ''));
}

try {
    logDebug('Starting Vercel serverless function');
    
    // Create necessary directories in /tmp (writable in Vercel)
    $dirs = [
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
        '/tmp/views'
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
            logDebug("Created directory: $dir");
        }
    }
    
    // Load .env file directly (committed to repo)
    $envFile = __DIR__ . '/../.env';
    logDebug('Looking for .env file', ['path' => $envFile, 'exists' => file_exists($envFile)]);

    if (file_exists($envFile)) {
        // Parse .env and set environment variables
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        logDebug('Parsing .env file', ['lines' => count($lines)]);
        
        $varsSet = 0;
        foreach ($lines as $line) {
            // Skip comments and empty lines
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            
            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Handle variable substitution ${VAR}
                if (preg_match('/\$\{([A-Z_]+)\}/', $value, $matches)) {
                    $varName = $matches[1];
                    if (isset($_ENV[$varName])) {
                        $value = str_replace('${' . $varName . '}', $_ENV[$varName], $value);
                    }
                }
                
                // Remove quotes if present
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                // Set environment variable
                if (!empty($key)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                    $varsSet++;
                }
            }
        }
        
        logDebug('Environment variables set', ['count' => $varsSet]);
        logDebug('Database config', [
            'DB_HOST' => $_ENV['DB_HOST'] ?? 'not set',
            'DB_DATABASE' => $_ENV['DB_DATABASE'] ?? 'not set',
            'APP_KEY' => isset($_ENV['APP_KEY']) ? 'set' : 'not set'
        ]);
    } else {
        throw new Exception('.env file not found at: ' . $envFile);
    }

    logDebug('Loading Laravel application');
    
    // Load Laravel application
    require __DIR__ . '/../public/index.php';
    
} catch (Throwable $e) {
    // Output error for debugging
    logDebug('Fatal error occurred', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString()),
        'env_file_exists' => file_exists(__DIR__ . '/../.env'),
        'php_version' => PHP_VERSION,
        'cwd' => getcwd(),
        'dir_contents' => array_slice(scandir(__DIR__ . '/..'), 0, 20)
    ], JSON_PRETTY_PRINT);
    exit(1);
}