<?php

// Enable error reporting for debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

try {
    define('LARAVEL_START', microtime(true));

    // Ensure /tmp directory exists and is writable
    $tmpDirs = ['/tmp', '/tmp/views', '/tmp/cache', '/tmp/sessions'];
    foreach ($tmpDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    // Determine if the application is in maintenance mode...
    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // Register the Composer autoloader...
    if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
        die('Vendor autoload not found. Please run: composer install');
    }
    require __DIR__.'/../vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
    
} catch (\Throwable $e) {
    // Catch any errors and display them
    http_response_code(500);
    echo '<h1>Application Error</h1>';
    echo '<pre>';
    echo 'Message: ' . $e->getMessage() . "\n\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
    echo 'Trace:' . "\n" . $e->getTraceAsString();
    echo '</pre>';
}