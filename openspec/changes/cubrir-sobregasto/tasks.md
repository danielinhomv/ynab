# Tasks

## 1. Datos del mes visible

- [x] 1.1 Comprobar el modelo de sobre con asignado y actividad en decimal. Si la vista ya lee `sobre_meses`, usar la fila del mes visible; si los montos siguen en el sobre, usar esas columnas. Si no existe ninguno, detenerse y preguntar. No crear `sobre_meses` ni otra migración, y no escribir el disponible. Verificar que la clase que guarda el asignado está en el proyecto.
- [x] 1.2 Resolver el plan: el único del usuario. Si no tiene plan, o tiene varios y el plan abierto sigue PENDIENTE, detenerse y preguntar. Si el clic de la fila ya abre la edición de `vista-del-plan`, detenerse y no atar los dos clics al mismo sitio. No añadir un selector ni reescribir ese otro delta. Verificar que no se editó `openspec/changes/vista-del-plan`.

## 2. Fila roja y tarjeta

- [x] 2.1 En `pages::home`, pintar en rojo el sobre cuya actividad supera a su asignado, con las clases y el distintivo «Sobregiro» de la fila actual. Un sobre que no está en ese caso no usa ese tratamiento. Formatear el monto con el number format y el currency placement del plan. Si esos patrones siguen PENDIENTE en `crear-plan`, detenerse y no copiar `Bs 1.250,00`. No activar «Resolver ahora». No sembrar «Supermercado». Verificar que la fila en problema se distingue y la que no lo está no sale en rojo.
- [x] 2.2 Al hacer clic en ese sobre, abrir en la misma vista la tarjeta «Cubrir sobregiro» (borde rojo, fondo blanco, título y nombre del sobre). Quitar `pointer-events-none` y mostrarla también en pantallas chicas mientras esté abierta. La × cierra sin guardar. Listar solo los otros sobres del mismo plan, mostrar el faltante (actividad menos asignado) y un botón `bg-forest` que confirma ese monto. No ofrecer el mismo sobre ni el dinero por asignar, y no abrir otra ruta. Verificar que la URL no cambia y que la tarjeta nombra el sobre.

## 3. Movimiento

- [x] 3.1 Al confirmar, en una transacción y en decimal, sumar el faltante al asignado del sobre en problema y restarlo del asignado del sobre elegido, solo en el mes visible. No cambiar la actividad, el disponible ni el dinero por asignar. No dejar editar el monto. Si no hay otro sobre, o si el asignado del origen es menor que el faltante, no guardar. Un sobre de otro usuario no puede ser origen. Verificar que los dos asignados cambian en el mismo monto y que el otro mes no cambia.
- [x] 3.2 Si el asignado queda mayor o igual que la actividad, quitar el rojo. El texto del mensaje de resultado, el de no haber otro sobre y el de no poder completar el movimiento siguen PENDIENTE en `proposal.md`: detenerse y preguntar antes de fijarlos. No inventar el texto. Verificar que, tras cubrir, la fila ya no usa el tratamiento rojo.

## 4. Pruebas por requisito

- [x] 4.1 Requisito «Sobre en rojo cuando el gasto supera lo asignado»: test de la fila roja con «Sobregiro» cuando la actividad supera al asignado, del formato del plan, de que un sobre cubierto no sale en rojo y de que el monto no se guarda como float. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.2 Requisito «Clic para cubrir desde otro sobre»: test de que el clic abre la tarjeta en la misma URL, nombra el sobre, lista otro sobre del plan, no ofrece el mismo sobre ni el dinero por asignar, no muestra un sobre ajeno y no usa «Resolver ahora». Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.3 Requisito «Monto faltante sugerido y movimiento en pocos pasos»: test de que confirmar suma y resta el faltante, no cambia la actividad, no toca otros meses y guarda decimal. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.4 Requisito «Al quedar cubierto desaparece el rojo»: test de que el rojo desaparece al quedar cubierto, de que sin otro sobre no se mueve dinero, de que un origen con asignado menor que el faltante no se guarda, y de que disponible y dinero por asignar no cambian. El texto exacto del mensaje queda fuera hasta cerrar la pregunta. Verificar con `php artisan test --compact` sobre ese test.
