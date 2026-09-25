# Proposal

## Why

El esqueleto de Laravel sigue en SQLite, sin Livewire y con la vista de bienvenida por defecto. Hace falta dejar el stack del contexto (PostgreSQL + Blade/Livewire) y un cascarón visual vacío para poder construir el presupuesto después, sin meter reglas de negocio ahora.

## What Changes

- Configurar Laravel para usar PostgreSQL como base de datos de la aplicación (`.env.example` y convención de entorno).
- Instalar y configurar Livewire sobre Blade y Vite/Tailwind ya presentes.
- Reemplazar la página de bienvenida por una pantalla que use un layout base: barra superior vacía, barra lateral vacía y área de contenido, siguiendo la estructura de las vistas de referencia (cabecera a lo ancho + columna izquierda + contenido).
- No incluir autenticación, registro, landing de marketing, planes, cuentas, sobres ni ninguna otra funcionalidad de negocio.

## Capabilities

### New Capabilities

- `postgresql`: conexión y configuración de PostgreSQL para la aplicación Laravel.
- `livewire-frontend`: frontend con Blade y Livewire listo para vistas posteriores.
- `app-shell`: layout base con barra superior y barra lateral vacías.

### Modified Capabilities

- Ninguna. No hay specs existentes.

## Impact

- Dependencias: `livewire/livewire`; posible extensión PHP `pgsql` en el entorno local.
- Archivos: `.env.example`, `composer.json`, `resources/views/`, `routes/web.php`, `resources/css/app.css` (solo si hace falta el layout).
- Tests de feature para configuración y para el layout vacío.
- Fuera de alcance: starter kit de autenticación, formularios de la vista de registro de referencia, datos de presupuesto, navegación con ítems.

## Preguntas abiertas

- Ninguna que bloquee este alcance. Las vistas de referencia se usan solo como guía de estructura (barra superior + barra lateral + contenido), no como réplica de copy, datos ni acciones.
