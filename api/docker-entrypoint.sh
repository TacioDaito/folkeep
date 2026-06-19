#!/bin/sh

# Copy .env.example to .env if .env doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo ".env created from .env.example"
fi

# Generate app key if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Cache config (optional but recommended for speed)
php artisan config:cache

# Start the main process
exec php artisan serve --host=0.0.0.0 --port=8000
