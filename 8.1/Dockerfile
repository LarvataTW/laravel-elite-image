FROM php:8.1.20-fpm-alpine3.18

ENV PHPIZE_DEPS \
        g++ \
        make \
        autoconf \
        re2c \
        libmcrypt-dev \
        curl-dev \
        libxml2-dev \
        oniguruma-dev

# 安裝必要套件 && 安裝常用工具 && 安裝 composer && php 任務執行器
RUN apk update \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add --no-cache \
        libstdc++ \
        vim \
        bash \
        supervisor \
        nginx \
        libmcrypt \
        libzip-dev \
        zip \
    && pecl install \
        swoole \
        redis \
        mcrypt-1.0.6 \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
        zip \
        exif \
        opcache \
        pcntl \
    && docker-php-ext-enable \
        swoole \
        redis \
        mcrypt \
    && chmod -R 755 /usr/local/lib/php/extensions \
    && curl -s https://getcomposer.org/installer | php -- --quiet --install-dir=/usr/bin --filename=composer \
    && composer global require "laravel/envoy" \
    && chmod +x ~/.composer/vendor/bin/envoy && ln -s ~/.composer/vendor/bin/envoy /usr/bin/envoy \
    && apk del -f .build-deps \
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/* \
    && rm /etc/nginx/http.d/default.conf \
    && rm -rf /var/www/*

# Configure nginx
COPY config/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY config/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY config/php.ini /usr/local/etc/php/php.ini

# supervisord
COPY config/supervisord.conf /etc/supervisord.conf

COPY ./supervisord.d /etc/supervisord.d
COPY ./optional/octane /opt/octane

WORKDIR /var/www

CMD [ "envoy", "run", "production"]
