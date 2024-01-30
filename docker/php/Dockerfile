FROM php:8.2.13-fpm

RUN apt-get update && apt-get upgrade -y \
    libonig-dev \
    libmcrypt-dev \
    zlib1g-dev \
    libpng-dev \
    libxml2-dev

## ZIP
RUN apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

## PDO
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo pdo_mysql

## LIBS
RUN docker-php-ext-install dom
RUN docker-php-ext-install intl
RUN docker-php-ext-install iconv
RUN docker-php-ext-install opcache
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install gd

## XDEBUG
RUN pecl install xdebug-3.2.2
RUN docker-php-ext-enable xdebug
RUN echo "xdebug.mode=develop,debug,coverage\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.start_with_request=yes\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.client_port=9000\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_handler=dbgp\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.max_nesting_level=1024\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.discover_client_host=1\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey=PHPSTORM\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.show_error_trace=1\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

## REDIS
RUN pecl install redis
RUN echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini
RUN echo "date.timezone=America/Sao_Paulo" > /usr/local/etc/php/conf.d/timezone_sao_paulo.ini
RUN echo "memory_limit = 1024M" > /usr/local/etc/php/conf.d/memory_limit.ini

## COMPOSER
COPY --from=composer:2.6.5 /usr/bin/composer /usr/local/bin/composer

## GIT
RUN apt install git -y

## PHP HTTP
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && install-php-extensions http

WORKDIR /var/www

RUN useradd -G www-data,root -ms /bin/bash -d /home/dev dev

RUN chown -R dev:www-data /var/www

COPY ./docker-entrypoint.sh /usr/local/bin/

USER dev

EXPOSE 9000
CMD ["php-fpm"]