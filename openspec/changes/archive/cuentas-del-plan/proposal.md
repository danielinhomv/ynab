# Proposal

## Why

El botón «Agregar cuenta» del sidebar no hace nada, y Efectivo, Banco Sol y Banco Ganadero son texto fijo. Un plan no puede registrar de dónde entra el dinero.

## What Changes

- «Agregar cuenta» abre un pop-up para elegir el tipo: tarjeta de crédito o cuenta indefinida (efectivo).
- Al elegir el tipo, el mismo flujo pide tres campos: nombre, de dónde entra el dinero y balance actual (numérico). No hay conexión a un banco real.
- Al crear la cuenta se muestra un pop-up con el texto «cuenta creada exitosamente». Si falta un dato o el balance no es numérico, se ve el error en el pop-up y no se crea la cuenta.
- La cuenta queda en el plan. Un plan tiene de 1 a N cuentas. Efectivo, Banco Sol y Banco Ganadero ilustran ese rango; no son un semillero.
- En la lista de Cuentas del sidebar, cada cuenta muestra un icono de editar. Un clic en la cuenta (no en ese icono) abre su vista propia.
- La lista usa el bloque Cuentas que ya existe (icono, nombre, botón «Agregar cuenta»). Un visitante no crea ni ve cuentas ajenas.

Fuera de este change: borrar cuentas, transferencias, movimientos, conectar un banco, editar el plan y el formulario de alta del plan. `crear-plan` dejó las cuentas fuera; este change no mete el alta de la cuenta dentro de ese formulario.

## Capabilities

### New Capabilities

- `cuenta`: alta de una cuenta del plan (tipo, tres campos y confirmación), lista en el sidebar y vista propia al hacer clic.

### Modified Capabilities

Ninguna. `app-shell` ya describe la barra lateral con cuentas; este change no cambia ese requisito. `plan` todavía no está en las specs principales.

## Impact

- El botón y la lista de `layouts/app` pasan de ser HTML fijo a cuentas del plan.
- Persistencia en PostgreSQL de la cuenta, asociada a un plan del usuario. El balance no se guarda como float.
- Pop-ups con el mismo lenguaje visual del dashboard (tarjeta blanca, botón verde). No hay un modal en el proyecto hoy.
- Pruebas del tipo, de los tres campos, del pop-up de éxito, del clic a la vista y de que no se crea una cuenta inválida.

## Preguntas abiertas

- Qué es «de dónde entra el dinero»: texto libre o una lista cerrada. No hay opciones definidas.
- Qué hace el icono de editar. Este pedido lo muestra; no dice qué campos se pueden cambiar ni si el tipo se puede cambiar.
- Qué muestra la vista propia de la cuenta, además de ser la de esa cuenta y no la de otra.
- A qué plan se agrega la cuenta si hay varios. `lista-y-apertura-de-planes` aún no cierra cuál es el plan abierto.
- Cómo se obliga el mínimo de una cuenta. `crear-plan` no crea cuentas, y este change no incluye el alta dentro de ese formulario.
- Si el balance puede ser negativo o con decimales, y el texto de los errores. El de éxito sí está definido.
- Si «Total en Cuentas» del sidebar pasa a ser la suma de los balances. Hoy es un monto fijo y este pedido no lo menciona.
- Si las cuentas fijas del sidebar (Efectivo, Banco Sol, Banco Ganadero) se reemplazan por las del plan o conviven con ellas.
