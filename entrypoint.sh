#!/bin/sh
set -e

echo "Starting JPVNK Application on Render..."

# Create database.sqlite if using SQLite and file does not exist
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
        chown www-data:www-data /var/www/html/database/database.sqlite
    fi
fi

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Link storage
php artisan storage:link --force || true

# Run database migrations
echo "Running migrations..."
php artisan migrate --force || true

# Cache configurations, routes, and views for speed
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Configure Apache Port from Render's $PORT env variable (default 80)
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

echo "Launching Apache Web Server on port $PORT..."
exec apache2-foreground
