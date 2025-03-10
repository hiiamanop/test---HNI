# Stage 1: Build stage (install dependencies and extensions)
FROM php:8.4.4-fpm AS builder

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# Stage 2: Production stage
FROM php:8.4.4-fpm AS production

# Set working directory
WORKDIR /var/www

# Copy built PHP extensions from the builder stage
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy project files
COPY . /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]
