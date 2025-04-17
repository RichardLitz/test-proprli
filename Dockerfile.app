
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

# Certifique-se de que o PHP-FPM está configurado para usar www-data
RUN sed -i 's/user = root/user = www-data/g' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/group = root/group = www-data/g' /usr/local/etc/php-fpm.d/www.conf

# Executa o Composer
RUN composer install --optimize-autoloader --no-dev

# Cria diretórios de armazenamento e cache se não existirem
RUN mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/bootstrap/cache

# Define permissões adequadas para todos os diretórios de armazenamento
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponha a porta 9000 (PHP-FPM)
EXPOSE 9000

# Script de entrada personalizado para garantir permissões corretas
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]