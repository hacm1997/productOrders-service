# Usa una imagen base de PHP con FPM
FROM php:8.1-fpm

# Instala extensiones y dependencias necesarias para Laravel y la aplicación
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    pkg-config \
    libzip-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql zip

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Copia todos los archivos de la aplicación antes de instalar las dependencias
COPY . .

# Instala las dependencias de Composer después de copiar todos los archivos
RUN composer install --no-dev --optimize-autoloader

# Establece los permisos de los archivos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expone el puerto 9000 para PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
