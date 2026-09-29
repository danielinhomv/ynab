# Design

## Context

La implementación mezcló el formulario de registro dentro de `layouts/app` (header + aside vacíos). Las vistas de referencia son dos productos visuales distintos: landing de registro e inicio de sesión, y dashboard de presupuesto. Specs: `user-auth` y `app-shell`. Motivación: ver `proposal.md`.

## Goals / Non-Goals

**Goals:**

- Dos layouts Blade: `layouts/guest` (landing) y `layouts/app` (dashboard).
- Fidelity visual a las dos capturas (paleta menta/verde, tipografía, jerarquía), no un formulario gris en el cascarón.
- Auth de sesión Laravel + Livewire, sin starter kit.

**Non-Goals:**

- OAuth Google/Apple funcional.
- Motor real de presupuesto (los montos del dashboard son presentación de la vista, no reglas de negocio persistidas).
- Verificar correo / reset de contraseña.
- Vista funcional de configuración de cuenta de usuario.

## Decisions

### 1. Layouts separados (obligatorio)

`pages::register` y `pages::login` usan `#[Layout('layouts.guest')]`. `pages::home` usa `layouts/app`. El visitante nunca monta el aside del dashboard.

Alternativa: un solo layout con `@guest` en el aside. Se descarta: produce exactamente el error actual (formulario + sidebar).

### 2. Landing = chrome de marketing + slot del formulario

`layouts/guest` trae cabecera, columna izquierda de la captura y pie. El slot es la tarjeta: en `/` y `/login` es inicio de sesión; en `/registro` es crear cuenta. Así ambas comparten la landing y no el dashboard.

### 2b. Orden de las pantallas de visitante

El visitante MUST ver login primero. Solo va a crear cuenta si elige **Crear cuenta**. Tras logout vuelve al login, no al registro.

### 3. Dashboard = chrome de la vista de presupuesto

`layouts/app` trae header (logo, plan, moneda, avatar) y sidebar (navegación, cuentas). El slot es el lienzo del mes (resumen + tabla), fiel a la captura.

El avatar abre un menú nativo (`details`/`summary`) para no depender de Alpine: **Cerrar sesión** cierra la sesión; **Configuración** se muestra y no navega (esa vista no está en este change).

### 4. Marca visual SobrePeso

Las capturas usan SobrePeso; la UI sigue esa marca y paleta. No se usa el cascarón neutro anterior.

## Risks / Trade-offs

- [Montos de la captura en el dashboard] → Son UI de referencia, no se persisten ni se calculan.
- [OAuth en la captura] → No se pintan botones muertos; el enlace a iniciar sesión sí.

## Migration Plan

1. Extraer landing a `layouts/guest` y rediseñar `layouts/app`.
2. Aplicar layouts en los componentes Livewire.
3. Actualizar tests (visitante sin aside de cuentas; autenticado sin landing).
4. Rollback: revertir el change.

## Open Questions

Ninguna que altere specs, enfoque o tareas.
