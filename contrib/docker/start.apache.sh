#!/bin/bash

# Create storage tree if needed and fix permissions
cp -r storage.skel/* storage/
chown -R www-data:www-data storage/ bootstrap/

# Refresh environment
php artisan route:cache
php artisan view:cache
php artisan config:cache

# Run Apache in foreground
apache2-foreground