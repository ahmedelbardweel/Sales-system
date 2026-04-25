FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    npm \
    sqlite3 \
    libsqlite3-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Update Apache DocumentRoot to point to Laravel's public directory
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Install composer and npm dependencies, then build
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Setup SQLite database
RUN mkdir -p /var/www/html/storage/app \
    && touch /var/www/html/storage/app/database.sqlite \
    && chown -R www-data:www-data /var/www/html/storage

ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/www/html/storage/app/database.sqlite

# Run migrations and cache config
RUN php artisan migrate --force \
    && php artisan route:cache \
    && php artisan view:cache

# Change port to 10000 for Render
RUN sed -i 's/80/10000/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
EXPOSE 10000

CMD ["apache2-foreground"]
