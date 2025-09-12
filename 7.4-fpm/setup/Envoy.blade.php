@servers(['web' => '127.0.0.1'])

@task('production')
{{--OPCache設定--}}
sed -i 's/opcache.validate_timestamps=1/opcache.validate_timestamps=0/g' /usr/local/etc/php/php.ini
{{--基本設定--}}
php artisan storage:link
php artisan route:cache
php artisan view:cache
{{--啟動設定--}}
supervisord -c /etc/supervisord.conf
supervisorctl start laravel-php-fpm:*
supervisorctl start laravel-nginx:*

tail -f /dev/null
@endtask

@task('local')
{{--啟動設定--}}
supervisord -c /etc/supervisord.conf
supervisorctl start laravel-php-fpm:*
supervisorctl start laravel-nginx:*

tail -f /dev/null
@endtask

@task('laravel-setting')
php artisan migrate
php artisan storage:link
php artisan route:cache
php artisan view:cache
@endtask

@task('nginx')
supervisorctl start laravel-nginx:*
@endtask

@task('nginx-swoole')
supervisorctl start laravel-nginx-swoole:*
@endtask

@task('php-fpm')
supervisorctl start laravel-php-fpm:*
@endtask

@task('swoole')
supervisorctl start laravel-swoole:*
@endtask

@task('schedule')
php /var/www/artisan schedule:run >> /dev/null 2>&1;
@endtask

@task('worker')
supervisorctl start laravel-worker:*
@endtask