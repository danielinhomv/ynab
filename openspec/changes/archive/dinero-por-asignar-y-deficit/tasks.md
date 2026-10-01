# Tasks

## 1. Tarjeta del mes visible

- [x] 1.1 Resolver el plan: el único del usuario. Si no tiene plan, o tiene varios y el plan abierto sigue PENDIENTE, detenerse y preguntar. No añadir un selector. No editar los changes `vista-del-plan`, `ingresos-gastos-cuenta` ni `cubrir-sobregasto`. Verificar que esos directorios no cambiaron.
- [x] 1.2 En `pages::home`, mostrar el dinero por asignar del mes visible en la tarjeta que ya tiene esa etiqueta, al renderizar, sin otra ruta. Conservar la grilla, el anillo esmeralda y el icono en `bg-mint`. Formatear con el number format y el currency placement del plan. Si esos patrones siguen PENDIENTE en `crear-plan`, detenerse y no usar «Bs 1.250,00» como total. No activar «Ingresos del Mes», «Presupuestado», «Gastado hasta hoy», «Auto-asignar» ni «Resolver ahora». Verificar que la tarjeta se ve en la misma URL.

## 2. Déficit acumulado

- [x] 2.1 El origen del ingreso, el origen de lo presupuestado, el total propio, si el sobrante reduce el arrastre y si el déficit del mes visible entra en su propio número siguen PENDIENTE en `proposal.md`. Detenerse y preguntar antes de calcular. No crear tabla ni columna. No escribir nombre, asignado, actividad ni disponible de un sobre, ni copiar el disponible al mes siguiente. Verificar que no hay migración nueva y que un sobre de prueba conserva sus montos.
- [x] 2.2 Cuando esas respuestas existan, calcular en decimal el dinero por asignar del mes visible como su total propio menos la suma de los déficit de los meses anteriores. Un mes suma déficit solo si lo presupuestado supera al ingreso. Un mes que no lo supera no suma. No usar float ni centavos enteros. Verificar que el mes siguiente resta ese déficit y que dos déficit seguidos se suman en el tercero.

## 3. Monto negativo

- [x] 3.1 Si el total es negativo, pintar solo el monto con `text-red-600`. Si no lo es, dejarlo en `text-slate-900`. No pintar la tarjeta como el aviso de sobregiro. Verificar los dos estados en la misma tarjeta.

## 4. Pruebas por requisito

- [x] 4.1 Requisito «Total arriba, siempre en la vista del plan»: test de la tarjeta «Dinero por asignar» en la misma vista, del formato del plan, de que no es el total de otro usuario y de que no se activan las otras tarjetas ni «Auto-asignar» ni «Resolver ahora». Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.2 Requisito «El déficit se descuenta del mes siguiente y se acumula»: test de que el mes siguiente resta el déficit, de que dos déficit se suman en el tercero, de que un mes sin déficit no se arrastra, de que los sobres no cambian y de que el monto no es float. Si las preguntas de 2.1 siguen abiertas, detenerse y no afirmar un origen de ingreso ni de presupuestado. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.3 Requisito «El total negativo se distingue»: test del monto en rojo cuando es negativo y del estilo positivo cuando no lo es, en la misma tarjeta. Verificar con `php artisan test --compact` sobre ese test.
