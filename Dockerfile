FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor \
    locales \
    && echo "pt_BR.UTF-8 UTF-8" >> /etc/locale.gen \
    && locale-gen pt_BR.UTF-8 \
    && update-locale LANG=pt_BR.UTF-8

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Set environment variables for UTF-8
ENV LANG=pt_BR.UTF-8 \
    LANGUAGE=pt_BR:pt:en \
    LC_ALL=pt_BR.UTF-8 \
    LC_CTYPE=pt_BR.UTF-8

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy nginx config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy supervisor config
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create necessary directories
RUN mkdir -p /var/www/html/logs /var/www/html/uploads /var/www/html/cache /var/www/html/participantes \
    /var/log/nginx /var/log/php-fpm /var/log/supervisor \
    && chmod -R 777 /var/www/html/logs /var/www/html/uploads /var/www/html/cache /var/www/html/participantes

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]