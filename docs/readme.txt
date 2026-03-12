Inicializacion del proyecto (DB/API)
====================================

Comando en Windows:
powershell -ExecutionPolicy Bypass -File .\scripts\init.ps1

Comando en Linux/macOS:
sh ./scripts/init.sh

Que hace este comando:
1) Ejecuta el script de inicializacion desde la raiz del proyecto
   (scripts/init.ps1 en Windows o scripts/init.sh en Linux/macOS).
2) init.ps1 valida que PHP esta disponible.
3) Lanza la migracion de BD (api/tools/migrate.php).
4) Ejecuta un health check de BD (api/tools/check_db.php).

Por que se usa "-ExecutionPolicy Bypass":
- En Windows, PowerShell puede bloquear scripts locales por la politica de ejecucion.
- "Bypass" aplica solo a esta ejecucion puntual del comando.
- No cambia la politica global del sistema de forma permanente.
- Permite correr init.ps1 aunque tu politica actual sea restrictiva.
- En Linux/macOS esto no existe, por eso se usa init.sh directamente.

Importante:
- No necesitas ejecutar este comando cada vez que inicias sesion.
- Usalo en estos casos:
  - Primera puesta en marcha del proyecto.
  - Despues de cambios de estructura en base de datos.
  - Cuando quieras verificar estado de conexion/tablas.


Configuracion de .env
=====================

Crea o edita el archivo .env en la raiz del proyecto con estas claves.
Valores sensibles (tokens/credenciales) nunca se suben al repositorio.

Base de datos:
DB_HOST=localhost
DB_USER=usuario
DB_PASS=password
DB_NAME=bot_padel

Twilio (WhatsApp):
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=tu_auth_token
TWILIO_FROM=tu_numero_whatsapp (ej: +14195238109 en sandbox)

Opcional: plantillas/contents de Twilio
TWILIO_CONTENT_SID=xxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_CONTENT_VARS={"1":"12/1","2":"3pm"}

Webhook:
PUBLIC_BASE_URL=https://tu-dominio-o-ngrok
WEBHOOK_PATH=index.php

Opcional (solo local con ngrok):
NGROK_API_URL=http://127.0.0.1:xxxx/api/tunnels

Notas:
- Si PUBLIC_BASE_URL no esta definido, la app intenta detectar la URL actual.
- Si TWILIO_CONTENT_SID no existe, se enviaran mensajes de texto normales.


Requisitos
==========
- PHP 8.1+ con extensiones: mysqli, pdo_mysql, mbstring, openssl
- Apache con mod_rewrite habilitado
- Composer instalado


Instalacion rapida
==================
1) composer install
2) Copia .env y completa valores
3) Ejecuta scripts/init.ps1 (Windows) o scripts/init.sh (Linux/macOS)
4) Abre /panel_login.php y entra con un usuario admin


Estructura del proyecto
=======================
Este proyecto separa panel (MVC), API REST y webhook de WhatsApp.

Entrypoints:
- index.php: webhook de WhatsApp (Twilio)
- panel_login.php: login del panel
- admin.php: dashboard principal (reservas, historial, usuarios)
- usuarios.php: alias del area de usuarios/reservas

Carpetas principales:
- app/: capa MVC del panel web
- api/: endpoints REST y acceso a BD
- public/: assets publicos (css)
- scripts/: inicializacion y utilidades
- docs/: documentacion interna
- vendor/: dependencias de Composer

Detalle de app/:
- app/Core/: utilidades centrales (Auth, View). Maneja sesion, CSRF y renderizado.
- app/Controllers/: orquestan las peticiones del panel y devuelven vistas.
- app/Services/: logica de negocio del panel (usuarios, reservas, historial, webhook).
- app/Views/: vistas HTML del panel y parciales reutilizables.

Detalle de api/:
- api/auth/: endpoints de login, logout y verificacion de rol.
- api/db/: acceso PDO y schema/migraciones (DbSchema).
- api/tools/: scripts CLI (migrate.php, check_db.php).
- api/*.php: endpoints REST (users, reservas, historial).


Webhook Twilio (Sandbox)
========================
1) Abre /index.php?webhook_info=1 para ver la URL publica detectada
2) Configura esa URL en Twilio Sandbox (When a message comes in)
3) Une tu numero al sandbox enviando "join <sandbox>"


Rutas principales
=================
- /panel_login.php
- /admin.php
- /usuarios.php (alias /reservas)
- /api/*


Seguridad basica
===============
- No subas .env al repositorio
- Rota tokens si se filtran
- Usa HTTPS en produccion


English Version
===============

Project Initialization (DB/API)
===============================

Windows command:
powershell -ExecutionPolicy Bypass -File .\scripts\init.ps1

Linux/macOS command:
sh ./scripts/init.sh

What this command does:
1) Runs the init script from the project root
   (scripts/init.ps1 on Windows or scripts/init.sh on Linux/macOS).
2) init.ps1 checks that PHP is available.
3) Runs DB migrations (api/tools/migrate.php).
4) Runs a DB health check (api/tools/check_db.php).

Why use "-ExecutionPolicy Bypass":
- PowerShell can block local scripts by execution policy.
- "Bypass" applies only to that command execution.
- It does not change the global policy permanently.
- It lets init.ps1 run even if your policy is restrictive.
- On Linux/macOS this does not exist, so init.sh is used.

Important:
- You do NOT need to run this command every time you log in.
- Use it when:
  - First time setup.
  - After DB schema changes.
  - When you want to verify DB connection/tables.


.env Configuration
==================

Create or edit the .env file in the project root with these keys.
Sensitive values (tokens/credentials) must NOT be committed.

Database:
DB_HOST=localhost
DB_USER=user
DB_PASS=password
DB_NAME=bot_padel

Twilio (WhatsApp):
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=your_auth_token
TWILIO_FROM=your_whatsapp_number (ex: +14195238109 in sandbox)

Optional: Twilio templates/contents
TWILIO_CONTENT_SID=xxxxxxxxxxxxxxxxxxxxxxx
TWILIO_CONTENT_VARS={"1":"12/1","2":"3pm"}

Webhook:
PUBLIC_BASE_URL=https://your-domain-or-ngrok
WEBHOOK_PATH=index.php

Optional (local only with ngrok):
NGROK_API_URL=http://127.0.0.1:xxxx/api/tunnels

Notes:
- If PUBLIC_BASE_URL is not set, the app tries to detect the current URL.
- If TWILIO_CONTENT_SID is missing, plain text messages will be sent.


Requirements
============
- PHP 8.1+ with extensions: mysqli, pdo_mysql, mbstring, openssl
- Apache with mod_rewrite enabled
- Composer installed


Quick Install
=============
1) composer install
2) Create .env and fill values
3) Run scripts/init.ps1 (Windows) or scripts/init.sh (Linux/macOS)
4) Open /panel_login.php and log in with an admin user


Project Structure
=================
This project separates the panel (MVC), REST API and WhatsApp webhook.

Entrypoints:
- index.php: WhatsApp webhook (Twilio)
- panel_login.php: panel login
- admin.php: main dashboard (reservations, history, users)
- usuarios.php: alias for the users/reservations area

Main folders:
- app/: MVC layer for the web panel
- api/: REST endpoints and DB access
- public/: public assets (css)
- scripts/: initialization and utilities
- docs/: internal documentation
- vendor/: Composer dependencies

app/ details:
- app/Core/: core utilities (Auth, View). Handles session, CSRF, rendering.
- app/Controllers/: orchestrate panel requests and return views.
- app/Services/: panel business logic (users, reservations, history, webhook).
- app/Views/: panel HTML views and reusable partials.

api/ details:
- api/auth/: login, logout and role verification endpoints.
- api/db/: PDO access and schema/migrations (DbSchema).
- api/tools/: CLI scripts (migrate.php, check_db.php).
- api/*.php: REST endpoints (users, reservas, historial).


Twilio Webhook (Sandbox)
========================
1) Open /index.php?webhook_info=1 to see the detected public URL
2) Configure that URL in Twilio Sandbox (When a message comes in)
3) Join the sandbox by sending "join <sandbox>"


Main Routes
===========
- /panel_login.php
- /admin.php
- /usuarios.php (alias /reservas)
- /api/*


Basic Security
==============
- Do not commit .env
- Rotate tokens if exposed
- Use HTTPS in production


