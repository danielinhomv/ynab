# Tasks

## 1. Datos del plan

- [x] 1.1 Comprobar que existen los modelos de categoría y sobre de `crear-plan`, con asignado, actividad y disponible en decimal, y el plan con `number_format` y `currency_placement`. Si no existen, detenerse y preguntar. No crear otra migración. Verificar que esas clases están en el proyecto.
- [x] 1.2 Resolver el plan a mostrar: el único del usuario. Si no tiene plan, o tiene varios y el plan abierto sigue PENDIENTE en `proposal.md`, detenerse y preguntar. No añadir un selector. Verificar con un test que la vista no incluye un sobre de otro usuario.

## 2. Tabla

- [x] 2.1 Sustituir la tabla fija de `pages::home` por un componente Livewire con la misma grilla: categoría y, debajo, sus sobres con nombre, asignado, actividad y disponible. No mostrar controles para crear o eliminar. No conectar «+ Nueva categoría» ni «+ Nuevo sobre». Si la suma de la fila de categoría sigue PENDIENTE, no calcularla. Verificar que se ven la categoría y los cuatro datos del sobre, y que no hay alta ni baja.
- [x] 2.2 Formatear asignado, actividad y disponible con el number format y el currency placement del plan. Si esos patrones siguen PENDIENTE en `crear-plan`, detenerse y no copiar `Bs 1.250,00` como único formato. Un disponible negativo usa las clases rojas de la fila actual y no muestra «Cubrir sobregiro». Verificar con un test el formato del plan y el rojo, y que el monto no se guarda como float.

## 3. Edición en la fila

- [x] 3.1 Al hacer clic en un sobre, editar nombre, asignado, actividad y disponible en esa fila, sin otra ruta ni pop-up. Guardar el asignado numérico. Un monto no numérico muestra el error en la fila y no cambia el valor. No editar un sobre ajeno. Si la fórmula de disponible o el efecto en el dinero por asignar siguen PENDIENTE, no calcularlos ni tocar ese total. Verificar con un test el guardado, el rechazo y que la URL no cambia.

## 4. Pruebas por requisito

- [x] 4.1 Requisito «Categorías y sobres del plan»: test de la categoría con el sobre y sus cuatro datos, de que no se ve un sobre ajeno y de que no hay control de crear o eliminar. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.2 Requisito «Montos con el formato del plan»: test del number format y currency placement del plan, y del disponible negativo en rojo sin acción de cubrir. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.3 Requisito «Edición en la misma fila»: test de que el clic no cambia de pantalla, de que el asignado numérico se guarda, de que un monto no numérico no se guarda y de que un sobre ajeno no cambia. Verificar con `php artisan test --compact` sobre ese test.
