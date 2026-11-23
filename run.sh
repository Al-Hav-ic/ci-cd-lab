#!/usr/bin/env bash
# =============================================================================
# Lab 8 — скрипт запуску локального сервера розробки (Laravel + Sanctum + Swagger)
# =============================================================================
# Що робить скрипт:
#   1. перевіряє наявність Composer-відповідей (vendor/autoload.php)
#   2. якщо .env відсутній — копіює .env.example -> .env
#   3. створює базу даних SQLite (database/database.sqlite), якщо потрібно
#   4. генерує ключ додаунжайз (APP_KEY)
#   5. запускає міграції
#   6. запускає сервер: php artisan serve --host=127.0.0.1 --port=8000
#
# Використання:
#   bash run.sh
# або з іншою папкою/портом:
#   APP_URL=http://127.0.0.1 PORT=8080 bash run.sh

set -e

PHP_BIN="${PHP_BIN:-D:/PhPLabs/_tools/php/php.exe}"
PORT="${PORT:-8000}"
BASE_URL="${APP_URL:-http://127.0.0.1:8000}"

cd "$(dirname "$0")"

echo ">>> Перевірка Composer-відповідей..."
[ -f vendor/autoload.php ] || { echo ">>> Встановлюємо залежності (composer install)..."; php "$PHP_BIN" composer.phar install -q --no-interaction; }

echo ">>> Налаштування середовища..."
[ -f .env ] || cp .env.example .env
[ -f database/database.sqlite ] || touch database/database.sqlite
"$PHP_BIN" artisan key:generate --force >/dev/null

echo ">>> Запуск міграцій..."
"$PHP_BIN" artisan migrate --force

echo ">>> Запуск сервера: $BASE_URL (порт $PORT)"
"$PHP_BIN" artisan serve --host=127.0.0.1 --port="$PORT"
