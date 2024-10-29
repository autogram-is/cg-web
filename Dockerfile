# Use the official WordPress image as the base
FROM wordpress:latest

# Install dependencies
RUN apt-get update && apt-get install -y \
    less \
    mariadb-client \
    unzip \
    wget

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Xdebug from source as described here:
# https://xdebug.org/docs/install
# Available branches of Xdebug could be seen here:
# https://github.com/xdebug/xdebug/branches
RUN cd /tmp && \
    git clone https://github.com/xdebug/xdebug.git && \
    cd xdebug && \
    git checkout xdebug_3_3 && \
    phpize && \
    ./configure --enable-xdebug && \
    make && \
    make install && \
    rm -rf /tmp/xdebug

# Since this Dockerfile extends the official Docker image `wordpress`,
# and since `wordpress`, in turn, extends the official Docker image `php`,
# the helper script docker-php-ext-enable (defined for image `php`)
# works here, and we can use it to enable xdebug:
RUN docker-php-ext-enable xdebug

# Set the working directory
WORKDIR /var/www/html

# Copy your custom wp-config.php if needed
# COPY wp-config.php /var/www/html/wp-config.php

# Copy xdebug.ini if needed
# COPY wp-config.php /usr/local/etc/php/conf.d/xdebug.ini
to /usr/local/etc/php/conf.d/
COPY files-to-copy/ /

# Copy your custom entrypoint script if needed
# COPY docker-entrypoint.sh /usr/local/bin/

# Make sure the entrypoint script is executable
# RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Use the default entrypoint from the WordPress image
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]