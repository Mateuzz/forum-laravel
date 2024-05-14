#!/bin/sh

php artisan optimize

if [ $RUN_MIGRATIONS = true ]; then
    php artisan migrate
fi

if [ $RUN_DB_SEED = true ]; then
    php artisan db:seed
fi

echo "Info: Starting php-fpm"
php-fpm82
echo "Info: Php-Fpm live"

echo "Info: Starting nginx"
nginx -g "daemon off;"
