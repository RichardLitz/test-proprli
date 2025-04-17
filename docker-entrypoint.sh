#!/bin/bash
set -e

# Garantir permissões corretas ao iniciar o container
echo "Configurando permissões..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Criar arquivo de log se não existir e definir permissões
touch /var/www/html/storage/logs/laravel.log
chown www-data:www-data /var/www/html/storage/logs/laravel.log
chmod 664 /var/www/html/storage/logs/laravel.log

echo "Permissões configuradas com sucesso!"

# Executar comando passado para o container
exec "$@"