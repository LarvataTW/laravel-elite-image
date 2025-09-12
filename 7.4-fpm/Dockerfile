FROM php:7.4-fpm

ENV PHPIZE_DEPS \
    g++ \
    make \
    autoconf \
    re2c \
    libmcrypt-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    libonig-dev

# 安裝必要套件 && 安裝常用工具 && 安裝 composer && php 任務執行器
RUN apt-get update \
    && apt-get install -y --no-install-recommends $PHPIZE_DEPS \
    && apt-get install -y --no-install-recommends \
        libstdc++6 \
        vim \
        bash \
        supervisor \
        nginx \
        libmcrypt-dev \
        libzip-dev \
        zip \
    && pecl install \
        swoole \
        redis \
        mcrypt-1.0.4 \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install \
        curl \
        iconv \
        mbstring \
        pdo \
        pdo_mysql \
        mysqli \
        tokenizer \
        xml \
        zip \
        exif \
        opcache \
    && docker-php-ext-enable \
        swoole \
        redis \
        mcrypt \
    && chmod -R 755 /usr/local/lib/php/extensions \
    && curl -s https://getcomposer.org/installer | php -- --quiet --install-dir=/usr/bin --filename=composer \
    && composer global require "laravel/envoy" \
    && chmod +x ~/.composer/vendor/bin/envoy && ln -s ~/.composer/vendor/bin/envoy /usr/bin/envoy \
    && apt-get remove --purge -y $PHPIZE_DEPS \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && rm -rf /var/www/*

# Configure nginx
COPY config/nginx.conf /etc/nginx/nginx.conf
COPY config/nginx-swoole.conf /etc/nginx/nginx-swoole.conf

# Configure PHP-FPM
COPY config/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY config/php.ini /usr/local/etc/php/php.ini

# supervisord
COPY config/supervisord.conf /etc/supervisord.conf
COPY ./supervisord.d /etc/supervisord.d

WORKDIR /var/www

CMD [ "envoy", "run", "production"]