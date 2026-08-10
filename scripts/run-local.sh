#!/usr/bin/env bash
set -euo pipefail

# Helper to create/import DB and start PHP built-in server for local testing
PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SQL="$PROJECT_ROOT/data.sql"
DB_NAME="data"
DB_USER="root"
DB_PASS=""

if [ ! -f "$SQL" ]; then
  echo "data.sql not found at $SQL"
  exit 1
fi

if [ -z "$DB_PASS" ]; then
  MYSQL_CMD=(mysql -u "$DB_USER")
else
  MYSQL_CMD=(mysql -u "$DB_USER" -p"$DB_PASS")
fi

echo "Creating database if not exists: $DB_NAME"
"${MYSQL_CMD[@]}" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;"
echo "Importing $SQL into $DB_NAME"
"${MYSQL_CMD[@]}" "$DB_NAME" < "$SQL"

cd "$PROJECT_ROOT"
echo "Starting PHP built-in server at http://localhost:8000"
php -S localhost:8000
