# Proposal

## Why

El registro quedó incrustado en el cascarón del dashboard (header + aside) y sin el diseño de las vistas de referencia. Hace falta separar las pantallas y aplicar esas dos vistas: landing de crear cuenta / inicio de sesión, y dashboard autenticado.

## What Changes

- Visitante: por defecto **inicio de sesión** en `/`. **Crear cuenta** va a `/registro` (misma landing, otra tarjeta).
- Tras autenticarse, dashboard con el diseño de la vista de presupuesto: barra superior, navegación + cuentas a la izquierda, contenido del plan al centro. **Sin** el formulario de registro.
- Menú del avatar (la inicial del nombre): al abrirlo se ve **Cerrar sesión** (funciona) y **Configuración** (visible, sin acción: esa vista no entra en este change).
- Campos de crear cuenta: nombre, correo, contraseña, confirmar contraseña (en español, estilo de la referencia).
- **BREAKING (respecto a la implementación anterior):** `/` como visitante ya no usa `layouts/app` del dashboard.

## Capabilities

### New Capabilities

- `user-auth`: registro, inicio de sesión y logout. El ítem Configuración del menú no abre ninguna vista.

### Modified Capabilities

- `app-shell`: cascarón **solo autenticado**, con la estructura visual de la vista de dashboard (header, sidebar, main). El visitante no lo ve.

## Impact

- Dos layouts Blade: landing (huésped) y dashboard (sesión).
- Rutas: `/` login (visitante) o dashboard (sesión), `/login`, `/registro`.
- Tests: el visitante no debe ver el sidebar del dashboard; el autenticado no debe ver la landing.

## Preguntas abiertas

Ninguna que bloquee. Google/Apple no se implementan. Configuración en el menú es placeholder.
