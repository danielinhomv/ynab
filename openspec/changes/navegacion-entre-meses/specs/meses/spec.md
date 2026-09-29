# Spec Delta

## Purpose

Permite moverse de un mes a otro en la vista del plan y ver las categorías y los sobres de ese mes.

## ADDED Requirements

### Requirement: Flechas de un mes y título visible
Arriba en la vista del plan MUST haber una flecha al mes anterior y una al mes siguiente, junto al título del mes. Un clic en una flecha MUST cambiar exactamente un mes. El título MUST mostrar el mes seleccionado con su nombre en español y el año, y MUST seguir visible después del cambio. Un visitante MUST NOT cambiar el mes. Qué mes aparece al entrar, y si el botón «Hoy» selecciona el mes actual, es PENDIENTE.

#### Scenario: Un clic va al mes siguiente
- **GIVEN** un usuario autenticado que ve un mes de su plan en el título
- **WHEN** hace clic en la flecha del mes siguiente
- **THEN** el título muestra el mes inmediato siguiente, con el nombre en español y el año

#### Scenario: Un clic va al mes anterior
- **GIVEN** un usuario autenticado que ve un mes de su plan en el título
- **WHEN** hace clic en la flecha del mes anterior
- **THEN** el título muestra el mes inmediato anterior, con el nombre en español y el año

#### Scenario: El visitante no cambia el mes
- **GIVEN** un visitante
- **WHEN** intenta usar las flechas de mes
- **THEN** no cambia el mes de ningún plan

### Requirement: La tabla es la del mes seleccionado
Al cambiar de mes, la vista de categorías y sobres MUST mostrar el asignado, la actividad y el disponible de ese mes. MUST NOT mostrar los montos de otro mes como si fueran los del mes seleccionado. Si un mes no tiene montos guardados, qué se muestra es PENDIENTE. Si la lista de categorías y sobres cambia con el mes, o solo sus montos, es PENDIENTE.

#### Scenario: Cada mes muestra sus montos
- **GIVEN** un sobre con un asignado en un mes y otro asignado en el mes siguiente
- **WHEN** el usuario selecciona cada uno de esos meses
- **THEN** ve el asignado, la actividad y el disponible que corresponden a ese mes

### Requirement: Cambio sin recargar toda la página
El cambio de mes MUST actualizar el título y la tabla sin recargar todo el dashboard. El cascarón de la vista MUST seguir presente. El disponible de un sobre MUST NOT copiarse al mes siguiente por este cambio. Si ese traspaso ocurre es PENDIENTE.

#### Scenario: El cascarón sigue al cambiar de mes
- **GIVEN** un usuario autenticado en la vista del plan
- **WHEN** cambia de mes con una flecha
- **THEN** el título y la tabla corresponden al mes nuevo y el dashboard no se reemplaza por otra página

#### Scenario: Navegar no traspasa el disponible
- **GIVEN** un sobre con disponible en un mes y sin traspaso definido hacia el mes siguiente
- **WHEN** el usuario abre el mes siguiente
- **THEN** el disponible de ese sobre en el mes anterior no se copia al mes siguiente
