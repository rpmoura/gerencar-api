#!/bin/sh

set -e

echo "Start entrypoint"

if [ ! -e /var/www/.composer_installed ]; then
    echo "Install dependencies"
    cd /var/www/ && composer install --optimize-autoloader && touch .composer_installed
    echo "Finish install dependencies"
fi

if [ ! -e /var/www/.migrations_executed ]; then
    echo "Run migrates and seeders"
    cd /var/www/ && php artisan migrate --seed && touch .migrations_executed
    echo "Finish run migrates and seeders"
fi

if [ ! -e /var/www/.env ]; then
    echo "Copy .env and generate key"
    cd /var/www/ && cp .env.example .env && php artisan key:generate
    echo "Finish copy .env and generate key"
fi

echo "Finish entrypoint"

exec "$@"