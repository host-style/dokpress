FROM php:8.4-cli-alpine

# Start as root
USER root

# Install development and deployment tools + WordPress dependencies
RUN apk add --no-cache \
    bash \
    git \
    curl \
    curl-dev \
    unzip \
    zip \
    su-exec \
    imagemagick \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    libwebp \
    libxml2 \
    icu-libs \
    oniguruma \
    libxslt \
    msmtp \
&& apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    imagemagick-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    libxslt-dev \
    openssl-dev \
# Configure GD with image format support
&& docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
# Install PHP extensions required for WordPress
&& docker-php-ext-install -j$(nproc) \
    bcmath \
    exif \
    fileinfo \
    gd \
    intl \
    mbstring \
    mysqli \
    opcache \
    pdo \
    pdo_mysql \
    xml \
    xsl \
    zip \
# Install PECL extensions
&& pecl install redis imagick \
&& docker-php-ext-enable redis imagick \
# Cleanup
&& apk del .build-deps \
&& rm -rf /tmp/* /var/cache/apk/*

# Create user with UID 1000 if it doesn't exist
RUN if ! getent passwd 1000 > /dev/null 2>&1; then \
        addgroup -g 1000 user && \
        adduser -D -u 1000 -G user -h /home/user -s /bin/bash user; \
    fi

# Create directories and set permissions for UID 1000
RUN mkdir -p /var/www /home/user/.composer

# Install Node.js and Yarn via Alpine packages
RUN apk add --no-cache nodejs npm \
    && npm install -g yarn

# Install Composer
ENV COMPOSER_VERSION=2.9.2
ENV COMPOSER_HOME=/home/user/.composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=$COMPOSER_VERSION

# Install WP-CLI for WordPress management
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp \
    && wp --info --allow-root

# Copy entrypoint script
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

WORKDIR /var/www

# Entrypoint
ENTRYPOINT ["/docker-entrypoint.sh"]

# Keep container running
CMD ["tail", "-f", "/dev/null"]
