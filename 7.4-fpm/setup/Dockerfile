FROM larvata/laravel-elite-image:latest-php7.4

# # 圖片處理
# RUN apk update \
#     && apk add --no-cache --virtual .build-deps g++ make autoconf libtool libpng-dev pcre-dev \
#     && apk add --no-cache freetype-dev libpng jpeg-dev imagemagick imagemagick-dev \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install gd \
#     && pecl install imagick-3.4.4 \
#     && docker-php-ext-enable imagick \
#     && apk del -f .build-deps \
#     && rm -rf /var/cache/apk/* \
#     && rm -rf /tmp/*

# # JavaScript 處理
# RUN apk update \
#     && apk add --no-cache nodejs yarn \
#     && rm -rf /var/cache/apk/* \
#     && rm -rf /tmp/*

# Composer install
COPY composer.* /tmp/
RUN cd /tmp && COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --no-autoloader

# Yarn install
# COPY package.json yarn.* /tmp/
# RUN cd /tmp && yarn --ignore-engines install

# Copy project
COPY ./ /var/www

# Composer load && Yarn build
# RUN rm -rf /var/www/vendor && rm -rf /var/www/node_modules && mv /tmp/vendor/ /var/www && mv /tmp/node_modules/ /var/www && composer dump-autoload && yarn production

# Composer load
RUN rm -rf /var/www/vendor && mv /tmp/composer.lock /var/www/composer.lock && mv /tmp/vendor/ /var/www && composer dump-autoload