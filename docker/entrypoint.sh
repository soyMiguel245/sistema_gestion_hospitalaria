#!/bin/sh
set -e

if [ ! -f .env ]; then
  cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64" .env; then
  php artisan key:generate --force
fi

echo "Esperando a que SQL Server este disponible en $DB_HOST:$DB_PORT..."
until php -r "new PDO('sqlsrv:Server='.getenv('DB_HOST').','.getenv('DB_PORT').';', getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" > /dev/null 2>&1; do
  sleep 2
done
echo "SQL Server disponible."

php -r "
\$pdo = new PDO('sqlsrv:Server='.getenv('DB_HOST').','.getenv('DB_PORT').';', getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
\$db = getenv('DB_DATABASE');
\$pdo->exec(\"IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = '\$db') CREATE DATABASE [\$db]\");
echo 'Base de datos [' . \$db . '] verificada/creada.' . PHP_EOL;
"

php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port=8000