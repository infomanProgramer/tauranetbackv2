#!/usr/bin/env bash
set -euo pipefail

: "${PORT:=80}"

echo "Listen ${PORT}" > /etc/apache2/ports.conf

echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername >/dev/null 2>&1 || true

# Configurar VirtualHost para el puerto de Render y habilitar .htaccess
# Asegurar valor por defecto para APACHE_LOG_DIR para evitar variable no definida
export APACHE_LOG_DIR=${APACHE_LOG_DIR:-/var/log/apache2}

# Usar heredoc normal para expandir ${PORT} y ${APACHE_LOG_DIR}
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:${PORT}>
    ServerName localhost
    DocumentRoot /var/www/public
    <Directory /var/www/public>
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Permisos para Laravel
mkdir -p /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Cargar archivos de licencia desde variables de entorno si están presentes
mkdir -p /var/www/storage/app/license || true
if [ -n "${LICENSE_DAT_B64:-}" ]; then
  echo "$LICENSE_DAT_B64" | base64 -d > /var/www/storage/app/license/license.dat || true
fi
if [ -n "${PUBLIC_PEM_B64:-}" ]; then
  echo "$PUBLIC_PEM_B64" | base64 -d > /var/www/storage/app/license/public.pem || true
fi
# Permitir fijar fingerprint para que coincida con la licencia
if [ -n "${LICENSE_FINGERPRINT:-}" ]; then
  echo -n "$LICENSE_FINGERPRINT" > /var/www/storage/app/system_id.txt || true
fi
chown -R www-data:www-data /var/www/storage || true

# Preparación de la app
# if [ ! -f /var/www/.env ] && [ -f /var/www/.env.example ]; then
#   cp /var/www/.env.example /var/www/.env
# fi

# Generar APP_KEY si falta
if ! grep -q "^APP_KEY=" /var/www/.env || [ -z "$(grep '^APP_KEY=' /var/www/.env | cut -d '=' -f2)" ]; then
  php artisan key:generate --force || true
fi

php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan config:cache || true
php artisan route:cache || true

# Enlace de almacenamiento si aplica
php artisan storage:link || true

# Iniciar Apache en primer plano
exec apache2-foreground
