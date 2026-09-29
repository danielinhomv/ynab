# Tasks

## 1. Montos del mes

- [x] 1.1 Comprobar que existe el modelo de sobre de `crear-plan`. Si no existe, detenerse y preguntar. No crear el sobre en este change. Verificar que la clase está en el proyecto.
- [x] 1.2 Crear la tabla `sobre_meses` con `sobre_id`, año, mes y asignado, actividad y disponible en decimal. No copiar el disponible de un mes a otro al migrar ni al cambiar de mes. Si el usuario no tiene un solo plan y el plan abierto sigue PENDIENTE, detenerse. Verificar que no se inserta una fila de traspaso.

## 2. Flechas y tabla

- [x] 2.1 En el encabezado de la vista del plan, junto al título, añadir las flechas «Mes anterior» y «Mes siguiente» con el estilo de botón claro que ya está ahí. Un clic cambia un mes en el componente Livewire, sin cargar otro documento. El título muestra el mes en español y el año. No conectar el botón «Hoy». Si el mes inicial sigue PENDIENTE, no elegir «hoy» ni la fecha del plan: los tests fijan el mes de partida. Verificar con un test el mes siguiente y el anterior, y que el visitante no cambia el mes.
- [x] 2.2 Al mostrar la tabla, leer asignado, actividad y disponible de `sobre_meses` para el mes seleccionado. No mostrar los montos de otro mes. Si no hay fila para ese mes, detenerse y preguntar antes de mostrar cero o el disponible anterior. No cambiar la lista de categorías por mes. Verificar con un test que cada mes muestra sus montos y que navegar no copia el disponible.

## 3. Pruebas por requisito

- [x] 3.1 Requisito «Flechas de un mes y título visible»: test de un clic al mes siguiente, de un clic al mes anterior y de que el visitante no cambia el mes. Verificar con `php artisan test --compact` sobre ese test.
- [x] 3.2 Requisito «La tabla es la del mes seleccionado»: test de que cada mes muestra su asignado, su actividad y su disponible. Verificar con `php artisan test --compact` sobre ese test.
- [x] 3.3 Requisito «Cambio sin recargar toda la página»: test de que el título y la tabla cambian sin reemplazar el dashboard, y de que el disponible del mes anterior no se copia al siguiente. Verificar con `php artisan test --compact` sobre ese test.
