# Design

## Context

El dashboard autenticado es `layouts/app`. El encabezado muestra «Plan activo / Presupuesto Personal» y «Moneda: Bs (BOB)» como texto fijo. El sidebar tiene navegación y cuentas, también fijas. No hay modelo `Plan` en la aplicación: el change `crear-plan` lo define (`user_id`, `name`, `currency` única) y aún no está aplicado. Motivación: `proposal.md`. Comportamiento: `specs/plan/spec.md`.

## Goals / Non-Goals

**Goals:**

- Una sola consulta de los planes del usuario autenticado, y un control por plan que lo abre en ese clic.
- El plan abierto se distingue con el mismo estilo de ítem activo que ya usa la navegación (`bg-mint`, `text-forest`).
- El botón de crear es un enlace al alta de `crear-plan`, no un segundo formulario.

**Non-Goals:**

- Otra tabla o una segunda columna de moneda.
- Tocar el presupuesto estático del home, el encabezado «Plan activo» o la pastilla de moneda hasta cerrar esas preguntas.
- Un ítem nuevo en «Navegación» (Informes, Cuentas). Dependencias nuevas.

## Decisions

### 1. Reusar el plan de `crear-plan`

La lista lee `name` y `currency` del plan de ese usuario. No se crea otra migración ni otro modelo. Si el modelo `Plan` todavía no existe, detenerse: no inventar un esquema paralelo.

Alternativa: una tabla solo para esta lista. Se descarta: duplicaría el plan y su moneda.

Abrir un id que no pertenece al usuario no abre nada. El filtro es `user_id` del usuario autenticado, el mismo criterio que ya usa la sesión de Laravel. No hace falta otro sistema de autorización.

### 2. Un clic, sin paso extra

Cada ítem es un solo control (enlace o acción Livewire). No hay botón «Abrir» aparte ni diálogo de confirmación. El ítem abierto usa las clases del enlace activo del sidebar, para reconocerlo sin un componente nuevo.

No se guarda «último plan abierto» en la base. Hacerlo decidiría que se recuerda en la siguiente visita. Hasta cerrar esa pregunta, el plan abierto es el de esta navegación y nada más.

### 3. El botón no construye el formulario

El botón usa las clases del botón primario que ya existe (`bg-forest`, texto blanco). Su destino es la pantalla de alta de `crear-plan`. Si esa ruta sigue PENDIENTE, no se elige una URL ni se monta aquí el formulario de nombre, multimoneda, fecha, number format y currency placement.

El estado vacío es un bloque con el mismo lenguaje visual (tarjeta blanca, texto corto) y el mismo botón. El texto es «Aún no tienes planes.»

### 4. Dónde se monta, todavía no

El bloque (lista o estado vacío, más el botón) es un componente Livewire dentro de `layouts/app`. No se inserta en el sidebar ni en el área principal hasta responder dónde vive. Insertarlo en uno de los dos cambia `layouts/app` o `pages::home`, y esa elección sigue en las preguntas abiertas.

## Risks / Trade-offs

- [Dos deltas `specs/plan/spec.md`, uno en `crear-plan` y otro aquí] → No repetir el alta en este delta. Al archivar, el `Purpose` que ya esté en la spec principal se conserva; este no lo pisa.
- [Listar planes antes de que exista la tabla] → Parar si `Plan` no está. No migrar por cuenta propia.
- [El home estático parece un plan cuando la lista está vacía] → El estado vacío dice que no hay planes. No reemplazar el presupuesto de ejemplo hasta cerrar esa pregunta.
- [Decidir sidebar, encabezado, destino del botón, orden o recuerdo del plan abierto] → Esas preguntas están en `proposal.md`. Parar antes de elegirlas.

## Migration Plan

- Sin migración propia. Si `crear-plan` ya creó `plans`, esta pantalla solo la lee.
- Rollback: quitar el componente de la interfaz. No hay datos que revertir.

## Open Questions

Las que cambian el lugar de la lista, el contenido al abrir, el encabezado, la ruta del botón, el orden y el recuerdo del plan abierto están en `proposal.md`. No se pueden cerrar al implementar sin cambiar las tareas.
