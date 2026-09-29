# Design

## Context

En `pages::home`, «+ Nueva categoría» y «+ Nuevo sobre» son botones sin acción, encima de la tabla. La tabla la pinta el componente de `vista-del-plan` con las categorías y sobres del plan más reciente. Categoría (`plan_id`, `name`) y sobre (`category_id`, `name`, `assigned`, `activity`, `available` en decimal) ya existen. Motivación: `proposal.md`. Comportamiento: `specs/alta-categoria-sobre/spec.md`.

## Goals / Non-Goals

**Goals:**

- Conectar los dos botones que ya están, sin una ruta nueva.
- El sobre nuevo guarda `0.00` en los tres montos, como decimal.
- La fila creada se ve en la tabla de esa pantalla.

**Non-Goals:**

- Eliminar, repetir la edición en fila, meses, dinero por asignar y cuentas.
- Otra migración o un plan creado desde estos botones. Dependencias nuevas.

## Decisions

### 1. Escribir en las tablas que ya existen

No hay migración. La categoría cuelga del plan más reciente del usuario autenticado, el mismo criterio que ya usa la tabla. El sobre cuelga de una categoría de ese plan. Los tres montos se guardan como el decimal que ya usa el sobre (`0.00`), no como float.

Si no hay modelos de categoría o sobre, detenerse. No crear un esquema paralelo.

Una categoría de otro usuario no acepta el alta. Si el usuario no tiene plan, no se crea uno.

Alternativa: una tabla nueva solo para esta alta. Se descarta: la vista ya lee categoría y sobre.

### 2. Los botones que ya están, sin otra pantalla

Se conectan «+ Nueva categoría» y «+ Nuevo sobre». No se añaden otros botones ni una ruta de detalle. Al guardar, la tabla de la misma vista muestra la fila.

Si el nombre se pide en un pop-up o en una fila vacía sigue PENDIENTE. No se elige el control ni un texto de confirmación distinto de la fila. El error de nombre vacío es el texto ya dado: «El nombre es obligatorio.»

Con una sola categoría, el alta del sobre la deja elegida. Con varias, el usuario elige; no se marca una por defecto hasta cerrar esa pregunta. Sin categorías, no se inventa el comportamiento del botón.

### 3. No tocar el delta de la vista

`vista-del-plan` dice que su tabla no ofrece crear. Este change no edita ese archivo. Los botones viven fuera de esa tabla, en la cabecera que ya existe.

## Risks / Trade-offs

- [Dos textos sobre «crear»: la vista lo prohíbe en su tabla y este change lo pone en los botones] → No modificar el delta `sobres`. La alta es de estos botones, no un control dentro de la grilla.
- [Elegir pop-up o fila vacía] → Está en `proposal.md`. Parar antes de dibujar el control.
- [Crear el sobre y mover dinero por asignar] → Los tres montos son cero. No se recalcula ese total.

## Migration Plan

- Sin migración. Se insertan filas en las tablas de `crear-plan`.
- Rollback: dejar los dos botones otra vez sin acción. Las filas creadas se borran a mano si hace falta.

## Open Questions

El control del nombre, el alta sin categorías, la categoría marcada cuando hay varias, los nombres repetidos, un texto de confirmación, el lugar de la fila y los botones sin plan están en `proposal.md`. No se cierran al implementar sin cambiar las tareas.
