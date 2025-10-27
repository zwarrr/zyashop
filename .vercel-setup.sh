#!/bin/sh

# Create necessary writable directories in /tmp for Vercel
mkdir -p /tmp/storage/framework/{sessions,views,cache/data}
mkdir -p /tmp/storage/logs
mkdir -p /tmp/bootstrap/cache
mkdir -p /tmp/views

# Set permissions
chmod -R 775 /tmp/storage
chmod -R 775 /tmp/bootstrap
chmod -R 775 /tmp/views

echo "Vercel runtime directories created"
