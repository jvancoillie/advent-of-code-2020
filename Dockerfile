FROM php:7.3-fpm

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN mkdir -p /var/www/advent
WORKDIR /var/www/advent
COPY . /var/www/advent

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

ADD php/xdebug.ini /usr/local/etc/php/conf.d/

CMD ["php-fpm"]

