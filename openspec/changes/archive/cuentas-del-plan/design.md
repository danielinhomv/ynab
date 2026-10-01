# Design

## Context

En `layouts/app`, la sección «Cuentas» es HTML fijo: Efectivo, Banco Sol, Banco Ganadero, el botón «Agregar cuenta» y «Total en Cuentas». El botón no abre nada. No hay modelo de cuenta ni un modal en el proyecto. El plan lo define `crear-plan` y aún no está aplicado. Motivación: `proposal.md`. Comportamiento: `specs/cuenta/spec.md`.

## Goals / Non-Goals

**Goals:**

- Un pop-up de dos pasos: primero el tipo, después los tres campos, y al guardar otro pop-up con «cuenta creada exitosamente».
- La cuenta cuelga de un plan del usuario autenticado. El balance es decimal, no float.
- El clic de la fila abre la vista; el icono de editar no dispara ese clic.

**Non-Goals:**

- Cliente HTTP a un banco, tokens o números de cuenta externos.
- Sembrar Efectivo, Banco Sol y Banco Ganadero.
- Formulario de edición, suma de «Total en Cuentas» y el alta de la cuenta dentro del formulario de `crear-plan`. Dependencias nuevas.

## Decisions

### 1. Tabla `cuentas` ligada al plan

Columnas: `plan_id`, `type` (`tarjeta_credito` o `indefinida`), `name`, `money_source` (el campo «de dónde entra el dinero») y `balance` `decimal`. No hay segunda tabla ni credenciales de banco.

El decimal es el mismo criterio que el de los montos de `crear-plan`: PostgreSQL `numeric` y cast decimal de Eloquent. Los centavos enteros también cumplirían; se descartan para no mezclar dos representaciones de dinero en el proyecto.

Si el modelo Plan no existe, detenerse. No crear el plan en este change.

Si el usuario tiene un solo plan, la cuenta se asocia a ese. Si no tiene ninguno, o tiene varios y el plan abierto sigue PENDIENTE, detenerse y preguntar. No inventar un selector de plan.

### 2. Un pop-up Livewire, dos pasos

El botón que ya está en el sidebar abre un componente Livewire. El primer paso son dos acciones: tarjeta de crédito y cuenta indefinida (efectivo). El segundo paso son los tres campos, en la misma tarjeta (`rounded-2xl`, botón `bg-forest`), sin cerrar y volver a abrir.

Alternativa: dos pop-ups distintos para el tipo y los campos. Se descarta: suma un cierre de más.

Al guardar, ese pop-up se reemplaza por otro con el texto exacto «cuenta creada exitosamente». El error de validación queda dentro del pop-up del formulario. El texto del error sigue PENDIENTE; no se redacta aquí.

`money_source` se guarda como string. Si el control es un texto o un `<select>` sigue PENDIENTE. No se elige la lista de opciones.

El icono de la fila reutiliza los SVG que ya están: el de moneda para indefinida y el de tarjeta para tarjeta de crédito. No se añade una librería de iconos.

### 3. Fila y vista, icono aparte

La fila es un enlace a una página Livewire con `layouts/app`, de esa cuenta y solo si el plan es del usuario autenticado. La vista muestra el nombre de la cuenta para reconocerla. El resto del contenido sigue PENDIENTE; no se arma un estado de cuenta.

El icono de editar es un botón dentro de la fila que no navega. Su acción sigue PENDIENTE. No se construye el formulario de edición.

No se toca el monto de «Total en Cuentas» ni se decide si las tres filas fijas se quitan, hasta cerrar esas preguntas.

## Risks / Trade-offs

- [Crear cuentas antes de que exista Plan] → Parar si el modelo no está. No migrar un plan paralelo.
- [Varios planes y ninguno marcado como abierto] → No adivinar el `plan_id`. Preguntar.
- [Dejar las tres cuentas fijas junto a las reales] → La lista puede mentir. No borrarlas ni sumarlas al total hasta la respuesta.
- [El icono de editar parece que edita] → Está visible y no navega. No implementar la edición en este change.

## Migration Plan

- Una migración nueva para `cuentas`. No hay cuentas previas que copiar.
- Rollback: revertir esa migración y volver el bloque Cuentas al HTML fijo.

## Open Questions

El control de «de dónde entra el dinero», la acción del icono, el contenido de la vista más allá del nombre, el plan cuando hay varios, el mínimo de una cuenta, negativos y decimales, el texto de error, el total del sidebar y las filas fijas están en `proposal.md`. No se cierran al implementar sin cambiar las tareas.
