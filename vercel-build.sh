#!/bin/bash

# Copy .env.vercel to .env for production
if [ -f ".env.vercel" ]; then
    echo "Copying .env.vercel to .env..."
    cp .env.vercel .env
fi

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
