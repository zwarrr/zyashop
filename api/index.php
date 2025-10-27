<?php

/**
 * Vercel Serverless Function Entry Point
 * Environment variables are set in vercel.json
 */

// Create necessary directories in /tmp
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
    }
}

// Load Laravel application
require __DIR__ . '/../public/index.php';