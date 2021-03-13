web: vendor/bin/heroku-php-nginx -C nginx_app.conf -F fpm_custom.conf public/
worker: php artisan queue:restart && php artisan queue:work database --tries=3 --timeout=1200
