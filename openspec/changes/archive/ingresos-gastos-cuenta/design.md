# Design

## Context

No hay vista de cuenta ni movimientos en el código. `cuentas-del-plan` define la cuenta (`plan_id`, `name`, `balance` decimal) y una página de esa cuenta en `layouts/app` que muestra el nombre; el resto de la vista sigue PENDIENTE y el change no está aplicado. Los formularios reales son tarjetas Livewire (`rounded-2xl`, campos con etiqueta, `@error`, botón `bg-forest`). Motivación: `proposal.md`. Comportamiento: `specs/movimiento/spec.md`.

## Goals / Non-Goals

**Goals:**

- Un formulario de cuatro campos en la vista de la cuenta, fecha en hoy, un envío.
- El ingreso y el nuevo balance se escriben en la misma transacción. El monto es decimal.
- La confirmación se ve en esa misma vista.

**Non-Goals:**

- Tabla o formulario de gasto mientras sus campos sigan PENDIENTE.
- Historial de movimientos, sobres, dinero por asignar y bancos. Dependencias nuevas.

## Decisions

### 1. Tabla `ingresos` y balance en la misma transacción

Columnas: `cuenta_id`, `fecha` (date), `origen` (de quién o cómo entra el dinero), `descripcion`, `monto` decimal. No hay columna de moneda: la cuenta ya pertenece a un plan de una sola moneda.

`monto` y `balance` usan el mismo decimal que `cuentas-del-plan`. No `float`. Al guardar, el balance de la cuenta pasa a ser el anterior más el monto, en esa transacción. Si la validación falla, no hay fila ni cambio de balance.

Si el modelo Cuenta no existe, detenerse. No crear la cuenta en este change.

Solo se inserta si la cuenta es de un plan del usuario autenticado.

No se crea tabla de gastos. Hacerla ahora exigiría inventar columnas.

### 2. Formulario en la vista de la cuenta

El formulario vive en la página de la cuenta de `cuentas-del-plan`, con la misma tarjeta que configuración de cuenta. Cuatro campos y un botón. La fecha es un input con el día de hoy en la zona horaria de la app.

`origen` es un texto. No hay catálogo. Si la pregunta abierta lo convierte en lista, cambia el control y la columna sigue siendo string. No se inventan opciones ahora.

Alternativa: una pantalla aparte de «nuevo ingreso». Se descarta: el pedido es en la vista de la cuenta, con pocos clics.

La confirmación es un aviso en esa vista, con las clases del dashboard (fondo blanco, anillo esmeralda, texto `text-forest`). El texto sigue PENDIENTE; no se redacta aquí. No hay librería de toasts.

El error se muestra bajo el campo. Su texto sigue PENDIENTE.

No se renderiza un formulario de gasto ni un botón que parezca guardarlo.

## Risks / Trade-offs

- [Registrar ingresos antes de que exista la cuenta] → Parar si el modelo no está.
- [Armar el gasto por simetría con el ingreso] → No hay campos definidos. No crear la tabla.
- [Dejar el balance desfasado si falla a medias] → Una transacción para el ingreso y el balance.
- [Tocar la actividad de un sobre] → Este change no escribe sobres. El balance de la cuenta es el único total que cambia.

## Migration Plan

- Una migración para `ingresos`. El balance ya vive en la cuenta; no se copia un historial previo.
- Rollback: revertir esa migración. Los balances sumados por ingresos de prueba se revierten con la base de desarrollo, no hay datos de producción que conservar.

## Open Questions

Los campos del gasto, cómo restaría el balance, el texto de confirmación y de error, si el ingreso se lista, si el origen es una lista y si el monto usa el formato del plan están en `proposal.md`. No se cierran al implementar sin cambiar las tareas.
