FROM php:8.3-fpm-alpine

RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql opcache

# Install composer
COPY --from=composer:2.1 /usr/bin/composer /usr/local/bin/composer
RUN composer --version

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

RUN rm -rf /tmp/pear

# Import custom php.ini
COPY ./php.ini /usr/local/etc/php/conf.d

WORKDIR /var/www

RUN export DOCKER_SCAN_SUGGEST=false