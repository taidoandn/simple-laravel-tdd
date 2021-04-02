FROM php:7.4-fpm

RUN mkdir -p /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y vim curl git zip unzip supervisor \
    && curl -sL https://deb.nodesource.com/setup_15.x | bash - \
    && apt-get -y install nodejs \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Remove Cache
RUN apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql

# SUPERVISOR
COPY .docker/supervisor.d /etc/supervisor/supervisor.d
COPY .docker/supervisord.conf /etc/supervisor/supervisord.conf

# Add user for laravel application
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory permissions
COPY . .

RUN chown -R www:www .

COPY .docker/start-entrypoint.sh /start.sh
RUN chmod 755 /start.sh

# Change current user to www
USER www

CMD ["/start.sh"]