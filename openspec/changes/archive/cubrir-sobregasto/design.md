# Design

## Context

La tabla de `pages::home` ya pinta una fila en rojo (`bg-red-50/70`, distintivo «Sobregiro», disponible en `text-red-600`) y, encima, una tarjeta blanca «Cubrir sobregiro». Esa tarjeta no recibe clics (`pointer-events-none`) y en pantallas chicas está oculta (`hidden sm:block`). No hay modelo de sobre en la app: `crear-plan` lo propone con asignado, actividad y disponible en decimal, y `navegacion-entre-meses` propone esos montos por mes en `sobre_meses`. Ninguno está aplicado. `vista-del-plan` deja el clic de la fila para editar y prohíbe cubrir. Motivación: `proposal.md`. Comportamiento: `specs/sobregasto/spec.md`.

## Goals / Non-Goals

**Goals:**

- La fila roja y la tarjeta existente pasan a ser la acción de cubrir, en el mismo componente de la vista.
- Un clic en el sobre en rojo abre esa tarjeta, sugiere el faltante y, al confirmar, mueve solo el asignado del mes visible entre dos sobres del mismo plan.

**Non-Goals:**

- Aviso «Resolver ahora», cubrir desde dinero por asignar, fórmula del disponible, edición en fila, flechas de mes, altas de categorías o sobres, ingresos y gastos. Dependencias nuevas.

## Decisions

### 1. La tarjeta que ya está, dentro del componente de la vista

La vista del plan sigue siendo el componente Livewire de `pages::home`, con `layouts/app` y el middleware `auth`. El clic en un sobre con actividad mayor que asignado guarda ese sobre en el estado del componente y muestra la tarjeta. La × que ya está cierra la tarjeta sin guardar. No hay ruta nueva.

Se quita `pointer-events-none` y el ocultamiento en pantallas chicas solo mientras la tarjeta está abierta. Sigue siendo la misma tarjeta: borde `border-red-100`, fondo blanco, `rounded-2xl`, título «Cubrir sobregiro» y el nombre del sobre. Dentro se agregan lo mínimo que la tarjeta estática no trae: la lista de los otros sobres del plan, el faltante sugerido y un botón de confirmar con `bg-forest`.

Alternativa: un modal o una página nueva. Se descarta: el pedido pide la UI actual, y esa tarjeta ya está en la fila.

Si el modelo de sobre no existe, detenerse. Si el usuario no tiene un solo plan y el plan abierto sigue PENDIENTE, detenerse. No se siembra la fila fija «Supermercado».

Si la fila ya abre la edición de `vista-del-plan`, detenerse y no atar los dos clics al mismo sitio hasta cerrar esa pregunta. Este change no reescribe ese delta.

### 2. Mover asignado, no actividad ni el total de arriba

El faltante es actividad menos asignado del sobre en problema, calculado en decimal. Confirmar corre en una transacción: suma ese monto al asignado del sobre en problema y lo resta del asignado del sobre elegido. La actividad no se toca. No se escribe el disponible: su fórmula sigue PENDIENTE. No se toca el dinero por asignar.

Los montos se leen y se guardan como decimal (columna numérica y cast decimal). No se usa float ni se cambia a centavos enteros, para seguir el mismo criterio de los otros changes.

Si la vista ya lee los montos de `sobre_meses`, se actualizan las dos filas de ese año y mes. Si todavía viven en el sobre, se actualizan esas columnas. No se crea `sobre_meses` aquí. Otros meses no se escriben.

El origen tiene que ser otro sobre del mismo plan, comprobando `plan.user_id`. Si su asignado es menor que el faltante, la transacción no se abre. Si no hay otro sobre, no se mueve dinero.

El monto sugerido no se edita hasta cerrar esa pregunta: el botón confirma el faltante calculado. El texto del mensaje de resultado no se inventa: la tarea se detiene antes de fijarlo.

## Risks / Trade-offs

- [Dejar la tarjeta decorativa y cubrir en otro panel] → El usuario no reconocería la UI actual. Se usa esa tarjeta.
- [Pintar el rojo solo si el disponible guardado es negativo] → La fórmula del disponible no está cerrada. El rojo de este change compara actividad con asignado.
- [Restar el origen con float] → El faltante y los dos asignados se operan en decimal, dentro de una transacción.
- [Cubrir y editar la fila con el mismo clic] → Si la edición ya está cableada, se para y se pregunta. No se borra esa edición.
- [Mover asignado y también el dinero por asignar] → El total de arriba no se modifica. La tarjeta estática menciona «dinero listo»; eso sigue en las preguntas abiertas.

## Migration Plan

- Sin migración propia. Se escriben el asignado del sobre o, si ya existe, la fila de `sobre_meses` del mes visible.
- Rollback: quitar la acción del componente y volver la tarjeta a estática. Los asignados ya movidos quedan como quedaron; no hay tabla que revertir.

## Open Questions

Están en `proposal.md`. Cambian el clic compartido con la edición, el dinero por asignar, si el faltante se puede editar, el origen insuficiente, el disponible guardado y el texto de los mensajes. No se cierran al implementar.
