# Proposal

## Why

El plan es mensual, pero el título «Septiembre 2026» es texto fijo y no hay forma de pasar al mes anterior o al siguiente. La tabla de categorías y sobres no puede mostrar otro mes.

## What Changes

- Arriba en la vista del plan, junto al título del mes que ya existe, hay una flecha al mes anterior y una al mes siguiente. Un clic avanza o retrocede un mes.
- El mes seleccionado se lee en el título, con el nombre del mes y el año, como el encabezado actual.
- La vista de categorías y sobres muestra asignado, actividad y disponible de ese mes. El cambio no recarga toda la página: el cascarón sigue y se actualizan el título y la tabla.
- El traspaso del disponible de un sobre al mes siguiente no se decide aquí. Sigue PENDIENTE.
- Un visitante no cambia el mes de un plan. El mes es el del plan del usuario.

Fuera de este change: el botón «Hoy», auto-asignar, crear o eliminar categorías, editar sobres, cuentas e ingresos. `vista-del-plan` dibuja la tabla y deja la navegación entre meses fuera; este change solo añade el mes y no repite la edición en fila.

## Capabilities

### New Capabilities

- `meses`: flechas de un mes, título visible del mes seleccionado y tabla de categorías y sobres de ese mes, sin recargar toda la página. `sobres` no está en las specs principales; lo introduce `vista-del-plan`.

### Modified Capabilities

Ninguna. `app-shell` no define el título del mes.

## Impact

- El encabezado de `pages::home` deja de ser el texto fijo «Septiembre 2026» y gana dos flechas, con el mismo estilo de botón que ya usa esa barra.
- Los montos del mes se leen aparte del nombre del sobre. No se copia el disponible al mes siguiente en este change.
- La actualización es de Livewire, sin un documento nuevo del layout.
- Pruebas de un clic por mes, del título, de la tabla de ese mes y de que el disponible no se traspasa mientras siga PENDIENTE.

## Preguntas abiertas

- Si el disponible de un sobre pasa al mes siguiente. Está marcado PENDIENTE de confirmar. Este change no lo copia ni lo pone en cero por su cuenta.
- Qué se muestra en un mes que aún no tiene montos guardados: vacío, cero, o el disponible arrastrado.
- Si asignado y actividad también son propios de cada mes, o solo el disponible.
- Si la lista de categorías y sobres cambia con el mes, o solo sus montos. Crear y eliminar categorías sigue abierto en `vista-del-plan`.
- Qué mes aparece al entrar: el mes calendario actual, o el de la fecha del plan. El significado de esa fecha sigue PENDIENTE en `crear-plan`.
- Si el botón «Hoy», que ya está junto al título y no hace nada, lleva al mes actual.
- Qué plan se navega si hay varios. `lista-y-apertura-de-planes` aún no cierra el plan abierto.
