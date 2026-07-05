FROM php:8.2-apache

# Install PostgreSQL PDO extension (required for Supabase connection)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

# Enable apache mod_rewrite
RUN a2enmod rewrite

# Copy all project files to Apache root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 80
EXPOSE 80
