<?php

/**
 * Vercel Serverless Function Entry Point
 */

// Vercel filesystem is read-only, so we load .env.vercel directly
$envVercel = __DIR__ . '/../.env.vercel';

if (file_exists($envVercel)) {
    // Parse .env.vercel and set environment variables
    $lines = file($envVercel, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            // Set environment variable
            if (!empty($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Load Laravel application
require __DIR__ . '/../public/index.php';