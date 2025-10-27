#!/bin/bash

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

echo "Build completed successfully"
