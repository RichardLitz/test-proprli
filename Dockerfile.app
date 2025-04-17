
FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Instala extensões do PHP necessárias para o Laravel e PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql pgsql zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuração do PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia arquivos do projeto
COPY . /var/www/html

# Permissões para o diretório de armazenamento
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Executa o Composer
RUN composer install --optimize-autoloader --no-dev

# Exponha a porta 9000 (PHP-FPM)
EXPOSE 9000

CMD ["php-fpm"]