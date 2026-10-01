# Tasks

## 1. Persistencia

- [x] 1.1 Comprobar que el modelo Plan de `crear-plan` existe. Si no existe, detenerse y preguntar. No crear un plan en este change. Verificar que la clase Plan está en el proyecto.
- [x] 1.2 Crear el modelo Cuenta, su factory y la migración con `plan_id`, `type` (`tarjeta_credito` o `indefinida`), `name`, `money_source` y `balance` decimal, no float. Sin columnas de banco. Verificar el tipo de `balance` en PostgreSQL.
- [x] 1.3 Asociar la cuenta al único plan del usuario. Si no tiene plan, o tiene varios y el plan abierto sigue PENDIENTE en `proposal.md`, detenerse y preguntar. No añadir un selector de plan. Verificar con un test que la cuenta queda en ese plan y que otro usuario no la abre.

## 2. Pop-up de alta

- [x] 2.1 Conectar el botón «Agregar cuenta» del sidebar a un pop-up Livewire de dos pasos, con el estilo de tarjeta y botón que ya usa el dashboard. El primer paso solo ofrece tarjeta de crédito y cuenta indefinida (efectivo), sin opción de banco. Verificar que un visitante no ve el pop-up.
- [x] 2.2 En el segundo paso pedir nombre, de dónde entra el dinero y balance actual. Si el control de «de dónde entra el dinero», los negativos, los decimales o el texto del error siguen PENDIENTE, detenerse y preguntar. No inventar una lista de orígenes. Cuando esté definido, un balance no numérico o un campo vacío muestra el error en el pop-up y no crea la cuenta. Verificar ese rechazo con un test.
- [x] 2.3 Al guardar datos válidos, reemplazar el pop-up por otro con el texto «cuenta creada exitosamente» y persistir tipo y los tres campos. Verificar con un test de tarjeta de crédito y otro de cuenta indefinida, y que un segundo alta deja dos cuentas en el mismo plan.

## 3. Lista y vista

- [x] 3.1 Mostrar cada cuenta del plan en el bloque Cuentas, con el icono ya usado (moneda para indefinida, tarjeta para tarjeta de crédito) y un icono de editar que no navega. Si sigue PENDIENTE quitar las filas fijas o cambiar «Total en Cuentas», detenerse y no hacerlo. Verificar que la cuenta creada se ve con el icono de editar y que el total fijo no se recalcula en este change.
- [x] 3.2 Un clic en la fila, fuera del icono de editar, abre una página Livewire con `layouts/app` de esa cuenta, mostrando su nombre. El resto de la vista sigue PENDIENTE: no construir movimientos ni un formulario de edición. Una cuenta ajena no se abre. Verificar con un test el clic y el rechazo de la cuenta ajena.

## 4. Pruebas por requisito

- [x] 4.1 Requisito «Pop-up para elegir el tipo»: test de las dos opciones, de que no aparece conectar un banco y de que el visitante no abre el pop-up. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.2 Requisito «Tres campos al elegir el tipo»: test de tarjeta de crédito, de cuenta indefinida y de una segunda cuenta en el mismo plan, con balance decimal. Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.3 Requisito «Error en el pop-up y confirmación de éxito»: test de balance no numérico, de un campo vacío y del texto «cuenta creada exitosamente». Verificar con `php artisan test --compact` sobre ese test.
- [x] 4.4 Requisito «Icono de editar y vista propia»: test del icono visible, del clic que abre esa cuenta y de que una cuenta ajena no se abre. Verificar con `php artisan test --compact` sobre ese test.
