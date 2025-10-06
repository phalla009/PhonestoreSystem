#!/bin/sh
# wait-for-db.sh
# Wait until database is ready before starting Laravel

set -e

if [ -z "$DB_HOST" ] || [ -z "$DB_PORT" ]; then
  echo "DB_HOST or DB_PORT not set!"
  exit 1
fi

echo "Waiting for database at $DB_HOST:$DB_PORT..."

until nc -z -v -w30 "$DB_HOST" "$DB_PORT"; do
  echo "Database not ready yet. Waiting..."
  sleep 2
done

echo "Database is up. Starting Laravel..."
exec "$@"
