# Design

## Context

La vista `pages::home` ya tiene, arriba, la tarjeta «Listo para asignar» con el monto fijo «Bs 1.250,00» y la etiqueta «Dinero por asignar». Al lado hay tres tarjetas estáticas (ingresos, presupuestado, gastado) que este change no activa. El monto negativo de la tabla usa `text-red-600`; esta tarjeta todavía no. No hay un total por mes en la base. `navegacion-entre-meses` propone el mes visible y no está aplicado. Motivación: `proposal.md`. Comportamiento: `specs/dinero-por-asignar/spec.md`.

## Goals / Non-Goals

**Goals:**

- Sustituir solo el monto de esa tarjeta por el dinero por asignar del mes visible, en el mismo componente.
- Restar ahí la suma de los déficit de los meses anteriores, en decimal, sin escribir sobres.

**Non-Goals:**

- Las otras tres tarjetas, auto-asignar, el aviso de sobregiro, flechas de mes, cubrir sobres y la fórmula del disponible. Dependencias nuevas.

## Decisions

### 1. La misma tarjeta, en el componente de la vista

Sigue siendo el componente Livewire de `pages::home`, con `layouts/app` y el middleware `auth`. El monto se pinta al renderizar la vista: no hay acción ni ruta para abrirlo. Se conservan la grilla, el anillo esmeralda, el icono en `bg-mint` y la etiqueta «Dinero por asignar».

Si el total es negativo, solo el monto usa `text-red-600`, el rojo que la tabla ya usa para un monto negativo. Si no lo es, el monto sigue en `text-slate-900`. No se pinta la tarjeta como el aviso rojo de sobregiro.

El número usa el number format y el currency placement del plan. Si esos patrones siguen PENDIENTE en `crear-plan`, detenerse y no dejar «Bs 1.250,00» como valor real.

Si el usuario no tiene un solo plan y el plan abierto sigue PENDIENTE, detenerse. No añadir un selector. El total se limita al plan cuyo `user_id` es el usuario autenticado.

Alternativa: una pantalla aparte para el total. Se descarta: tiene que verse siempre, sin otro clic.

### 2. Calcular el arrastre, sin guardar nada en el sobre

El déficit de un mes es lo presupuestado menos el ingreso, solo cuando lo presupuestado es mayor. El dinero por asignar de un mes es su total propio menos la suma de esos déficit anteriores. La resta es en decimal (no float, y no se cambia a centavos enteros).

Ese resultado no se escribe en el sobre. No cambian nombre, asignado, actividad ni disponible. No se copia el disponible al mes siguiente.

No se crea tabla ni columna para este total mientras sigan PENDIENTE el origen del ingreso, el origen de lo presupuestado y el total propio. Sin esa base, una columna guardaría una fórmula que el pedido no cerró. Cuando exista, el número se calcula al leer el mes visible; no se arrastra escribiendo sobres.

Alternativa: guardar el déficit acumulado en el disponible del sobre. Se descarta: el pedido dice que el déficit nunca modifica los sobres.

## Risks / Trade-offs

- [Dejar «Bs 1.250,00» y solo cambiar la etiqueta] → El monto tiene que ser el del mes visible. El fijo no es un total.
- [Pintar de rojo toda la tarjeta] → Solo el monto pasa a `text-red-600`. El aviso de sobregiro es otra pieza.
- [Restar el arrastre con float] → La suma de déficit y la resta al total propio son decimal.
- [Inventar el ingreso como la suma de ingresos de la cuenta] → `ingresos-gastos-cuenta` no actualiza este total. No se usa esa suma hasta cerrar la pregunta.
- [Tomar lo presupuestado como la suma del asignado] → Tampoco está cerrado. `vista-del-plan` y `cubrir-sobregasto` no escriben este total; este change no reescribe esos deltas.

## Migration Plan

- Sin migración. No hay columna nueva ni datos de ejemplo.
- Rollback: volver el monto de la tarjeta al texto fijo. No hay sobres que revertir, porque este change no los escribe.

## Open Questions

Están en `proposal.md`. Cambian el total propio, el origen del ingreso, el origen de lo presupuestado, el sobrante y si el déficit del mes visible entra en su propio número. No se cierran al implementar.
