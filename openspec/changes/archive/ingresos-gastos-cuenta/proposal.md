# Proposal

## Why

La vista de una cuenta no registra dinero que entra o sale. Sin un ingreso, el balance queda en el dato inicial y no hay forma de anotar de dónde vino.

## What Changes

- En la vista de la cuenta el usuario autenticado puede añadir un ingreso con un formulario corto, en un envío: fecha, de quién o cómo entra el dinero, descripción (por qué se pagó) y monto.
- La fecha del ingreso abre en hoy. El usuario puede cambiarla.
- Si falta un dato o el monto no es numérico, se ve el error y no se guarda el ingreso ni cambia el balance.
- Tras un ingreso válido hay una confirmación visible y el balance de esa cuenta queda actualizado con ese monto. El monto no se guarda como float.
- También se puede añadir un gasto desde esa vista. Los datos del gasto no fueron especificados: quedan en Preguntas abiertas y como PENDIENTE. Este change no inventa sus campos.
- Un visitante no registra movimientos. No se registran en una cuenta ajena.
- El formulario usa el dashboard y las tarjetas de formulario que ya existen. No hay una pantalla de movimientos en el proyecto hoy; la vista de la cuenta la define `cuentas-del-plan` y su contenido, aparte del nombre, sigue PENDIENTE allí.

Fuera de este change: editar o borrar un movimiento, listarlo en un historial, asignarlo a un sobre, transferir entre cuentas y conectar un banco.

## Capabilities

### New Capabilities

- `movimiento`: alta de un ingreso en la vista de la cuenta, con confirmación y balance actualizado. El gasto se ofrece en esa vista y sus datos quedan PENDIENTE. `cuenta` no está en las specs principales; el change `cuentas-del-plan` cubre el alta de la cuenta y deja el contenido de su vista abierto.

### Modified Capabilities

Ninguna.

## Impact

- Formulario Livewire en la vista de la cuenta (`layouts/app`).
- Persistencia en PostgreSQL del ingreso, ligado a la cuenta, y actualización de su balance en decimal.
- El gasto no se persiste hasta definir sus campos.
- Pruebas del ingreso, de la fecha de hoy, del error, de la confirmación, del balance y de que el gasto no se inventa.

## Preguntas abiertas

- Datos de un gasto. No se especificaron campos (fecha, monto, a quién se pagó, descripción u otros). Hasta responder, el gasto es PENDIENTE y no se arma su formulario.
- Si un gasto, cuando exista, resta el balance, y en qué monto.
- Texto de la confirmación del ingreso y texto de los errores.
- Si el ingreso queda listado en la vista o solo se confirma y se actualiza el balance.
- Si «de quién o cómo entra el dinero» es texto libre o una lista. Este change lo trata como un solo dato del formulario; no hay opciones definidas.
- Si el monto del ingreso se muestra con el number format y el currency placement del plan. Esos patrones siguen PENDIENTE en `crear-plan`.
