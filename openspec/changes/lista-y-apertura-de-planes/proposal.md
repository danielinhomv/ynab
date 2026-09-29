# Proposal

## Why

El dashboard no tiene planes del usuario: el encabezado «Plan activo» es un texto fijo. Sin una lista no se puede encontrar un plan, abrirlo ni empezar otro cuando hace falta otra moneda.

## What Changes

- El usuario autenticado ve la lista de sus planes. Cada ítem muestra el nombre y la única moneda de ese plan, para distinguirlo sin abrirlos todos.
- Un clic en un plan lo abre. No hay un segundo paso ni una confirmación.
- Hay un botón para crear un plan nuevo, visible también cuando ya hay planes, para empezar otro (y así otra moneda) sin buscar. El botón no agrega una moneda al plan abierto.
- Si el usuario no tiene planes, ve un estado vacío claro, con ese botón, y no una lista.
- Un visitante no ve la lista ni abre planes ajenos.
- La pantalla usa el dashboard ya existente (barra, sidebar, colores y botones). No se añaden otras secciones de navegación.

Este change no incluye el formulario de alta (campos, validación y categorías de ejemplo): eso es `crear-plan`. El botón solo lleva a ese alta. Tampoco incluye editar el plan, cambiarle la moneda, borrarlo, cuentas, meses, asignar dinero ni cubrir sobregiros.

## Capabilities

### New Capabilities

- `plan`: lista de planes del usuario, apertura en un clic y botón para crear otro plan de una sola moneda. En las specs principales `plan` todavía no existe. El change `crear-plan` usa el mismo path para el alta y deja fuera elegir entre planes; este delta no repite el formulario.

### Modified Capabilities

Ninguna. `app-shell`, `user-auth`, `livewire-frontend` y `postgresql` no cambian de requisito.

## Impact

- Lista y botón en el dashboard autenticado (`layouts/app`), con el estilo de las tarjetas y botones actuales. El home hoy no tiene esa lista; el encabezado «Plan activo» y «Moneda: Bs (BOB)» siguen siendo texto fijo.
- Lectura de los planes del usuario autenticado. No hace falta otra tabla si `crear-plan` ya guarda el plan con su moneda.
- Pruebas de lista, de un clic para abrir, del botón de crear, del estado vacío y de que no se ven planes ajenos.

## Preguntas abiertas

- Dónde se colocan la lista y el botón. El dashboard no tiene una lista de planes: el sidebar es navegación y cuentas, y el encabezado muestra «Plan activo» fijo.
- Qué muestra el área de presupuesto al abrir un plan. El home es un presupuesto estático. Este change no define categorías, sobres ni meses.
- Si, al abrir, el encabezado «Plan activo» y la moneda del cascarón pasan a mostrar ese plan. `crear-plan` dejó la misma pregunta abierta.
- A dónde lleva el botón de crear. El formulario y su ruta están en `crear-plan` y la ruta sigue PENDIENTE. Este change no define los campos.
- Orden de la lista, y cómo distinguir dos planes con el mismo nombre y la misma moneda.
- Si el plan abierto se recuerda al volver a entrar, o solo dura esta visita.
