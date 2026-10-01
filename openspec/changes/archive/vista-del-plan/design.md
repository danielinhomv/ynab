# Design

## Context

`pages::home` pinta una tabla fija: cabecera «Categoría / Sobre», «Asignado», «Actividad», «Disponible»; fila de categoría en `bg-mint/50`; filas de sobre; un disponible negativo en `bg-red-50/70` y `text-red-600`. Encima hay botones «+ Nueva categoría» y «+ Nuevo sobre» que no hacen nada. `crear-plan` define categoría (`plan_id`, `name`) y sobre (`category_id`, `name`, `assigned`, `activity`, `available` en decimal) y aún no está aplicado. También guarda `number_format` y `currency_placement`, con las listas PENDIENTE. Motivación: `proposal.md`. Comportamiento: `specs/sobres/spec.md`.

## Goals / Non-Goals

**Goals:**

- La misma tabla, leída del plan, con edición de la fila en el sitio.
- Los tres montos salen de un solo formateador que usa el number format y el currency placement del plan.
- El disponible negativo reutiliza las clases rojas que ya tiene la fila. No hay acción de cubrir.

**Non-Goals:**

- Otra tabla de categorías, otra ruta de detalle, ni fórmula de disponible o de dinero por asignar.
- Conectar «+ Nueva categoría» y «+ Nuevo sobre». Dependencias nuevas.

## Decisions

### 1. Reusar categoría y sobre de `crear-plan`

No hay migración propia. Si esos modelos no existen, detenerse. No crear un esquema paralelo.

La consulta filtra por el plan del usuario autenticado. Si tiene un solo plan, se usa ese. Si no tiene, o tiene varios y el plan abierto sigue PENDIENTE, detenerse. No inventar un selector.

Un sobre se actualiza solo si su categoría pertenece a un plan de ese usuario.

### 2. Edición en la fila, sin ruta nueva

La tabla pasa a un componente Livewire dentro de `pages::home`, con las mismas clases de grilla. Un clic marca esa fila como editable y sustituye nombre, asignado, actividad y disponible por campos. Guardar ocurre ahí. No hay página de detalle ni pop-up.

Alternativa: un formulario en otra ruta. Se descarta: el pedido pide no cambiar de pantalla.

No se calcula disponible ni se toca el dinero por asignar. Los tres montos se guardan como decimal, el valor editado, hasta cerrar esa pregunta. Un monto no numérico no escribe.

La fila de categoría muestra el nombre. No se suman los sobres hasta cerrar esa pregunta.

Los botones «+ Nueva categoría» y «+ Nuevo sobre» no se conectan. La tabla de este change no los incluye: el requisito prohíbe ese control.

### 3. Formato del plan, no el de la maqueta

Un solo punto arma el texto de asignado, actividad y disponible a partir de `number_format` y `currency_placement`. No se copia `Bs 1.250,00` como único formato. Si los patrones siguen PENDIENTE en `crear-plan`, detenerse antes de inventar la lista. El componente solo llama a ese formateador.

El disponible negativo usa las clases rojas de la fila estática. No se copia el recuadro «Cubrir sobregiro».

## Risks / Trade-offs

- [Pintar la tabla antes de que existan categoría y sobre] → Parar. No migrar por cuenta propia.
- [Guardar disponible a mano y luego calcularlo] → No hay fórmula en este change. No restar actividad del asignado hasta la respuesta.
- [Dejar los botones «+ Nueva categoría» y «+ Nuevo sobre»] → Parecen que crean. No conectarlos; la tabla no los muestra.
- [Varios planes] → No elegir uno al azar. Preguntar si el plan abierto sigue PENDIENTE.

## Migration Plan

- Sin migración. Se leen y actualizan las tablas de `crear-plan`.
- Rollback: volver el bloque de `pages::home` al HTML fijo. No hay datos nuevos que borrar.

## Open Questions

Crear o eliminar, la fórmula de actividad y disponible, el efecto en el dinero por asignar, la suma de la fila de categoría, qué plan se muestra y los patrones de formato están en `proposal.md`. No se cierran al implementar sin cambiar las tareas.
