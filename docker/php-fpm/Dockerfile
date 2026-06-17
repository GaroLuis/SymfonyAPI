FROM php:8.1-fpm

# Install software
RUN apt update && apt install -yq gnupg supervisor && \
apt update && apt install -yq libsodium-dev libpq-dev libicu-dev git vim libzip-dev zlib1g-dev zip inetutils-ping wget && \
docker-php-ext-install pdo_pgsql intl exif bcmath zip && \
curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && \
apt install symfony-cli && \
pecl install xdebug && \
printf "\n" | pecl install apcu && \
docker-php-ext-enable xdebug && \
docker-php-ext-enable apcu && \
apt clean && \
rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN ln -sf /usr/local/bin/php /usr/bin/php

# install composer
COPY --from=composer:2.3 /usr/bin/composer /usr/bin/composer

# SSH Server for development (PHPStorm)
RUN apt update \
    && apt install -yq \
        openssh-server

COPY ssh/insecure_id_rsa /tmp/id_rsa
COPY ssh/insecure_id_rsa.pub /tmp/id_rsa.pub

RUN mkdir -p /root/.ssh
RUN chmod 700 /root/.ssh
RUN cat /tmp/id_rsa.pub >> /root/.ssh/authorized_keys
RUN cat /tmp/id_rsa.pub >> /root/.ssh/id_rsa.pub
RUN cat /tmp/id_rsa >> /root/.ssh/id_rsa
RUN rm -f /tmp/id_rsa*
RUN chmod 644 /root/.ssh/authorized_keys /root/.ssh/id_rsa.pub
RUN chmod 400 /root/.ssh/id_rsa
RUN cp -rf /root/.ssh /var/www
RUN chown -R www-data:www-data /var/www/.ssh

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
