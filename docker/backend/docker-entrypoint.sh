#!/bin/sh
set -e

cd /var/www/backend

if [ ! -f vendor/autoload_runtime.php ]; then
  composer install --prefer-dist --no-progress --no-interaction
fi

if [ ! -f config/jwt/private.pem ]; then
  php bin/console lexik:jwt:generate-keypair --skip-if-exists --no-interaction || true
fi

echo "[entrypoint] Attente de la base de données..."
until php bin/console dbal:run-sql "SELECT 1" >/dev/null 2>&1; do
  sleep 2
done

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true

exec "$@"
