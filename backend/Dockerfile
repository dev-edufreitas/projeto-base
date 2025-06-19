# Use PHP 8.3 Alpine para desenvolvimento
FROM php:8.3-cli-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev

# Instalar extensões PHP necessárias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Configurar OPcache para desenvolvimento
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário para Laravel
RUN addgroup -g 1000 laravel && adduser -u 1000 -G laravel -s /bin/sh -D laravel

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de dependências primeiro (para cache do Docker)
COPY composer.json composer.lock ./

# Instalar dependências PHP como root
RUN composer install --no-scripts --no-autoloader

# Copiar código fonte
COPY . .

# Completar instalação do Composer
RUN composer dump-autoload --optimize

# Ajustar permissões
RUN chown -R laravel:laravel /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Criar diretórios necessários
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && chown -R laravel:laravel /var/www/html/storage

# Mudar para usuário laravel
USER laravel

# Expor porta 8000 para o servidor built-in do Laravel
EXPOSE 8000

# Health check simples
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8000 || exit 1

# Comando para rodar Laravel em desenvolvimento
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]