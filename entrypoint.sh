#!/bin/sh
set -e

echo "Starting JPVNK Application on Render..."

# Setup .env if not present
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# Create database directory and sqlite file with full read/write permissions for www-data
mkdir -p /var/www/html/database
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 777 /var/www/html/database
fi

# Ensure storage directories exist and have full write permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Ensure APP_KEY exists before running migrations or caches
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY is not set in environment. Generating application key..."
    php artisan key:generate --force || true
fi

# Link storage
php artisan storage:link --force || true

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force || true

# Auto-seed initial data
echo "Checking and seeding initial data..."
php artisan db:seed --force || true

# Clear and rebuild caches
echo "Optimizing application caches..."
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Crucial: Ensure www-data ownership and 777 permissions AFTER all artisan commands
echo "Setting final permissions for www-data..."
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/.env
chmod -R 777 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/.env

# Configure Apache Port from Render's $PORT env variable (default 80)
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

echo "Launching Apache Web Server on port $PORT..."
exec apache2-foreground
