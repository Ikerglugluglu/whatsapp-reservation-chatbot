#!/usr/bin/env sh
# Script de inicializacion (Linux/macOS).

set -eu

# Rutas base.
SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"
ROOT_DIR="$(CDPATH= cd -- "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT_DIR"

PHP_BIN="${PHP_BIN:-php}"

echo "==> Checking PHP availability"
"$PHP_BIN" -v | head -n 1
echo "OK: Checking PHP availability"

echo "==> Running DB migration"
"$PHP_BIN" "./api/tools/migrate.php"
echo "OK: Running DB migration"

echo "==> Running DB health check"
"$PHP_BIN" "./api/tools/check_db.php"
echo "OK: Running DB health check"

echo ""
echo "Init completed successfully."
