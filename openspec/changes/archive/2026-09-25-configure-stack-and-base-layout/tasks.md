# Tasks

## 1. PostgreSQL

- [x] 1.1 Actualizar `.env.example` a `DB_CONNECTION=pgsql` con `DB_HOST`, `DB_PORT=5432`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`, y alinear `.env` si existe. Verificar que `.env.example` ya no declara `sqlite`.
- [x] 1.2 Dejar `phpunit.xml` con SQLite en memoria y verificar que `php artisan test` sigue usando esa conexión de pruebas.

## 2. Livewire

- [x] 2.1 Instalar `livewire/livewire` compatible con Laravel 13 y verificar que aparece en `composer.json` / `composer.lock` y que no se añadieron React, Vue ni Inertia.
- [x] 2.2 Crear el layout Blade con las directivas de Livewire y un componente de página Livewire para `/`. Verificar que `php artisan route:list` muestra esa ruta y que Vite/Tailwind siguen siendo el pipeline de assets.

## 3. Layout base

- [x] 3.1 Implementar `header`, `aside` y `main` en el layout, con barras vacías (sin nav, cuentas, botones ni datos). Verificar en el HTML de `/` que existen esas tres regiones y que header/aside no tienen enlaces ni listas.
- [x] 3.2 Quitar o dejar de usar `welcome.blade.php` y no añadir landing, registro ni autenticación. Verificar que `/` no muestra copy de marketing ni formularios de cuenta.

## 4. Pruebas

- [x] 4.1 Test de feature: `.env.example` declara PostgreSQL (requisito postgresql). Verificar que el test pasa con `php artisan test`.
- [x] 4.2 Test de feature: GET `/` responde 200, incluye assets/scripts de Livewire y las regiones header/aside/main vacías de negocio (requisitos livewire-frontend y app-shell). Verificar que el test pasa con `php artisan test`.
