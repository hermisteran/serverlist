#!/bin/bash

echo "Iniciando entorno Laravel"

# Permisos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Instalar dependencias si vendor no existe
if [ ! -d vendor ]; then
  echo "Instalando dependencias con Composer"
  composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts
fi

# Generar .env si no existe
if [ ! -f .env ]; then
  echo "Archivo .env no encontrado. Generando desde variables del entorno"
  cat <<EOF > .env
APP_NAME="Server Api"
APP_ENV=${APP_ENV:-local}
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-serverlist}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-root}
EOF
fi

# Esperar a que la DB esté lista
# echo "Esperando conexión a la base de datos en $DB_HOST:$DB_PORT"
# until nc -z "$DB_HOST" "$DB_PORT"; do
#   echo "DB no disponible aún. Reintentando"
#   sleep 2
# done
# echo "Base de datos disponible."

# Generar APP_KEY si falta
if ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY .env | cut -d '=' -f2)" ]; then
  echo "Generando APP_KEY"
  php artisan key:generate
fi

# Cachear configuración
php artisan config:clear
php artisan config:cache

# Ejecutar migraciones y seeders
php artisan migrate --seed --force

# Crear storage link
php artisan storage:link

# Generar documentación scribe y permisos
php artisan scribe:generate
chmod -R 755 public/vendor
chown -R www-data:www-data public/vendor

# Iniciar Apache
echo "Iniciando Apache"
exec apache2-foreground
