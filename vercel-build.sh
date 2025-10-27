#!/bin/bash

# Install Composer dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Create necessary directories
mkdir -p /tmp/storage/framework/{sessions,views,cache}
mkdir -p /tmp/storage/logs
mkdir -p /tmp/bootstrap/cache

# Set permissions (if needed)
chmod -R 755 /tmp/storage
chmod -R 755 /tmp/bootstrap/cache

echo "Vercel build completed successfully"
