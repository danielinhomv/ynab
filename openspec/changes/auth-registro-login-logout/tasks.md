# Tasks

## 1. Layouts separados

- [x] 1.1 Crear `layouts/guest.blade.php` con la landing de la captura (cabecera, columna de presentación, slot del formulario) y verificar que registro/login no renderizan el aside de presupuesto.
- [x] 1.2 Rediseñar `layouts/app.blade.php` como dashboard de la captura (header, sidebar de navegación/cuentas, main) y verificar que el usuario autenticado en `/` no ve el formulario de crear cuenta.

## 2. Auth sobre la landing

- [x] 2.1 Formulario de crear cuenta en la tarjeta derecha (nombre, correo, contraseña, confirmar), en español. Verificar `GET /` como visitante.
- [x] 2.2 Registro válido, contraseñas distintas y correo duplicado. Verificar con tests.
- [x] 2.3 Login en la misma landing, enlaces recíprocos. Verificar `/login`.

## 3. Dashboard autenticado

- [x] 3.1 Logout desde el menú de usuario del dashboard; tras logout se ve la landing. Verificar con test.
- [x] 3.2 Configuración de nombre y correo dentro del dashboard. Verificar auth vs visitante.

## 4. Pruebas

- [x] 4.1 Tests de crear cuenta (landing, no dashboard). Verificar `php artisan test`.
- [x] 4.2 Tests de login. Verificar `php artisan test`.
- [x] 4.3 Test de logout. Verificar `php artisan test`.
- [x] 4.4 Tests de configuración. Verificar `php artisan test`.
- [x] 4.5 Tests de app-shell: dashboard vs landing son vistas distintas. Verificar `php artisan test`.
