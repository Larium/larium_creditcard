FROM php:8.1-cli
WORKDIR "/opt/php"

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && \
    apt-get install -y \
    unzip \
    git && \
    rm -rf /var/lib/apt/lists/*

RUN pecl channel-update pecl.php.net && \
    pecl install xdebug && \
    docker-php-source delete && \
    rm -r /tmp/* /var/cache/*

COPY ./.docker/php-ini-overrides.ini /usr/local/etc/php/conf.d/99-overrides.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && chmod +x composer.phar \
    && mv composer.phar /usr/local/bin/composer