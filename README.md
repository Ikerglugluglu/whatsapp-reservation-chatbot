# WhatsApp Reservation Chatbot

<<<<<<< Updated upstream
Chatbot de WhatsApp para gestionar reservas automáticamente mediante Twilio.
Incluye panel administrativo web y API REST.

Tech stack:
- PHP
- MySQL
- Twilio WhatsApp API
- MVC architecture
=======
Chatbot de WhatsApp para gestionar reservas automaticamente con un panel web administrativo y una API REST.

Tecnologias:
- PHP 8 (arquitectura MVC)
- MySQL
- Twilio WhatsApp API
- Composer

>>>>>>> Stashed changes
## Features

- Chatbot de WhatsApp para reservas automaticas
- Panel web administrativo
- API REST para gestion de usuarios y reservas
- Historial de reservas
- Sistema de migraciones para base de datos
- Configuracion mediante variables de entorno

## Screenshots

Login panel
Admin dashboard
Chatbot conversation example

Imagenes:
- docs/screenshots/login.png
- docs/screenshots/panel.png
- docs/screenshots/chat.png

## Quick Start

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

<<<<<<< Updated upstream
TWILIO_CONTENT_SID=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
=======
TWILIO_CONTENT_SID=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
>>>>>>> Stashed changes
TWILIO_CONTENT_VARS={"1":"12/1","2":"3pm"}

PUBLIC_BASE_URL=https://tu-dominio-o-ngrok
WEBHOOK_PATH=index.php
<<<<<<< Updated upstream
NGROK_API_URL=http://127.0.0.1:xxx/api/tunnels
=======
NGROK_API_URL=http://127.0.0.1:xxx/api/tunnels
>>>>>>> Stashed changes
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

## Politicas y legal

- [Aviso legal](docs/aviso-legal.md)
- [Politica de privacidad](docs/privacidad.md)
- [Politica de cookies](docs/cookies.md)

## Security

Sensitive configuration values (API tokens, database credentials)
must be stored in the .env file and should never be committed to the repository.

## Publicar en GitHub

- No subas `.env`
- No hardcodees credenciales
- Evita numeros reales en ejemplos
- Elimina logs/dumps/backups

## License

MIT License

---

# WhatsApp Reservation Chatbot

WhatsApp chatbot with a web panel and REST API to manage reservations, users and history (PHP + MVC + Twilio).

Technologies:
- PHP 8 (MVC architecture)
- MySQL
- Twilio WhatsApp API
- Composer

## Features

- WhatsApp chatbot for automatic reservations
- Admin web panel
- REST API for users and reservations
- Reservation history
- Database migration system
- Environment-based configuration

## Screenshots

Login panel
Admin dashboard
Chatbot conversation example

Images:
- docs/screenshots/login.png
- docs/screenshots/panel.png
- docs/screenshots/chat.png

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

<<<<<<< Updated upstream
TWILIO_CONTENT_SID=xxxxxxxxxxxxxxxxxxxxxxxxxx
=======
TWILIO_CONTENT_SID=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
>>>>>>> Stashed changes
TWILIO_CONTENT_VARS={"1":"12/1","2":"3pm"}

PUBLIC_BASE_URL=https://your-domain-or-ngrok
WEBHOOK_PATH=index.php
<<<<<<< Updated upstream
NGROK_API_URL=http://127.0.0.1:xxx/api/tunnels
=======
NGROK_API_URL=http://127.0.0.1:xxx/api/tunnels
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
=======
## Troubleshooting (Twilio Sandbox)

If Twilio Debugger shows error `12200` with:
`cvc-elt.1.a: Cannot find the declaration of element 'br'`
it usually means your webhook returned **HTML** instead of TwiML. A common cause
is a PHP fatal error that outputs an HTML error page with `<br>` tags.

In this project we saw a frequent root cause:
**BOM (UTF‑8 with BOM) in vendor PHP files**, which triggers:
`Namespace declaration statement has to be the very first statement...`

Fix:
1. Avoid editing `vendor/` with Visual Studio.
2. Reinstall dependencies: `composer install`
3. If needed, remove BOM from affected files.

>>>>>>> Stashed changes
## Policies and Legal

- [Legal notice](docs/aviso-legal.md)
- [Privacy policy](docs/privacidad.md)
- [Cookie policy](docs/cookies.md)

## Security

Sensitive configuration values (API tokens, database credentials)
must be stored in the .env file and should never be committed to the repository.

## Publish to GitHub

- Do not commit `.env`
- Do not hardcode credentials
- Avoid real phone numbers in examples
- Remove logs/dumps/backups

## License

MIT License
