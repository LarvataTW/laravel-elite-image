@servers(['web' => '127.0.0.1'])

@task('production')
supervisord -c /etc/supervisord.conf
{{--基本設定--}}
php artisan storage:link
php artisan route:cache
php artisan view:cache
{{--啟動設定--}}
supervisorctl start laravel-schedule:*
supervisorctl start laravel-worker:*
supervisorctl start laravel-php-fpm:*
supervisorctl start laravel-octane:*
supervisorctl start laravel-nginx:*

tail -f /dev/null
@endtask

@task('local')
supervisord -c /etc/supervisord.conf
{{--啟動設定--}}
{{--supervisorctl start laravel-schedule:*--}}
{{--supervisorctl start laravel-worker:*--}}
supervisorctl start laravel-php-fpm:*
supervisorctl start laravel-octane-dev:*
supervisorctl start laravel-nginx:*

tail -f /dev/null
@endtask
