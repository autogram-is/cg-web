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

# Set the working directory
WORKDIR /var/www/html

# Copy your custom wp-config.php if needed
# COPY wp-config.php /var/www/html/wp-config.php

# Copy your custom entrypoint script if needed
# COPY docker-entrypoint.sh /usr/local/bin/

# Make sure the entrypoint script is executable
# RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Use the default entrypoint from the WordPress image
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]