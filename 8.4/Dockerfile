# --- Build stage: 只用來編譯與安裝 PHP Extensions、Composer 工具 ---
FROM php:8.4.19-fpm-alpine3.22 AS build

# DB_TYPE: mysql (預設) 或 pgsql
ARG DB_TYPE=mysql

# Build dependencies (legacy ENV style replaced with key=value)
# Optional/disabled items: re2c, curl-dev, oniguruma-dev
ENV PHPIZE_DEPS="g++ make autoconf zlib-dev libxml2-dev libpng-dev freetype-dev jpeg-dev libwebp-dev openssl-dev"

WORKDIR /var/www
# 安裝 build 工具與 PHP 擴充
RUN apk update && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
        && apk add --no-cache libzip-dev zip  \
        && if [ "$DB_TYPE" = "pgsql" ]; then apk add --no-cache postgresql-dev; fi \
        && pecl install \
                swoole \
                redis \
        && docker-php-ext-configure opcache --enable-opcache \
        && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
        && if [ "$DB_TYPE" = "pgsql" ]; then \
                docker-php-ext-install pdo_pgsql pgsql; \
           else \
                docker-php-ext-install pdo_mysql mysqli; \
           fi \
        && docker-php-ext-install \
                zip \
                exif \
                opcache \
                pcntl \
                gd \
        && docker-php-ext-enable \
                swoole \
                redis



# 安裝 Composer 與 Envoy
RUN curl -s https://getcomposer.org/installer | php -- --quiet --install-dir=/usr/bin --filename=composer \
        && composer global require "laravel/envoy" "symfony/console:^7.0" "symfony/process:^7.0" \
        && chmod +x ~/.composer/vendor/bin/envoy && ln -s ~/.composer/vendor/bin/envoy /usr/bin/envoy


# 複製 PHP extensions 和 Composer 到下一層
RUN mkdir -p /build/extensions /build/composer 
RUN cp -r /usr/local/lib/php/extensions /build/
RUN cp -r /usr/bin/composer /build/composer/
RUN cp -r ~/.composer /build/composer/.composer

# --- Final stage: 真正執行環境 ---
FROM php:8.4.19-fpm-alpine3.22

# 繼承 build arg
ARG DB_TYPE=mysql

# 安裝基本工具（無需 build 工具）
RUN apk add --no-cache \
                supervisor \
                nginx \
                libpng \
                libwebp \
                libjpeg-turbo \
                freetype \
                libzip \
                libexif \
        && if [ "$DB_TYPE" = "pgsql" ]; then apk add --no-cache libpq; fi

# 修補 CVE-2025-15467 (OpenSSL RCE)
RUN apk upgrade --no-cache libcrypto3 libssl3 openssl


# 複製 build 階段安裝好的 PHP extensions 與 Composer 工具
COPY --from=build /build/extensions /usr/local/lib/php/extensions
COPY --from=build /build/composer/composer /usr/bin/composer
COPY --from=build /build/composer/.composer /root/.composer
COPY --from=build /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini /usr/local/etc/php/conf.d/
RUN  ln  -s  ~/.composer/vendor/bin/envoy /usr/bin/envoy

# nginx、PHP-FPM、Supervisor 設定檔
COPY config/nginx.conf /etc/nginx/nginx.conf
COPY config/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY config/php.ini /usr/local/etc/php/php.ini
# pgsql 變體：切換 php.ini 中的 DB 擴充宣告，避免載入未編譯的 mysql 擴充
RUN if [ "$DB_TYPE" = "pgsql" ]; then \
        sed -i \
            -e 's/^extension=pdo_mysql$/;extension=pdo_mysql/' \
            -e 's/^extension=mysqli$/;extension=mysqli/' \
            -e 's/^;extension=pdo_pgsql$/extension=pdo_pgsql/' \
            -e 's/^;extension=pgsql$/extension=pgsql/' \
            /usr/local/etc/php/php.ini; \
    fi
COPY config/supervisord.conf /etc/supervisord.conf
COPY ./supervisord.d /etc/supervisord.d
COPY ./optional/octane /opt/octane

# 清理預設內容
RUN rm -rf /var/cache/apk/* \
        && rm /etc/nginx/http.d/default.conf \
        && rm -rf /var/www/*


WORKDIR /var/www

CMD ["envoy", "run", "production"]
