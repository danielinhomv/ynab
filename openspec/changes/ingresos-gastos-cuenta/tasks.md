# Tasks

## 1. Persistencia del ingreso

- [x] 1.1 Comprobar que el modelo Cuenta de `cuentas-del-plan` existe, con `balance` decimal. Si no existe, detenerse y preguntar. No crear la cuenta en este change. Verificar que la clase Cuenta está en el proyecto.
- [x] 1.2 Crear el modelo Ingreso, su factory y la migración con `cuenta_id`, `fecha`, `origen`, `descripcion` y `monto` decimal, no float. Sin tabla de gastos. Verificar el tipo de `monto` en PostgreSQL.
- [x] 1.3 En una transacción, guardar el ingreso y sumar el monto al balance de la cuenta, solo si la cuenta es del usuario autenticado. Si la validación falla, no hay fila y el balance no cambia. Verificar con un test que una cuenta ajena no recibe el ingreso.

## 2. Formulario en la vista de la cuenta

- [x] 2.1 Añadir en la vista de la cuenta un formulario con la tarjeta de los formularios actuales y solo estos campos: fecha, de quién o cómo entra el dinero, descripción y monto. La fecha abre en hoy. Un envío sin cambiarla guarda la fecha de hoy. No abrir otra pantalla. Verificar con un test esos cuatro campos y la fecha de hoy.
- [x] 2.2 Rechazar un monto no numérico o un campo vacío: mostrar el error en el formulario y no cambiar el balance. Si el texto del error o el de la confirmación siguen PENDIENTE en `proposal.md`, detenerse y preguntar antes de redactarlos. Cuando estén definidos, un ingreso válido muestra la confirmación en la misma vista y el balance es el anterior más el monto. Verificar con un test el rechazo y la suma.
- [x] 2.3 No crear formulario, botón que guarde ni tabla de gasto mientras sus campos sigan PENDIENTE. No modificar sobres ni el dinero por asignar. Verificar que no existe una tabla de gastos y que un recorrido de la vista no guarda un gasto.

## 3. Pruebas por requisito

- [x] 3.1 Requisito «Formulario corto de ingreso»: test de los cuatro campos, de la fecha de hoy sin cambiarla y de que una cuenta ajena no recibe el ingreso. Verificar con `php artisan test --compact` sobre ese test.
- [x] 3.2 Requisito «Error sin cambiar el balance»: test de monto no numérico y de un campo vacío, sin cambio de balance. Verificar con `php artisan test --compact` sobre ese test.
- [x] 3.3 Requisito «Confirmación y balance actualizado»: test de la confirmación visible y de que el balance es el anterior más el monto, guardado en decimal. Verificar con `php artisan test --compact` sobre ese test.
- [x] 3.4 Requisito «Gasto pendiente de definir»: test de que no se guarda un gasto y el balance no cambia por un gasto. Verificar con `php artisan test --compact` sobre ese test.
