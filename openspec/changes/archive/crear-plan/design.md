# Design

## Context

Hoy no hay modelos de plan, categoría ni sobre. El home autenticado (`pages::home` en `layouts/app`) es HTML fijo. Los formularios reales son componentes Livewire de una vista (`pages::register`, `pages::account-settings`): tarjeta `rounded-2xl`, campos con etiqueta, `@error` bajo el campo y botón `bg-forest`. La sesión es la de Laravel; `/configuracion` ya usa `middleware('auth')`. PostgreSQL es la base por defecto. Motivación y alcance: `proposal.md`. Comportamiento: `specs/plan/spec.md`.

## Goals / Non-Goals

**Goals:**

- Un envío autenticado persiste el plan y sus ejemplos en la misma transacción.
- El formulario se ve como los formularios que ya existen, dentro del dashboard, sin ítems nuevos de navegación.
- La moneda del plan es una sola columna, no una lista.

**Non-Goals:**

- Sustituir el presupuesto estático del home, el encabezado «Plan activo» o la moneda del cascarón.
- Tabla de cuentas, selector de plan, edición de moneda, edición de categorías o sobres.
- Dependencias nuevas. Sigue siendo Laravel, Blade, Livewire y PostgreSQL.

## Decisions

### 1. Un componente Livewire en el dashboard

Página Livewire de una vista, `#[Layout('layouts.app')]`, mismo marcado de campos que `pages::account-settings`. El visitante no la monta: la ruta que la sirva lleva `middleware('auth')`, igual que `configuracion`. No se agrega un enlace en el sidebar ni en el menú del avatar.

Alternativa: formulario Blade servido por un controlador. Se descarta: las pantallas con estado de este proyecto ya son Livewire.

La URL concreta sigue PENDIENTE (pregunta abierta del proposal). El componente no depende de esa elección. No registrar la ruta hasta resolverla: elegir `/` cuando no hay plan, u otra ruta, cambia `HomePageController` y las tareas.

### 2. Tres tablas, una moneda, montos en decimal

- `plans`: `user_id`, `name`, `currency` (un string), `fecha` (date), `number_format`, `currency_placement`. El campo de formulario multimoneda escribe `currency`. No hay segunda columna de moneda.
- `categories`: `plan_id`, `name`.
- `sobres`: `category_id`, `name`, `assigned`, `activity`, `available`.

Los tres montos son `decimal` de PostgreSQL (exacto), con cast decimal de Eloquent. No usar `float` ni `double`. Los centavos enteros también cumplirían la regla; se descartan aquí para no convertir en cada lectura cuando este change aún no calcula dinero.

`fecha` se guarda como date. Qué representa (mes de inicio o fecha de alta) sigue PENDIENTE; `created_at` no sustituye esa columna.

No hay `cuenta_id` ni tabla de cuentas. Si el plan puede existir sin cuenta sigue PENDIENTE. No se inventa una cuenta por defecto.

No hay índice único sobre `user_id`. Ponerlo decidiría que solo cabe un plan; no ponerlo, junto con un listado, decidiría que este change administra varios. Ninguna de las dos entra hasta cerrar esa pregunta. Este change no muestra un selector de planes.

### 3. Alta en una transacción

Validar y, si pasa, crear plan, categorías y sobres en una transacción. Si la validación falla, no hay filas nuevas. Los ejemplos salen de una sola lista en código, para poder sustituir el catálogo sin tocar el formulario.

Esa lista, las opciones de los tres selectores y los valores por defecto siguen PENDIENTE. No copiar los nombres ni los montos del home estático (Servicios básicos, Luz, Bs 1.250,00, BOB) como si ya estuvieran confirmados. Si al implementar siguen en PENDIENTE, detenerse y preguntar antes de sembrar o de rellenar los `<select>`.

El nombre vacío muestra «El nombre es obligatorio.» bajo el campo. Un valor fuera de las opciones del selector muestra «El valor no es válido.» Esas dos frases cubren la interfaz en español; el locale de la app sigue en `en`, así que no alcanza el mensaje por defecto de Laravel.

### 4. Confirmación sin librería nueva

El error va bajo el campo, como en configuración de cuenta. El éxito es un aviso visible con las clases que ya usa el dashboard (fondo blanco, anillo esmeralda, texto `text-forest`). El texto y si el aviso queda en el formulario o en la vista del plan siguen PENDIENTE. No añadir un sistema de toasts.

## Risks / Trade-offs

- [El home estático sigue mostrando otro presupuesto] → La confirmación tiene que nombrar el plan creado. No reemplazar el home en este change.
- [Sembrar el catálogo o la moneda de la maqueta] → La lista de ejemplos y las opciones quedan vacías de decisiones hasta las preguntas abiertas. Parar si siguen en PENDIENTE.
- [Un plan sin cuentas choca con la regla de 1 a N cuentas] → No se crea ninguna cuenta. La pregunta queda abierta; no se añade una cuenta implícita para «cumplir» la regla.
- [Un segundo envío crearía otro plan, porque no hay unicidad ni selector] → No limitar ni listar planes en la implementación hasta responder la pregunta. Parar antes de elegir una de las dos.
- [number format y currency placement se confunden con el monto guardado] → Esos campos son metadatos de presentación. Los montos, si se escriben, van en las columnas decimal.

## Migration Plan

- Migraciones nuevas para `plans`, `categories` y `sobres`. No hay datos previos que migrar.
- Rollback: revertir esas migraciones. El home estático no cambia, así que no hay vista que restaurar.

## Open Questions

Las que cambian la ruta, los selectores, el semillero, los montos iniciales, la confirmación o un segundo plan están en `proposal.md` y no se resuelven aquí. No son detalles que se puedan cerrar durante la implementación sin cambiar las tareas.
