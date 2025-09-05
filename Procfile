web: php artisan optimize:for-production && php-fpm -D && caddy run --config Caddyfile --adapter caddyfile
worker: php artisan queue:work --sleep=3 --tries=3 --max-time=3600