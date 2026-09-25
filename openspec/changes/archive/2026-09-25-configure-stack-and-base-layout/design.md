# Design

## Context

El repo es un esqueleto Laravel 13 con Vite, Tailwind 4 y `welcome.blade.php`. `DB_CONNECTION` en `.env.example` es `sqlite`. No hay Livewire ni starter kit. Las especificaciones están en `specs/postgresql`, `specs/livewire-frontend` y `specs/app-shell`. Motivación: ver `proposal.md`.

## Goals / Non-Goals

**Goals:**

- Ejemplo de entorno y convención local en PostgreSQL.
- Livewire instalado y usado por la ruta principal, sin cambiar Vite/Tailwind.
- Un layout Blade reutilizable (cabecera + sidebar + contenido) y una página mínima que lo use.

**Non-Goals:**

- Instalar Breeze, Fortify, Jetstream u otro starter de autenticación.
- Migrar datos; no hay datos de negocio.
- Paleta pixel-perfect ni copy de las capturas de referencia.
- Cambiar `phpunit.xml` a PostgreSQL.

## Decisions

### 1. PostgreSQL solo en config de aplicación, SQLite en tests

Laravel 13 ya soporta `pgsql`. Se actualiza `.env.example` (`DB_CONNECTION=pgsql`, host, puerto 5432, base, usuario, contraseña). Si existe `.env`, alinearlo. `phpunit.xml` se deja en SQLite en memoria.

Alternativa: exigir PostgreSQL también en CI/tests. Se descarta: la spec lo permite y no hay tablas de negocio.

### 2. Livewire como única adición de frontend

`composer require livewire/livewire` (versión compatible con Laravel 13). Layout Blade con directivas de Livewire. Un componente de página Livewire para `/` reemplaza `welcome`. Sin React/Vue/Inertia.

Alternativa: Blade puro ahora e instalar Livewire después. Se descarta: el usuario pidió configurar el frontend del contexto ahora.

### 3. Layout estructural, no réplica de marketing

`resources/views/layouts/app.blade.php` con regiones identificables (`header`, `aside`, `main`). Las barras quedan vacías (sin nav, cuentas ni botones). Colores neutros con Tailwind. La captura de presupuesto sirve de guía de regiones; la de registro queda fuera.

Alternativa: clonar SobrePeso (logo, moneda, “Nuevo sobre”). Se descarta: contradice barras vacías y “sin funcionalidad de negocio”.

### 4. Ruta `/` sirve el cascarón

`Route::get('/', ...)` apunta al componente Livewire. Se elimina o deja de usarse `welcome.blade.php`.

## Risks / Trade-offs

- [No hay PostgreSQL local] → Documentar en tareas que hay que crear la base; la app arranca para vistas sin migrar modelos de negocio.
- [Extensión `pdo_pgsql` ausente] → Fallará la conexión real; tests de HTTP del layout no la necesitan.
- [Layout demasiado vacío vs. expectativas visuales] → Las regiones existen con tamaño visible; el contenido de negocio llega en changes posteriores.

## Migration Plan

1. Instalar Livewire y actualizar `.env.example` / `.env`.
2. Crear layout y componente; cambiar la ruta `/`.
3. Ejecutar tests de feature.
4. Rollback: revertir el change; no hay migraciones de dominio.

## Open Questions

Ninguna que altere specs, enfoque o tareas.
