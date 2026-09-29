# Design

## Context

El encabezado de `pages::home` muestra «Septiembre 2026» y un botón «Hoy» que no hace nada. No hay flechas. `vista-del-plan` sustituye la tabla fija por las categorías y sobres del plan y deja fuera el cambio de mes; aún no está aplicado. `crear-plan` define el sobre con asignado, actividad y disponible, sin un mes. Motivación: `proposal.md`. Comportamiento: `specs/meses/spec.md`.

## Goals / Non-Goals

**Goals:**

- Dos flechas junto al título. Un clic suma o resta un mes y el título lo dice en español.
- La tabla pide los montos de ese mes. El layout no se vuelve a pedir entero.
- Navegar no copia el disponible al mes siguiente.

**Non-Goals:**

- Activar «Hoy», auto-asignar o crear categorías.
- Decidir el mes inicial, el hueco sin montos y el traspaso del disponible. Dependencias nuevas.

## Decisions

### 1. El mes vive en el componente de la vista

La vista del plan es un componente Livewire. El mes seleccionado es estado de ese componente. La flecha llama a una acción que suma o resta un mes. No es un enlace que cargue otro documento: el cascarón de `layouts/app` no se vuelve a pintar entero.

Alternativa: una ruta por mes (`/2026/09`). Se descarta: recarga la página o abre otra, y el pedido pide un clic sin recargar toda la vista.

Las flechas usan el mismo botón claro del encabezado (fondo blanco, anillo esmeralda). Se distinguen como «Mes anterior» y «Mes siguiente». El título arma el nombre del mes en español y el año, sin depender del locale `en` de la app.

Si el modelo de sobre no existe, detenerse. Si el usuario no tiene un solo plan y el plan abierto sigue PENDIENTE, detenerse. No elegir el mes inicial hasta cerrar esa pregunta: el componente no asume «hoy» ni la fecha del plan.

### 2. Montos por sobre y mes, sin copiar el disponible

Tabla `sobre_meses`: `sobre_id`, año, mes, `assigned`, `activity`, `available` en decimal. El nombre del sobre sigue en el sobre. Una fila por sobre y mes.

La tabla de la vista une los sobres del plan con la fila de ese mes. Al pasar de mes no se inserta una fila que copie el disponible. Si no hay fila para ese mes, no se muestra un cero ni el disponible anterior hasta cerrar la pregunta. No se decide aquí si asignado y actividad son por mes: las tres columnas están en la fila porque la vista las pide del mes seleccionado, y la fórmula de traspaso no se implementa.

No se hace una lista distinta de categorías por mes. Los mismos sobres se listan; cambian los montos. Si la respuesta dice que el conjunto de sobres cambia, se ajusta la consulta.

## Risks / Trade-offs

- [Dejar «Septiembre 2026» fijo y solo añadir flechas] → El título tiene que ser el mes seleccionado. Si no, las flechas mienten.
- [Copiar el disponible al crear el mes siguiente] → Prohibido mientras siga PENDIENTE. No hay job ni observer que lo haga.
- [Elegir el mes actual para poder probar] → Los tests fijan el mes de partida. La vista real no elige el mes inicial hasta la respuesta.
- [Montos solo en el sobre, iguales todos los meses] → La tabla no cambiaría. Por eso los montos van en `sobre_meses`.

## Migration Plan

- Una migración para `sobre_meses`. No se rellenan meses ni se copia el disponible.
- Rollback: revertir esa migración y volver el título al texto fijo. Las flechas salen con la vista.

## Open Questions

El traspaso del disponible, el mes sin montos, si asignado y actividad son por mes, si la lista de sobres cambia, el mes inicial, el botón «Hoy» y el plan abierto están en `proposal.md`. No se cierran al implementar sin cambiar las tareas.
