# Spec Delta

## Purpose

Permite al usuario autenticado ver sus planes, abrir uno en un clic y empezar otro plan cuando necesita otra moneda.

## ADDED Requirements

### Requirement: Lista de planes del usuario
El usuario autenticado MUST ver solo sus planes. Cada ítem MUST mostrar el nombre del plan y su única moneda, y MUST NOT mostrar una segunda moneda. MUST NOT mostrar planes de otro usuario. El lugar de la lista dentro del dashboard y el orden de los ítems son PENDIENTE. Un visitante MUST NOT ver la lista.

#### Scenario: Ve sus planes con nombre y moneda
- **GIVEN** un usuario autenticado con dos planes, cada uno con una moneda distinta
- **WHEN** ve la lista de planes
- **THEN** ve el nombre y la moneda de cada uno, y no ve una segunda moneda en ningún ítem

#### Scenario: No ve planes ajenos
- **GIVEN** un plan de otro usuario
- **WHEN** el usuario autenticado ve la lista
- **THEN** ese plan no aparece

#### Scenario: El visitante no ve la lista
- **GIVEN** un visitante
- **WHEN** intenta ver la lista de planes
- **THEN** no ve planes de ningún usuario

### Requirement: Abrir un plan en un clic
Un clic en un plan de la lista MUST abrirlo, sin un segundo paso ni una confirmación. Tras abrirlo, MUST quedar claro cuál es el plan abierto. MUST NOT abrir un plan de otro usuario. Qué muestra el área de presupuesto, si el encabezado «Plan activo» y la moneda del cascarón cambian, y si el plan abierto se recuerda en otra visita, es PENDIENTE.

#### Scenario: Un clic abre ese plan
- **GIVEN** un usuario autenticado que ve al menos un plan suyo en la lista
- **WHEN** hace clic en ese plan
- **THEN** ese plan queda abierto y se distingue como el plan abierto, sin un paso intermedio

#### Scenario: No abre un plan ajeno
- **GIVEN** un plan de otro usuario
- **WHEN** el usuario autenticado intenta abrirlo
- **THEN** ese plan no queda abierto

### Requirement: Botón para crear otro plan
El usuario autenticado MUST ver un botón para crear un plan nuevo tanto si ya tiene planes como si no tiene ninguno. Un clic MUST iniciar el alta de otro plan y MUST NOT agregar una moneda al plan abierto. El destino de ese botón es el formulario de `crear-plan`; la ruta de ese formulario es PENDIENTE. Este change MUST NOT pedir los campos del alta.

#### Scenario: El botón sigue visible con planes existentes
- **GIVEN** un usuario autenticado que ya tiene al menos un plan
- **WHEN** ve la lista
- **THEN** ve el botón para crear un plan nuevo

#### Scenario: El botón no cambia la moneda del plan abierto
- **GIVEN** un usuario autenticado con un plan abierto
- **WHEN** usa el botón de crear un plan nuevo
- **THEN** inicia el alta de otro plan y la moneda del plan que ya estaba abierto no cambia

### Requirement: Estado vacío sin planes
Si el usuario autenticado no tiene planes, MUST ver un estado vacío que comunica que no hay planes, junto con el botón de crear. MUST NOT ver una lista de planes. Si el presupuesto estático del home permanece en esa situación es PENDIENTE.

#### Scenario: Sin planes se ve el estado vacío
- **GIVEN** un usuario autenticado sin planes
- **WHEN** entra al dashboard
- **THEN** ve un estado vacío que indica que no hay planes, ve el botón de crear y no ve ítems de plan
