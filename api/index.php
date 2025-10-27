<?php

/**
 * Vercel Serverless Function Entry Point
 */

// Check if .env exists, if not copy from .env.vercel
$envFile = __DIR__ . '/../.env';
$envVercel = __DIR__ . '/../.env.vercel';

if (!file_exists($envFile) && file_exists($envVercel)) {
    copy($envVercel, $envFile);
}

// Load Laravel application
require __DIR__ . '/../public/index.php';