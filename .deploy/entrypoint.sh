#!/bin/bash
set -e

echo "Waiting for PostgreSQL..."
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME"; do
  sleep 2
done

echo "Running migrations..."
php artisan migrate --force --no-interaction

# Если нужно сеять данные (только при первом запуске или всегда — на твой выбор)
# php artisan db:seed --force --no-interaction

# Оптимизация (опционально)
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting services..."
service nginx start
exec php-fpm
