# Tasks

## 1. Acceso desde el plan

- [ ] 1.1 Comprobar el modelo de plan de `crear-plan`, con `name`, `currency`, `fecha`, `number_format` y `currency_placement`. Si no existe, detenerse y preguntar. No crear otra tabla. Si el usuario no tiene un solo plan y el plan abierto sigue PENDIENTE, detenerse. No editar los changes `crear-plan` ni `user-auth`. Verificar que el modelo está y que esos directorios no cambiaron.
- [ ] 1.2 Crear la pantalla Livewire en `layouts/app`, ruta `plan.configuracion` con `middleware('auth')`, distinta de `configuracion`. En `pages::home`, un enlace en la línea «Moneda del plan» abre esa pantalla en un clic. Mostrar solo nombre, moneda, fecha, number format y currency placement, rellenos con el plan del usuario. No enlazar el avatar ni el sidebar. Un visitante no entra. No cargar un plan ajeno. Verificar que un clic llega a los cinco valores y que `/configuracion` sigue siendo la cuenta.

## 2. Guardado

- [ ] 2.1 Al guardar, escribir nombre, fecha, number format y currency placement del plan. El nombre vacío muestra el error en el campo y no escribe. La confirmación es un mensaje visible en la misma tarjeta; el texto exacto sigue PENDIENTE: detenerse y preguntar antes de fijarlo. No inventar catálogos. No actualizar el encabezado «Plan activo» ni la píldora «Moneda». Verificar que un nombre válido se guarda y que el vacío no cambia el plan.
- [ ] 2.2 La moneda es un solo string. Sin montos guardados, el valor nuevo reemplaza al anterior y no queda una segunda moneda ni otro plan. Si el plan ya tiene montos, detenerse y no cambiar `currency` ni reescribir o convertir esos montos. Verificar que, sin montos, queda una sola moneda y que, con montos, los números no cambian.

## 3. Pruebas por requisito

- [ ] 3.1 Requisito «Acceso en un clic desde el plan»: test del clic desde la vista del plan, de los cinco campos rellenos, de que no es la pantalla de cuenta, de que el avatar no navega, de que un visitante no entra y de que no se abre un plan ajeno. Verificar con `php artisan test --compact` sobre ese test.
- [ ] 3.2 Requisito «Guardar sin errores y confirmar»: test de que un guardado válido persiste los campos y muestra una confirmación, y de que el nombre vacío no guarda. El texto exacto queda fuera hasta cerrar la pregunta. Verificar con `php artisan test --compact` sobre ese test.
- [ ] 3.3 Requisito «Una sola moneda, sin reescribir montos»: test de que otra moneda reemplaza a la anterior cuando no hay montos, y de que con montos esos montos no se convierten ni se reescriben. Verificar con `php artisan test --compact` sobre ese test.
