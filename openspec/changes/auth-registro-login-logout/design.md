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

## Decisions

### 1. Layouts separados (obligatorio)

`pages::register` y `pages::login` usan `#[Layout('layouts.guest')]`. `pages::home` y configuración usan `layouts/app`. El visitante nunca monta el aside del dashboard.

Alternativa: un solo layout con `@guest` en el aside. Se descarta: produce exactamente el error actual (formulario + sidebar).

### 2. Landing = chrome de marketing + slot del formulario

`layouts/guest` trae cabecera, columna izquierda de la captura y pie. El slot es solo la tarjeta (registro o login). Así login y registro comparten la misma escena y no el dashboard.

### 3. Dashboard = chrome de la vista de presupuesto

`layouts/app` trae header (logo, plan, moneda, menú de usuario con configuración y logout) y sidebar (navegación, cuentas). El slot es el lienzo del mes (resumen + tabla), fiel a la captura.

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
