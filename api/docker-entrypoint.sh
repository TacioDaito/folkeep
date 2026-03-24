#!/bin/sh

# Run migrations
php artisan migrate --force

# Cache config (optional but recommended for speed)
php artisan config:cache

# Start the main process
exec php artisan serve --host=0.0.0.0 --port=8000
