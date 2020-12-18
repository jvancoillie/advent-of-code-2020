FROM php:7.3-fpm

RUN apt-get update -y
RUN apt-get install -y libgmp-dev re2c libmhash-dev libmcrypt-dev file
RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/
RUN docker-php-ext-configure gmp
RUN docker-php-ext-install gmp

#RUN pecl install xdebug
#RUN docker-php-ext-enable xdebug

RUN mkdir -p /var/www/advent
WORKDIR /var/www/advent
COPY . /var/www/advent

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

ADD php/xdebug.ini /usr/local/etc/php/conf.d/

CMD ["php-fpm"]

