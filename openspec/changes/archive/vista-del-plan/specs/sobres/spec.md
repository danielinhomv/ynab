# Spec Delta

## Purpose

Muestra las categorías y los sobres del plan, y permite editar un sobre en su fila sin abrir otra pantalla.

## ADDED Requirements

### Requirement: Categorías y sobres del plan
La vista del plan MUST mostrar cada categoría y, debajo, sus sobres. Cada sobre MUST mostrar nombre, asignado, actividad y disponible. MUST NOT mostrar sobres de un plan de otro usuario. Un visitante MUST NOT ver esta vista. MUST NOT ofrecer crear ni eliminar categorías o sobres. Si la fila de categoría muestra la suma de sus sobres, y qué plan se muestra cuando hay varios, es PENDIENTE.

#### Scenario: Los sobres se ven dentro de su categoría
- **GIVEN** un usuario autenticado con un plan que tiene una categoría y un sobre dentro de ella
- **WHEN** abre la vista de ese plan
- **THEN** ve el nombre de la categoría y, en el sobre, el nombre, el asignado, la actividad y el disponible

#### Scenario: No ve sobres ajenos
- **GIVEN** un sobre de un plan de otro usuario
- **WHEN** el usuario autenticado abre la vista de su plan
- **THEN** ese sobre no aparece

#### Scenario: La vista no crea ni elimina
- **GIVEN** un usuario autenticado en la vista del plan
- **WHEN** recorre la tabla
- **THEN** no hay un control para crear o eliminar una categoría o un sobre

### Requirement: Montos con el formato del plan
Asignado, actividad y disponible MUST mostrarse con el number format y el currency placement de ese plan. MUST NOT guardarse como float. Un disponible negativo MUST mostrarse en rojo. MUST NOT ofrecer cubrir ese sobre desde otro. Los patrones concretos de number format y currency placement son PENDIENTE: se usan los que tenga el plan.

#### Scenario: El monto usa el formato del plan
- **GIVEN** un plan con number format y currency placement definidos, y un sobre con asignado
- **WHEN** el usuario ve ese sobre
- **THEN** el asignado, la actividad y el disponible se leen con ese number format y ese currency placement

#### Scenario: Disponible negativo en rojo
- **GIVEN** un sobre cuyo disponible es negativo
- **WHEN** el usuario lo ve en la tabla
- **THEN** el disponible se muestra en rojo y no hay una acción para cubrirlo

### Requirement: Edición en la misma fila
Un clic en un sobre MUST permitir editar sus campos en esa fila, sin cambiar de pantalla. Al guardar, el sobre MUST conservar los valores editados. Un valor de monto que no es numérico MUST mostrar el error y MUST NOT guardarse. Si actividad o disponible se calculan al editar, y si el asignado cambia el dinero por asignar, es PENDIENTE. MUST NOT guardar esos montos como float.

#### Scenario: El clic no abre otra pantalla
- **GIVEN** un usuario autenticado que ve un sobre suyo
- **WHEN** hace clic en ese sobre
- **THEN** puede editar sus campos en la misma fila y la pantalla no cambia

#### Scenario: El asignado editado se guarda
- **GIVEN** un usuario autenticado editando un sobre en su fila
- **WHEN** guarda un asignado numérico distinto
- **THEN** el sobre queda con ese asignado

#### Scenario: Un monto no numérico no se guarda
- **GIVEN** un usuario autenticado editando un sobre en su fila
- **WHEN** intenta guardar un monto que no es numérico
- **THEN** ve el error y el monto anterior no cambia

#### Scenario: No edita un sobre ajeno
- **GIVEN** un sobre de un plan de otro usuario
- **WHEN** el usuario autenticado intenta editarlo
- **THEN** ese sobre no cambia
