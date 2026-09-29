# Proposal

## Why

«+ Nueva categoría» y «+ Nuevo sobre» se ven en la vista del plan y no crean nada. Sin eso, el usuario solo tiene las categorías y sobres que nacieron con el plan.

## What Changes

- «+ Nueva categoría» pide solo el nombre y crea la categoría en el plan que ya se muestra: el más reciente del usuario. Si el nombre está vacío, muestra «El nombre es obligatorio.» y no crea la categoría.
- «+ Nuevo sobre» pide el nombre y la categoría de ese plan. Si el plan tiene una sola categoría, esa queda elegida. Asignado, actividad y disponible empiezan en 0.00 y no se guardan como float. Si el nombre está vacío, muestra «El nombre es obligatorio.» y no crea el sobre.
- Al guardar, la fila nueva aparece en la tabla de esa misma pantalla. No hay otra ruta.
- Un visitante no crea categorías ni sobres. No se crea nada en un plan de otro usuario.

Fuera de este change: eliminar categorías o sobres, la edición en la fila (ya está en `vista-del-plan`), meses, dinero por asignar y cuentas. Este change no repite la tabla ni el alta del plan.

## Capabilities

### New Capabilities

- `alta-categoria-sobre`: alta de una categoría por nombre y de un sobre en una categoría del plan que ya se muestra, con montos iniciales en cero decimal.

### Modified Capabilities

Ninguna. `sobres` no está en las specs principales. El delta de `vista-del-plan` dice que esa vista no ofrece crear; este change no edita ese archivo. Al archivar, el `Purpose` de `sobres` no se pisa.

## Impact

- Los dos botones de `pages::home` pasan de no hacer nada a crear en el plan más reciente.
- Se escriben filas en las tablas de categoría y sobre que ya existen. No hay migración nueva. El balance de los tres montos del sobre nuevo es decimal, no float.
- La fila creada se ve en la tabla actual, sin cambiar de pantalla.
- Pruebas del nombre vacío, de la categoría única preelegida, de los tres ceros decimales y de que no se crea en un plan ajeno.

## Preguntas abiertas

- Si el nombre se pide en un pop-up de la misma pantalla o en una fila vacía de la tabla. El pedido no elige el control; sí pide no cambiar de pantalla y ver la fila al guardar.
- Qué hace «+ Nuevo sobre» si el plan no tiene ninguna categoría. Con una sola, esa queda elegida. Con varias, el usuario elige; no está dicho cuál aparece marcada al abrir.
- Si dos categorías o dos sobres del mismo plan pueden llamarse igual.
- Si hace falta un texto de confirmación además de ver la fila. El pedido no lo da.
- En qué lugar de la tabla queda la fila nueva.
- Qué hacen los botones si el usuario no tiene ningún plan. Este change no crea un plan.
