FROM wordpress:php8.3-fpm

ARG PLUGIN_REPO
ARG PLUGIN_NAME

ENV DEBIAN_FRONTEND=noninteractive
# Install required packages

RUN apt-get update && \
    apt-get install -y git unzip zip && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install WP-CLI
COPY --from=wordpress:cli /usr/local/bin/wp /usr/local/bin/wp

RUN php -d memory_limit=512M /usr/local/bin/wp core download --path=/var/www/html --allow-root

# Clone plugin
WORKDIR /var/www/html/wp-content/plugins
RUN git clone ${PLUGIN_REPO} ${PLUGIN_NAME}

# Install Composer dependencies
WORKDIR /var/www/html/wp-content/plugins/${PLUGIN_NAME}

RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction

