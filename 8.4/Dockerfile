# --- Build stage: 只用來編譯與安裝 PHP Extensions、Composer 工具 ---
FROM php:8.4.5-fpm-alpine3.21 AS build

ENV PHPIZE_DEPS \
        g++ \
        make \
        autoconf \
        re2c \
        libmcrypt-dev \
        curl-dev \
        libxml2-dev \
        oniguruma-dev

# 安裝 build 工具與 PHP 擴充
RUN apk update && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
        && apk add --no-cache libzip-dev zip libmcrypt \
        && pecl install \
                swoole \
                redis \
                mcrypt-1.0.7 \
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
                redis

# 安裝 Composer 與 Envoy
RUN curl -s https://getcomposer.org/installer | php -- --quiet --install-dir=/usr/bin --filename=composer \
        && composer global require "laravel/envoy" \
        && chmod +x ~/.composer/vendor/bin/envoy && ln -s ~/.composer/vendor/bin/envoy /usr/bin/envoy

# 複製 PHP extensions 和 Composer 到下一層
RUN mkdir -p /build/extensions /build/composer
RUN cp -r /usr/local/lib/php/extensions /build/extensions/
RUN cp -r /usr/bin/composer /build/composer/
RUN cp -r ~/.composer /build/composer/.composer
# RUN cp -P /usr/bin/envoy /build/composer/envoy

# --- Final stage: 真正執行環境 ---
FROM php:8.4.5-fpm-alpine3.21

# 安裝基本工具（無需 build 工具）
RUN apk add --no-cache \
                supervisor \
                nginx \
                zip

# 複製 build 階段安裝好的 PHP extensions 與 Composer 工具
COPY --from=build /build/extensions /usr/local/lib/php/extensions
COPY --from=build /build/composer/composer /usr/bin/composer
COPY --from=build /build/composer/.composer /root/.composer
# COPY --from=build /build/composer/envoy /usr/bin/envoy
# COPY --from=build /usr/bin/envoy /usr/bin/envoy2
RUN  ln  -s  ~/.composer/vendor/bin/envoy /usr/bin/envoy

# nginx、PHP-FPM、Supervisor 設定檔
COPY config/nginx.conf /etc/nginx/nginx.conf
COPY config/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY config/php.ini /usr/local/etc/php/php.ini
COPY config/supervisord.conf /etc/supervisord.conf
COPY ./supervisord.d /etc/supervisord.d
COPY ./optional/octane /opt/octane

# 清理預設內容
RUN rm -rf /var/cache/apk/* \
        && rm /etc/nginx/http.d/default.conf \
        && rm -rf /var/www/*

WORKDIR /var/www

CMD ["envoy", "run", "production"]