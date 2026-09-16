#!/bin/sh

php /var/www/artisan octane:stop >/proc/1/fd/1
rm -f /var/www/storage/framework/octane/octane-server-state.json /var/www/storage/logs/octane-server-state.json
php /var/www/artisan octane:start >/proc/1/fd/1
