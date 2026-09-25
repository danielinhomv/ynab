# Proposal

## Why

El registro quedó incrustado en el cascarón del dashboard (header + aside) y sin el diseño de las vistas de referencia. Hace falta separar las pantallas y aplicar esas dos vistas: landing de crear cuenta / inicio de sesión, y dashboard autenticado.

## What Changes

- Pantalla de visitante (crear cuenta e inicio de sesión) con el diseño de la vista de landing: cabecera propia, columna de presentación a la izquierda y tarjeta de formulario a la derecha. **Sin** barra lateral ni chrome del dashboard.
- Tras autenticarse, dashboard con el diseño de la vista de presupuesto: barra superior, navegación + cuentas a la izquierda, contenido del plan al centro. **Sin** el formulario de registro.
- Botón de logout y configuración de cuenta de usuario en el chrome del dashboard (menú de usuario).
- Campos de crear cuenta: nombre, correo, contraseña, confirmar contraseña (en español, estilo de la referencia).
- **BREAKING (respecto a la implementación anterior):** `/` como visitante ya no usa `layouts/app` del dashboard.

## Capabilities

### New Capabilities

- `user-auth`: registro, inicio de sesión, logout y configuración de cuenta de usuario (nombre y correo), sobre el layout de landing.

### Modified Capabilities

- `app-shell`: cascarón **solo autenticado**, con la estructura visual de la vista de dashboard (header, sidebar, main). El visitante no lo ve.

## Impact

- Dos layouts Blade: landing (huésped) y dashboard (sesión).
- Rutas: `/` según sesión, `/login`, `/configuracion`.
- Tests: el visitante no debe ver el sidebar del dashboard; el autenticado no debe ver la landing.

## Preguntas abiertas

Ninguna que bloquee. Google/Apple no se implementan (botones que no hacen nada). La configuración sigue siendo nombre y correo.
