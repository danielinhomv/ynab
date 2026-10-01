# Proposal

## Why

Un usuario autenticado entra al dashboard, pero no puede crear su plan: el presupuesto en pantalla es una vista fija, no un plan suyo. Sin ese alta no hay presupuesto mensualizado de una sola moneda sobre el que trabajar.

## What Changes

- El usuario autenticado puede crear un plan de presupuesto mensualizado, de una sola moneda, en un solo envío.
- El formulario tiene exactamente estos campos: nombre, multimoneda (selector de la moneda única del plan), fecha, number format y currency placement.
- Al crear el plan se guardan categorías y sobres de ejemplo (por ejemplo, servicios básicos). Este change no incluye editarlos; quedan persistidos para un change posterior.
- La pantalla reutiliza el dashboard y los formularios ya existentes (misma tipografía, colores y campos). No se añade otra navegación ni otras acciones.
- El formulario abre con valores por defecto en los campos que no son el nombre, para poder crear el plan con el mínimo de clics. Los valores concretos están en Preguntas abiertas.
- Si falta un dato obligatorio o un valor no es válido, se muestra el error en el campo y no se crea el plan. Al terminar con éxito, el usuario ve una confirmación visible de que el plan quedó creado.
- Un visitante no puede crear un plan.

Fuera de este change: editar el plan o su moneda, elegir entre varios planes, cuentas, navegación entre meses, asignar dinero, cubrir sobregiros y editar categorías o sobres.

## Capabilities

### New Capabilities

- `plan`: alta de un plan mensualizado de una sola moneda, con categorías y sobres de ejemplo.

### Modified Capabilities

Ninguna. El cascarón, la autenticación, Livewire y PostgreSQL no cambian de requisito.

## Impact

- Formulario Livewire dentro del dashboard autenticado (`layouts/app`), con el mismo estilo que los formularios actuales.
- Persistencia en PostgreSQL del plan, sus categorías y sus sobres, asociados al usuario autenticado.
- Los montos, si este change llega a guardarlos, no usan float. Si se guardan, es en decimal o en centavos enteros.
- Pruebas del alta, de la moneda única, de los ejemplos generados y de las validaciones.

## Preguntas abiertas

- Dónde vive el formulario: ruta nueva dentro del dashboard, o la vista principal cuando el usuario aún no tiene plan. El home actual muestra un presupuesto de ejemplo estático y no tiene formulario de alta.
- Si, tras crear el plan, el encabezado «Plan activo» y la moneda del cascarón pasan a mostrar ese plan, o siguen como están.
- Qué significa fecha: mes de inicio del presupuesto, o solo la fecha de creación.
- Lista cerrada de monedas del selector multimoneda, y cuál es el valor por defecto. La vista actual muestra Bolivianos (Bs / BOB); no hay un catálogo definido.
- Opciones y valores por defecto de number format y de currency placement. La vista actual muestra un símbolo delante y miles con punto y decimales con coma (`Bs 1.250,00`); no está definido si esas son las únicas opciones.
- Catálogo exacto de categorías y sobres de ejemplo. El usuario citó servicios básicos como ejemplo. La vista estática también muestra Alimentación y Transporte, con sobres y montos de demostración. No está confirmado si ese conjunto es el semillero.
- Valores iniciales de asignado, actividad y disponible en esos sobres. El glosario los nombra; este pedido no dice si nacen en cero o con los montos de la vista estática.
- Reglas de validación además de «dato obligatorio o inválido no crea el plan»: longitud del nombre, nombre duplicado para el mismo usuario, y qué se considera inválido en cada selector.
- Texto y lugar de la confirmación de éxito (mensaje en el formulario, o el plan recién creado en la vista de presupuesto).
- Si un mismo usuario puede crear un segundo plan en este change. La regla fija dice que otra moneda exige otro plan, pero este pedido no incluye un selector de plan activo.
- Un plan, por regla fija, tiene de 1 a N cuentas. Este change no crea cuentas. Queda abierto si el plan puede existir sin cuenta hasta un change posterior.
