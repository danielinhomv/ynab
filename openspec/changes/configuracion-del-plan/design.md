# Design

## Context

No hay modelo de plan en la app. `crear-plan` propone la tabla `plans` con `user_id`, `name`, `currency` (un string), `fecha`, `number_format` y `currency_placement`, y deja fuera editarlos. La vista del plan es `pages::home`: la línea «Moneda del plan» está en esa página. El bloque «Plan activo» del encabezado está oculto bajo `md`. `/configuracion` es la cuenta (`pages::account-settings`): tarjeta `rounded-2xl`, etiqueta, `@error` y botón `bg-forest`. El ítem «Configuración» del avatar no navega. Motivación: `proposal.md`. Comportamiento: `specs/configuracion-del-plan/spec.md`.

## Goals / Non-Goals

**Goals:**

- Un clic en la vista del plan abre el formulario con los cinco valores guardados.
- Un envío actualiza esa fila y deja una sola moneda. Con montos ya guardados, no los toca.

**Non-Goals:**

- La pantalla de cuenta, el ítem del avatar, un ítem nuevo de sidebar, otro plan, cuentas, sobres y el encabezado «Plan activo». Dependencias nuevas.

## Decisions

### 1. Otra pantalla, el mismo formulario

Componente Livewire de una vista, `#[Layout('layouts.app')]`, con el marcado de `pages::account-settings`. Ruta nueva con `middleware('auth')`, distinta de `configuracion`, nombre `plan.configuracion`. El visitante no la monta.

El acceso es un enlace en `pages::home`, en la línea «Moneda del plan», que ya está en la vista del plan y se ve sin el corte `md` del encabezado. Un clic. No se usa el avatar ni el sidebar: el primero no puede navegar y el segundo no tiene un hueco de configuración del plan.

Alternativa: reutilizar `/configuracion`. Se descarta: esa ruta es la cuenta, y el pedido es la configuración del plan.

Si el modelo de plan no existe, detenerse. Si el usuario no tiene un solo plan y el plan abierto sigue PENDIENTE, detenerse. No añadir un selector. Solo se carga el plan cuyo `user_id` es el usuario autenticado. Los campos muestran `name`, `currency`, `fecha`, `number_format` y `currency_placement`.

### 2. Actualizar la fila, sin tocar montos

Guardar escribe esas cinco columnas del plan. `currency` sigue siendo un solo string: el valor nuevo reemplaza al anterior. No hay segunda moneda ni se crea otro plan.

`fecha` se guarda como date. Qué representa sigue PENDIENTE; no se usa `created_at` en su lugar.

Si el plan ya tiene montos (asignado, actividad, disponible, balance u otros), no se cambia `currency` y no se reescriben ni se convierten esos montos. La tarea se detiene antes de elegir entre bloquear el guardado, dejar los números o convertirlos. Sin montos, el selector sí reemplaza la moneda.

El nombre vacío no escribe. Las listas de moneda, number format y currency placement siguen PENDIENTE: no se inventan opciones ni el texto de la confirmación. La confirmación es un mensaje en la misma tarjeta, visible tras un guardado válido. El encabezado y la píldora «Moneda» no se actualizan hasta cerrar esa pregunta.

## Risks / Trade-offs

- [Abrir la cuenta desde «Moneda del plan»] → El enlace apunta a `plan.configuracion`, no a `configuracion`.
- [Hacer clicable «Plan activo»] → Ese bloque no está en pantallas chicas. El clic está en la vista del plan.
- [Convertir montos al cambiar la moneda] → Prohibido mientras siga PENDIENTE. No hay tipo de cambio ni se escriben sobres o cuentas.
- [Inventar el catálogo de monedas para poder guardar] → Sin la lista, no se validan opciones nuevas. Guardar el valor ya persistido no exige inventarla.

## Migration Plan

- Sin migración. Se actualiza la fila de `plans` que cree `crear-plan`.
- Rollback: quitar la ruta y el enlace. Los valores ya guardados quedan en el plan.

## Open Questions

Están en `proposal.md`. Cambian la moneda con montos, el significado de la fecha, los catálogos, el texto de la confirmación, el encabezado y el plan abierto. No se cierran al implementar.
