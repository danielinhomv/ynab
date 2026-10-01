# Tasks

## 1. Datos del usuario

- [x] 1.1 Comprobar que el modelo Plan de `crear-plan` existe, con `user_id`, `name` y una sola `currency`. Si no existe, detenerse y preguntar. No crear otra migración ni otro modelo. Verificar que la clase Plan y esa columna están en el proyecto.

## 2. Lista y estado vacío

- [x] 2.1 Crear el componente Livewire que lista solo los planes del usuario autenticado, cada uno con su nombre y su única moneda. El plan abierto usa `bg-mint` y `text-forest`. No añadir un ítem en Navegación, no cambiar el encabezado «Plan activo» ni el presupuesto estático del home. Verificar con un test que se ven los dos nombres y sus monedas, y que el plan de otro usuario no aparece.
- [x] 2.2 Si el usuario no tiene planes, mostrar «Aún no tienes planes.» y el botón de crear, sin ítems de plan. Verificar con un test de un usuario sin planes.
- [x] 2.3 Si el lugar de la lista sigue PENDIENTE en `proposal.md`, detenerse y preguntar. No montar el componente en el sidebar ni en el área principal antes de esa respuesta. Cuando esté respondida, montarlo ahí y verificar que se ve dentro del dashboard autenticado.

## 3. Abrir y crear otro

- [x] 3.1 Hacer que cada plan sea un solo control: un clic lo abre, sin botón «Abrir» aparte ni confirmación. Un id de otro usuario no queda abierto. No guardar un último plan abierto en la base. Verificar con un test que ese clic deja ese plan distinguido como abierto y que el ajeno no se abre.
- [x] 3.2 Mostrar el botón de crear un plan nuevo con planes y sin planes. No renderizar los campos del alta. Si la ruta del formulario de `crear-plan` sigue PENDIENTE, detenerse y preguntar antes de elegir el enlace. Verificar con un test que el botón está, que no aparecen los campos del formulario y que la moneda del plan ya abierto no cambia.

## 4. Pruebas por requisito

- [x] 4.1 Requisito «Lista de planes del usuario»: test de nombre y moneda única, de que no se ve un plan ajeno y de que el visitante no ve la lista. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.2 Requisito «Abrir un plan en un clic»: test de que un clic abre ese plan sin paso intermedio y de que un plan ajeno no se abre. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.3 Requisito «Botón para crear otro plan»: test de que el botón se ve cuando ya hay planes y de que usarlo no cambia la moneda del plan abierto. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.4 Requisito «Estado vacío sin planes»: test del texto «Aún no tienes planes.», del botón de crear y de que no hay ítems de plan. Verificar con `php artisan test --compact` sobre ese test.
