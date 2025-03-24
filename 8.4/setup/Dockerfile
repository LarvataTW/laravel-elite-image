FROM larvata/laravel-elite-image:latest-php8.3

ARG INSTALL_NPM=false
RUN if [ ${INSTALL_NPM} = true ]; then \
  apk add --update nodejs npm \
;fi

## 圖片處理
#RUN apk update \
#    && apk add --no-cache --virtual .build-deps g++ make autoconf libtool libpng-dev pcre-dev \
#    && apk add --no-cache libgomp freetype-dev libpng jpeg-dev imagemagick imagemagick-dev \
#    && docker-php-ext-configure gd --with-freetype --with-jpeg \
#    && docker-php-ext-install gd \
#    && pecl install imagick-3.5.1 \
#    && docker-php-ext-enable imagick \
#    && apk del -f .build-deps \
#    && rm -rf /var/cache/apk/* \
#    && rm -rf /tmp/*

# Composer install
COPY composer.* /tmp/
RUN cd /tmp && COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --no-autoloader

# Copy project
COPY --chown=www-data:www-data ./ /var/www

# Composer load
RUN rm -rf /var/www/vendor && mv /tmp/composer.lock /var/www/composer.lock && mv /tmp/vendor/ /var/www && composer dump-autoload