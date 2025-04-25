FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copia o código
COPY . .

# Instala dependências PHP
RUN composer install --no-interaction --optimize-autoloader

# Garante que as migrations rodem na inicialização
COPY wait-for-db.sh /usr/local/bin/wait-for-db.sh
ENTRYPOINT ["wait-for-db.sh"]
