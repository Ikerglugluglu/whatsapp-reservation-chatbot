# Chatbot de WhatsApp para reservas

Chatbot de WhatsApp con panel web y API REST para gestionar reservas, usuarios e historial (PHP + MVC + Twilio).

## Instalacion rapida

1. `composer install`
2. Crea `.env` y completa valores
3. Ejecuta `scripts/init.ps1` (Windows) o `scripts/init.sh` (Linux/macOS)
4. Abre `/panel_login.php` y entra con un usuario admin
   - Si no existe ninguno, la migracion crea uno con `ADMIN_USER`/`ADMIN_PASS`

## Configuracion de .env

Crea o edita el archivo `.env` en la raiz del proyecto.

```env
DB_HOST=localhost
DB_USER=usuario
DB_PASS=password
DB_NAME=bot_padel

TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=tu_auth_token
TWILIO_FROM=tu_numero_whatsapp

ADMIN_USER=admin
ADMIN_PASS=admin1234

TWILIO_CONTENT_SID=HXb5b62575e6e4ff6129ad7c8efe1f983e
TWILIO_CONTENT_VARS={"1":"12/1","2":"3pm"}

PUBLIC_BASE_URL=https://tu-dominio-o-ngrok
WEBHOOK_PATH=index.php
NGROK_API_URL=http://127.0.0.1:4040/api/tunnels
```

Notas:
- Si `PUBLIC_BASE_URL` no esta definido, la app intenta detectar la URL actual.
- Si `TWILIO_CONTENT_SID` no existe, se enviaran mensajes de texto normales.

## Requisitos

- PHP 8.1+ con extensiones: mysqli, pdo_mysql, mbstring, openssl
- Apache con mod_rewrite habilitado
- Composer instalado

## Inicializacion (DB/API)

Windows:
```
powershell -ExecutionPolicy Bypass -File .\scripts\init.ps1
```

Linux/macOS:
```
sh ./scripts/init.sh
```

Que hace:
- Valida PHP
- Ejecuta migracion (`api/tools/migrate.php`)
- Ejecuta health check (`api/tools/check_db.php`)

Importante:
- Si no existe ningun admin, la migracion crea uno (`ADMIN_USER`/`ADMIN_PASS`).
- No necesitas ejecutar esto cada vez que inicias sesion.

## Estructura del proyecto

- `index.php`: webhook de WhatsApp (Twilio)
- `panel_login.php`: login del panel
- `admin.php`: dashboard principal (reservas, historial, usuarios)
- `usuarios.php`: alias del area de usuarios/reservas
- `app/`: capa MVC del panel web
- `api/`: endpoints REST y acceso a BD
- `public/`: assets publicos (css)
- `scripts/`: inicializacion y utilidades
- `docs/`: documentacion interna

## Webhook Twilio (Sandbox)

1. Abre `/index.php?webhook_info=1` para ver la URL publica detectada
2. Configura esa URL en Twilio Sandbox (When a message comes in)
3. Une tu numero al sandbox enviando `join <sandbox>`


# WhatsApp Reservation Chatbot

WhatsApp chatbot with a web panel and REST API to manage reservations, users and history (PHP + MVC + Twilio).

## Quick Start

1. `composer install`
2. Create `.env` and fill values
3. Run `scripts/init.ps1` (Windows) or `scripts/init.sh` (Linux/macOS)
4. Open `/panel_login.php` and log in with an admin user
   - If none exists, migration creates one using `ADMIN_USER`/`ADMIN_PASS`

## .env Configuration

Create or edit the `.env` file in the project root.

```env
DB_HOST=localhost
DB_USER=user
DB_PASS=password
DB_NAME=bot_padel

TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=your_auth_token
TWILIO_FROM=your_whatsapp_number

ADMIN_USER=admin
ADMIN_PASS=admin1234

TWILIO_CONTENT_SID=HXb5b62575e6e4ff6129ad7c8efe1f983e
TWILIO_CONTENT_VARS={"1":"12/1","2":"3pm"}

PUBLIC_BASE_URL=https://your-domain-or-ngrok
WEBHOOK_PATH=index.php
NGROK_API_URL=http://127.0.0.1:4040/api/tunnels
```

Notes:
- If `PUBLIC_BASE_URL` is not set, the app tries to detect the current URL.
- If `TWILIO_CONTENT_SID` is missing, plain text messages will be sent.

## Requirements

- PHP 8.1+ with extensions: mysqli, pdo_mysql, mbstring, openssl
- Apache with mod_rewrite enabled
- Composer installed

## Initialization (DB/API)

Windows:
```
powershell -ExecutionPolicy Bypass -File .\scripts\init.ps1
```

Linux/macOS:
```
sh ./scripts/init.sh
```

What it does:
- Validates PHP
- Runs migration (`api/tools/migrate.php`)
- Runs health check (`api/tools/check_db.php`)

Important:
- If no admin exists, migration creates one (`ADMIN_USER`/`ADMIN_PASS`).
- You do NOT need to run this every time you log in.

## Project Structure

- `index.php`: WhatsApp webhook (Twilio)
- `panel_login.php`: panel login
- `admin.php`: main dashboard (reservations, history, users)
- `usuarios.php`: alias for the users/reservations area
- `app/`: MVC layer for the web panel
- `api/`: REST endpoints and DB access
- `public/`: public assets (css)
- `scripts/`: initialization and utilities
- `docs/`: internal documentation

## Twilio Webhook (Sandbox)

1. Open `/index.php?webhook_info=1` to see the detected public URL
2. Configure that URL in Twilio Sandbox (When a message comes in)
3. Join the sandbox by sending `join <sandbox>`

