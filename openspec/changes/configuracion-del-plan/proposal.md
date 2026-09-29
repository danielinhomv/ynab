# Proposal

## Why

El alta del plan guarda nombre, moneda, fecha, number format y currency placement, pero no hay dónde corregirlos. Hace falta una pantalla de configuración del plan, a un clic desde el plan, que guarde esos cambios y lo confirme.

## What Changes

- Una pantalla, dentro del dashboard, para editar el plan abierto. Los campos son exactamente: nombre, moneda, fecha, number format y currency placement. Se abren con los valores ya guardados.
- La moneda sigue siendo una sola. El selector reemplaza esa moneda; no agrega otra. Otra moneda en paralelo sigue siendo otro plan.
- Desde la vista del plan, un clic abre esta pantalla. No es el ítem «Configuración» del menú del avatar: ese ítem, por `user-auth`, se ve y no navega. Tampoco es la pantalla de cuenta (`/configuracion`).
- Si falta un dato obligatorio o un valor no es válido, el error queda en el campo y no se guarda. Un guardado válido muestra una confirmación visible.
- Un visitante no entra. No se edita el plan de otro usuario.

## Capabilities

### New Capabilities

- `configuracion-del-plan`: editar nombre, moneda única, fecha, number format y currency placement del plan abierto.

### Modified Capabilities

- Ninguna. `plan` no está en las specs principales. `crear-plan` deja fuera editar el plan; este change no reescribe ese delta. `user-auth` exige que «Configuración» del avatar no navegue, y aquí no cambia.

## Impact

- Pantalla Livewire en `layouts/app`, con la misma tarjeta de formulario que `pages::account-settings` (etiqueta, error bajo el campo, botón `bg-forest`).
- Un control en la vista del plan (`pages::home`), junto a la línea que ya dice «Moneda del plan», abre esa pantalla. No se agrega un ítem al sidebar ni al menú del avatar.
- Actualiza la fila del plan (nombre, moneda, fecha, number format, currency placement). Sin paquetes nuevos.

## Preguntas abiertas

- Cambiar la moneda cuando el plan ya tiene montos guardados (sobres, cuentas u otros). PENDIENTE: si el guardado se bloquea, si los números siguen y solo cambia cómo se muestran, o si se convierten. Hasta resolverlo no se reescriben ni se convierten esos montos.
- Qué significa fecha. Sigue PENDIENTE en `crear-plan`. Este change guarda el valor editado y no define ese significado.
- Lista de monedas, de number format y de currency placement, y qué valor es inválido. Sigue PENDIENTE en `crear-plan`.
- Texto de la confirmación y de los errores. El error se ve en el campo; la confirmación es visible. El texto exacto es PENDIENTE.
- Si, al guardar, el encabezado «Plan activo» y la píldora «Moneda» del cascarón pasan a mostrar los valores nuevos. `crear-plan` lo dejó abierto.
- Qué plan se edita cuando el usuario tiene varios y el plan abierto sigue PENDIENTE en `lista-y-apertura-de-planes`.
