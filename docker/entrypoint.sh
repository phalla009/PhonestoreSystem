#!/bin/sh

# Wait until MySQL is healthy
echo "Waiting for MySQL to be ready..."
until nc -z -v -w30 db 3306
do
  echo "Still waiting for MySQL..."
  sleep 5
done

echo "MySQL is ready! Running Laravel commands..."

# Clear caches and run migrations
php artisan config:clear
php artisan cache:clear
php artisan migrate --force

# Start Laravel
exec php artisan serve --host=0.0.0.0 --port=8000
