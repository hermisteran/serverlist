FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl sqlite3 libsqlite3-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurar límites de subida en PHP
RUN echo "upload_max_filesize=10M\npost_max_size=12M" > /usr/local/etc/php/conf.d/uploads.ini
# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Crear y setear permisos antes de copiar código
WORKDIR /var/www/html
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copiar proyecto
COPY . .

# Copiar script de entrada
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Instalar dependencias Laravel (sin scripts para evitar problemas en build)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
