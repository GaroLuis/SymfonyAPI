FROM php:8.1-fpm

# Install software
RUN apt-get update && apt-get install -yq gnupg supervisor && \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && apt-get install -yq libsodium-dev libpq-dev libicu-dev git vim libzip-dev zlib1g-dev zip inetutils-ping yarn wget && \
    docker-php-ext-install pdo_pgsql intl exif bcmath zip && \
    wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony && \
    pecl install xdebug && \
    printf "\n" | pecl install apcu && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-enable apcu && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN ln -sf /usr/local/bin/php /usr/bin/php

# install composer
COPY --from=composer:2.3 /usr/bin/composer /usr/bin/composer

# SSH Server for development (PHPStorm)
RUN apt-get update \
    && apt-get install -yq \
        openssh-server

COPY ssh/insecure_id_rsa /tmp/id_rsa
COPY ssh/insecure_id_rsa.pub /tmp/id_rsa.pub

RUN mkdir /var/run/sshd && \
    mkdir /root/.ssh && \
    chmod 700 /root/.ssh && \
    cat /tmp/id_rsa.pub >> /root/.ssh/authorized_keys && \
    cat /tmp/id_rsa.pub >> /root/.ssh/id_rsa.pub && \
    cat /tmp/id_rsa >> /root/.ssh/id_rsa && \
    rm -f /tmp/id_rsa* && \
    chmod 644 /root/.ssh/authorized_keys /root/.ssh/id_rsa.pub && \
    chmod 400 /root/.ssh/id_rsa && \
    cp -rf /root/.ssh /var/www && \
    chown -R www-data:www-data /var/www/.ssh

RUN chsh -s /bin/bash www-data

# configure PHP and debugging command
COPY php.ini /usr/local/etc/php/conf.d/

WORKDIR /var/www/symfony-api
VOLUME /var/www/symfony-api

EXPOSE 9000

RUN usermod -u 1000 www-data && \
    groupmod -g 1000 www-data && \
    chown www-data:www-data /var/www

# Run

ADD supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
