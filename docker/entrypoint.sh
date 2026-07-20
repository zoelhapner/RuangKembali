#!/bin/sh
set -e

echo "=== Fixing storage permission (runtime) ==="

mkdir -p /var/www/storage/app/public
mkdir -p /var/www/storage/logs
touch /var/www/storage/logs/laravel.log
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

exec "$@"
