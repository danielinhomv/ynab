# Proposal

## Why

Si un sobre gastó más de lo asignado, el usuario tiene que verlo de inmediato y poder cubrirlo moviendo dinero desde otro sobre, sin pasos de más y con el monto que falta ya sugerido. Hoy la fila roja y la tarjeta «Cubrir sobregiro» son estáticas y no mueven dinero.

## What Changes

- Un sobre cuya actividad (el gasto) es mayor que su asignado se muestra en rojo, con el tratamiento visual que ya usa la fila de sobregiro.
- Al hacer clic en ese sobre se abre la tarjeta «Cubrir sobregiro» de la vista actual. La tarjeta nombra el sobre en problema.
- Desde esa tarjeta se elige otro sobre y se confirma el movimiento. El monto sugerido es el faltante (actividad menos asignado). Al confirmar, ese monto se suma al asignado del sobre en problema y se resta del asignado del sobre de origen. La actividad de ambos no cambia.
- Si después del movimiento la actividad ya no supera al asignado, el rojo desaparece. Hay un mensaje claro de resultado.
- No se agrega el aviso «Resolver ahora», ni cubrir desde «dinero por asignar», ni navegación de meses, ni altas o bajas de categorías y sobres, ni ingresos o gastos.

## Capabilities

### New Capabilities

- `sobregasto`: mostrar en rojo el sobre con gasto mayor a lo asignado y cubrirlo moviendo el faltante desde otro sobre.

### Modified Capabilities

- Ninguna. `sobres` no está en las specs principales. El delta en curso de `vista-del-plan` muestra el disponible negativo en rojo y dice que no hay acción para cubrirlo. Este cambio agrega esa acción. No se editan los archivos de ese otro change.

## Impact

- Vista del plan en `resources/views/pages/⚡home.blade.php`: la fila roja (`bg-red-50/70`, disponible en `text-red-600`, badge «Sobregiro») y la tarjeta blanca «Cubrir sobregiro» dejan de ser estáticas. La tarjeta hoy no recibe clics y en pantallas chicas está oculta.
- Asignado del mes visible de dos sobres del mismo plan. Montos en decimal, no float.
- Sin paquetes nuevos y sin rutas nuevas.

## Preguntas abiertas

- El clic para editar la fila, pedido en `vista-del-plan`, y el clic para cubrir este sobregasto coinciden en el mismo sobre. PENDIENTE: si el clic en el sobre rojo abre solo cubrir, o también la edición en la fila.
- La tarjeta estática dice «Mueve fondos de otro sobre y tu dinero listo». Este pedido solo toma dinero de otro sobre. PENDIENTE: si también se puede cubrir desde el dinero por asignar, y si el movimiento cambia ese total.
- PENDIENTE: si el monto sugerido se puede cambiar antes de confirmar, o si la confirmación mueve siempre el faltante completo.
- PENDIENTE: qué pasa si el asignado del sobre de origen es menor que el faltante, y si ese sobre puede quedar con gasto mayor a lo asignado.
- El disponible guardado y su fórmula siguen PENDIENTE en otros changes. Este cambio no la define. PENDIENTE: si, además de asignado, hay que reescribir el disponible guardado.
- PENDIENTE: el texto exacto del mensaje al cubrir, al no haber otro sobre, y al no poder completar el movimiento. La tarjeta ya usa el título «Cubrir sobregiro» y el nombre del sobre.
