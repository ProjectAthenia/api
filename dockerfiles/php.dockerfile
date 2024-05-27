FROM php:8-fpm-bookworm

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
RUN delgroup dialout

# Setup users and enable sudo
RUN apt update && \
      apt -y install sudo

RUN adduser laravel
RUN adduser laravel sudo
RUN echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

RUN docker-php-ext-install pdo pdo_mysql

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

RUN apt update  \
    && apt install -y libmagickwand-dev --no-install-recommends
# The oficial install does not work right now, but the next two lines are the official install
#    && pecl install imagick \
#    && docker-php-ext-enable imagick
# This is temporary until the official install is fixed
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions imagick/imagick@master

RUN apt update \
    && apt install -y libpng-dev \
    && docker-php-ext-install gd

RUN apt-get -y update \
    && apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN apt update \
    && apt install -y libzip-dev zip \
    && docker-php-ext-install zip

RUN apt update \
    && apt install -y libonig-dev \
    && docker-php-ext-install mbstring

RUN apt update \
    && apt install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl
RUN docker-php-ext-install xml

ADD ./php/php.ini /usr/local/etc/php/conf.d/custom-php.ini

USER laravel

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
