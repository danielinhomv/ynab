# Proposal

## Why

El usuario tiene que ver, en la vista del plan y sin abrir otra pantalla, cuánto dinero le queda por asignar. Si en un mes presupuestó más que el ingreso, ese déficit se arrastra y se descuenta del total del mes siguiente, acumulándose, sin tocar los sobres. Hoy la tarjeta «Dinero por asignar» muestra un monto fijo.

## What Changes

- Arriba en la vista del plan, en la tarjeta que ya dice «Dinero por asignar», se muestra el total del mes visible. El número usa el number format y el currency placement del plan. Está siempre en esa vista: no hace falta un clic ni otra pantalla.
- Si en un mes lo presupuestado supera al ingreso, la diferencia es el déficit de ese mes. Ese déficit se descuenta del dinero por asignar del mes siguiente. Los déficit de meses anteriores se suman (se acumulan) al descontar los meses posteriores.
- El cálculo solo cambia el total de arriba. No modifica un sobre ni su nombre, asignado, actividad o disponible.
- Si el total es negativo, el monto se distingue del positivo con el rojo que la misma vista ya usa para montos negativos. El positivo conserva el estilo actual de la tarjeta.
- No se activan las tarjetas «Ingresos del Mes», «Presupuestado» ni «Gastado hasta hoy», ni «Auto-asignar», ni el aviso «Resolver ahora».

## Capabilities

### New Capabilities

- `dinero-por-asignar`: mostrar el total de arriba del mes visible y descontar ahí, acumulado, el déficit de haber presupuestado más que el ingreso.

### Modified Capabilities

- Ninguna. No está en las specs principales. `vista-del-plan` deja PENDIENTE si editar el asignado cambia este total y no lo calcula. `ingresos-gastos-cuenta` actualiza el balance de la cuenta y no este total. `cubrir-sobregasto` no modifica este total al mover dinero entre sobres. No se editan esos changes.

## Impact

- Tarjeta superior de `resources/views/pages/⚡home.blade.php`: el monto fijo «Bs 1.250,00» pasa a ser el total del mes visible. La tarjeta sigue en la misma grilla (`rounded-2xl`, anillo esmeralda, etiqueta «Dinero por asignar»).
- Lectura del mes visible. El monto se guarda o se calcula en decimal, no en float. Sin escribir sobres.
- Sin paquetes nuevos y sin rutas nuevas.

## Preguntas abiertas

- PENDIENTE: de dónde sale el ingreso del mes. `ingresos-gastos-cuenta` registra ingresos en la cuenta y no actualiza este total.
- PENDIENTE: de dónde sale lo presupuestado. No está dicho si es la suma del asignado de los sobres de ese mes.
- PENDIENTE: cuál es el dinero por asignar del mes antes de restarle el déficit arrastrado.
- PENDIENTE: si un mes con ingreso mayor a lo presupuestado reduce el déficit acumulado, o si solo se acumulan los déficit y el sobrante no se arrastra.
- PENDIENTE: si el déficit del mes que se está viendo ya entra en el número de ese mismo mes, además de descontarse del mes siguiente.
- Los patrones concretos de number format y currency placement siguen PENDIENTE en `crear-plan`. Se usan los del plan. No se copia «Bs 1.250,00» como formato.
