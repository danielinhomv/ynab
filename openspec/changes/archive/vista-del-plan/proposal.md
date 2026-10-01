# Proposal

## Why

La vista del presupuesto es HTML fijo: categorías y sobres de ejemplo, con montos que no son del plan. No se puede ver ni corregir el asignado de un sobre real.

## What Changes

- La vista del plan muestra sus categorías y, dentro de cada una, sus sobres. Usa la tabla que ya existe: fila de categoría y filas de sobre, con las columnas Categoría / Sobre, Asignado, Actividad y Disponible.
- Cada sobre muestra nombre, asignado, actividad y disponible. Los tres montos se leen con el number format y el currency placement de ese plan.
- Un clic en el sobre edita sus campos en la misma fila. No abre otra pantalla.
- Un sobre con disponible negativo se muestra en rojo, como la fila que ya está en la tabla. No incluye la acción de cubrirlo.
- Los montos no se guardan como float.
- Un visitante no ve ni edita sobres. Un usuario no edita sobres de un plan ajeno.

Fuera de este change: crear o eliminar categorías y sobres, mover dinero entre sobres, navegación entre meses, dinero por asignar, auto-asignar y cuentas. `crear-plan` guarda ejemplos y prohíbe editarlos; este change es la vista y la edición en fila, y no repite el alta del plan. El catálogo de esos ejemplos sigue PENDIENTE en ese change.

## Capabilities

### New Capabilities

- `sobres`: vista del plan agrupada en categorías y sobres, con montos del plan y edición en la fila. No está en las specs principales. No reutiliza el path `plan` de `crear-plan` y `lista-y-apertura-de-planes`, porque esos deltas son el alta y la lista, no la tabla.

### Modified Capabilities

Ninguna. `app-shell` no define el contenido de la tabla.

## Impact

- El bloque de categorías de `pages::home` deja de ser HTML fijo y lee las categorías y sobres del plan.
- Edición en la misma fila con Livewire, sin una ruta nueva de detalle.
- Los montos usan `number_format` y `currency_placement` del plan. Esas opciones siguen PENDIENTE en `crear-plan`.
- Pruebas de la tabla, del formato, de la edición en fila y del disponible negativo en rojo.

## Preguntas abiertas

- Cómo se crean o eliminan categorías y sobres. Este change no lo incluye.
- Si actividad y disponible se escriben a mano o se calculan al cambiar asignado o actividad. El pedido permite editar los campos del sobre y no da la fórmula.
- Si cambiar el asignado modifica el dinero por asignar. La regla fija habla del déficit sobre ese total; no dice la fórmula de esta edición.
- Si la fila de categoría muestra la suma de sus sobres, como la tabla estática, o solo el nombre.
- Qué plan se muestra si hay varios. `lista-y-apertura-de-planes` aún no cierra cuál queda abierto, ni qué reemplaza al presupuesto estático.
- Los patrones concretos de number format y currency placement. Este change solo los aplica; no define la lista.
