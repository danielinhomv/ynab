# Tasks

## 1. Datos

- [x] 1.1 Comprobar que existen los modelos de categoría (`plan_id`, `name`) y sobre (`category_id`, `name`, `assigned`, `activity`, `available` decimal) de `crear-plan`. Si no existen, detenerse y preguntar. No crear otra migración. Verificar que esas clases están en el proyecto.

## 2. Alta

- [x] 2.1 Conectar el botón «+ Nueva categoría» que ya está en la vista del plan, sin añadir otro botón ni otra ruta. La categoría queda en el plan más reciente del usuario. Si el control del nombre (pop-up o fila vacía) sigue PENDIENTE en `proposal.md`, detenerse y preguntar antes de dibujarlo. Cuando esté definido, un nombre vacío muestra «El nombre es obligatorio.» y no crea la categoría; un nombre válido muestra la fila en la misma pantalla. Un visitante no crea. Verificar con un test el alta, el nombre vacío y el visitante.
- [x] 2.2 Conectar «+ Nuevo sobre». Pedir nombre y categoría de ese plan. Si hay una sola categoría, dejarla elegida. Asignado, actividad y disponible en `0.00` decimal, no float. Nombre vacío: «El nombre es obligatorio.» y no se crea. Una categoría de otro usuario no recibe el sobre. Si no hay categorías, o hay varias y cuál queda marcada sigue PENDIENTE, detenerse y no elegir una por defecto. Verificar con un test la categoría única, los tres ceros como texto decimal, el nombre vacío y el rechazo de la categoría ajena.
- [x] 2.3 Al guardar, la fila se ve en la tabla de esa pantalla. No añadir eliminar, no tocar el total de dinero por asignar y no editar el delta `sobres` de `vista-del-plan`. Verificar que la fila creada está en la respuesta de la misma vista y que no aparece un control de eliminar.

## 3. Pruebas por requisito

- [x] 3.1 Requisito «Crear una categoría por nombre»: test del nombre que crea la categoría en el plan más reciente y la muestra sin cambiar de ruta, del texto «El nombre es obligatorio.» y de que el visitante no crea. Verificar con `php artisan test --compact` sobre ese test.
- [x] 3.2 Requisito «Crear un sobre en una categoría del plan»: test de la única categoría preelegida, de asignado, actividad y disponible en `0.00` sin float, del nombre vacío y de que una categoría ajena no recibe el sobre. Verificar con `php artisan test --compact` sobre ese test.
