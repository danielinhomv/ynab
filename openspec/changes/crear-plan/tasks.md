# Tasks

## 1. Persistencia

- [x] 1.1 Crear el modelo Plan, su factory y la migración con `user_id`, `name`, `currency` (una sola columna), `fecha` (date), `number_format` y `currency_placement`. Verificar que la migración no crea una segunda moneda ni una tabla de cuentas.
- [x] 1.2 Crear los modelos Category y Sobre, sus factories y las migraciones: categoría con `plan_id` y `name`; sobre con `category_id`, `name`, `assigned`, `activity` y `available` en columnas `decimal`, no `float`. Verificar el tipo de columna en el esquema de PostgreSQL.
- [x] 1.3 Crear plan, categorías y sobres en una sola transacción al enviar el formulario válido, siempre del usuario autenticado. Verificar con un test que otro usuario no recibe ese plan y que un fallo de validación no deja filas.

## 2. Formulario en el dashboard

- [x] 2.1 Crear la página Livewire con `layouts/app` y el mismo trato visual que `pages::account-settings`: solo los campos nombre, multimoneda, fecha, number format y currency placement. Verificar que no se añade un ítem en el sidebar ni en el menú del avatar, y que no se editan login, registro ni el HTML estático del home.
- [x] 2.2 Si la ruta del formulario sigue PENDIENTE en `proposal.md`, detenerse y preguntar. No elegir entre reutilizar `/` y una ruta nueva. Cuando esté respondida, registrarla con `middleware('auth')` y verificar que el visitante no ve el formulario y el autenticado ve los cinco campos.

## 3. Alta

- [x] 3.1 Validar el nombre vacío con el mensaje «El nombre es obligatorio.» bajo el campo y no crear el plan. Verificar ese caso con un test de Livewire.
- [x] 3.2 Si las opciones o los valores por defecto de multimoneda, fecha, number format o currency placement siguen PENDIENTE, detenerse y preguntar. No copiar BOB ni el formato `Bs 1.250,00` de la maqueta. Cuando estén respondidos, mostrarlos al abrir el formulario y rechazar un valor fuera de la lista con «El valor no es válido.». Verificar que, sin cambiar esos campos, un solo envío crea el plan con la moneda, la fecha, el number format y el currency placement que se veían.
- [x] 3.3 Si el catálogo de categorías y sobres de ejemplo, o los montos iniciales de asignado, actividad y disponible, siguen PENDIENTE, detenerse y preguntar. No copiar Servicios básicos ni los montos del home. Cuando estén respondidos, guardarlos en la misma transacción del alta. Verificar que el plan queda con al menos una categoría y un sobre asociado, y que los montos no son float.
- [x] 3.4 Si el texto o el lugar de la confirmación siguen PENDIENTE, detenerse y preguntar. Cuando estén respondidos, mostrar la confirmación con las clases ya usadas en el dashboard, sin una librería de toasts. Verificar que tras un alta válida la confirmación se ve.
- [x] 3.5 No agregar índice único por usuario ni un selector de planes. Si hace falta decidir si un segundo alta se rechaza, detenerse y preguntar. Verificar que este change no muestra un listado de planes ni crea cuentas.

## 4. Pruebas por requisito

- [x] 4.1 Requisito «Formulario de crear plan»: test de los cinco campos para el autenticado y de que el visitante no crea un plan. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.2 Requisito «Plan de una sola moneda»: test de que el plan guarda una sola `currency` (la de multimoneda), el nombre, la fecha, el number format y el currency placement, y de que otro usuario no lo ve. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.3 Requisito «Alta en un solo envío con valores por defecto»: test de que informar solo el nombre crea el plan con los valores que el formulario mostraba. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.4 Requisito «Categorías y sobres de ejemplo»: test de que el alta válida deja al menos una categoría y un sobre, y de que el alta inválida no deja filas. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.5 Requisito «Validación clara y confirmación visible»: test del nombre vacío, de un valor fuera de las opciones y de la confirmación visible. Verificar con `php artisan test --compact` sobre ese test.
