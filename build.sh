#!/usr/bin/env bash
# exit on error
set -o errexit

composer install --no-dev --optimize-autoloader

# Create SQLite database if it doesn't exist (useful for persistent disks)
mkdir -p /var/lib/sqlite
touch /var/lib/sqlite/database.sqlite

npm install
npm run build

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
