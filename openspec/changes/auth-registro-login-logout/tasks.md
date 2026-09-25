# Tasks

## 1. Layouts separados

- [x] 1.1 Crear `layouts/guest.blade.php` con la landing de la captura (cabecera, columna de presentación, slot del formulario) y verificar que registro/login no renderizan el aside de presupuesto.
- [x] 1.2 Rediseñar `layouts/app.blade.php` como dashboard de la captura (header, sidebar de navegación/cuentas, main) y verificar que el usuario autenticado en `/` no ve el formulario de crear cuenta.

## 2. Auth sobre la landing

- [x] 2.1 Formulario de iniciar sesión en la tarjeta derecha (correo, contraseña), en español. Verificar `GET /` como visitante.
- [x] 2.2 Desde login, **Crear cuenta** abre `/registro`. Verificar enlace recíproco a iniciar sesión.
- [x] 2.3 Registro válido, contraseñas distintas y correo duplicado. Verificar con tests.

## 3. Dashboard autenticado

- [x] 3.1 Menú del avatar en el dashboard: Cerrar sesión funciona y vuelve a la landing. Verificar con test.
- [x] 3.2 Configuración en ese menú visible y sin acción (no es un enlace). Verificar con test.

## 4. Pruebas

- [x] 4.1 Tests de crear cuenta (landing, no dashboard). Verificar `php artisan test`.
- [x] 4.2 Tests de login. Verificar `php artisan test`.
- [x] 4.3 Test de logout. Verificar `php artisan test`.
- [x] 4.4 Tests: Configuración del menú no navega. Verificar `php artisan test`.
- [x] 4.5 Tests de app-shell: dashboard vs landing son vistas distintas. Verificar `php artisan test`.
