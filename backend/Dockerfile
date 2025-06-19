FROM php:8.3-apache

# Instalar extensões PHP necessárias para Laravel 12 e PostgreSQL
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_pgsql pdo_sqlite zip exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Instalar e configurar OPcache para melhor performance
RUN docker-php-ext-install opcache
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configurar PHP para melhor performance
COPY php.ini /usr/local/etc/php/php.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar apenas composer files primeiro (para cache de layers)
COPY composer.json composer.lock ./

# Instalar dependências do Composer (usar cache)
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --optimize-autoloader

# Copiar resto da aplicação
COPY . .

# Otimizar autoloader do Composer
RUN composer dump-autoload --optimize --no-dev --no-scripts

# Configurar permissões para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expor porta
EXPOSE 9000

# Copia o entrypoint e dá permissão
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]