#!/bin/bash
set -e

echo "Deployment started..."

(php artisan down) || true

git pull origin production

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

php artisan clear-compiled

php artisan optimize

npm run prod

php artisan migrate --force

php artisan up

echo "Deployment finished!"
