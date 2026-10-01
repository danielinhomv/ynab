# Spec Delta

## Purpose

Permite crear una categoría y un sobre en el plan que ya se muestra, sin salir de esa pantalla.

## ADDED Requirements

### Requirement: Crear una categoría por nombre
El botón «+ Nueva categoría» MUST pedir solo el nombre y MUST crear la categoría en el plan que ya se muestra, el más reciente del usuario. Si el nombre está vacío, MUST mostrar «El nombre es obligatorio.» y MUST NOT crear la categoría. Al guardar un nombre, la fila MUST aparecer en la tabla de esa misma pantalla. MUST NOT abrir otra ruta. Un visitante MUST NOT crear una categoría. MUST NOT crear la categoría en un plan de otro usuario. El control que pide el nombre, si el nombre puede repetirse, el lugar de la fila y qué ocurre si no hay ningún plan son PENDIENTE.

#### Scenario: Un nombre crea la categoría en la misma pantalla
- **GIVEN** un usuario autenticado que ve su plan más reciente
- **WHEN** usa «+ Nueva categoría» y envía un nombre
- **THEN** esa categoría queda en ese plan y su fila se ve en la tabla, sin cambiar de pantalla

#### Scenario: El nombre vacío no crea la categoría
- **GIVEN** un usuario autenticado en el alta de una categoría
- **WHEN** envía el nombre vacío
- **THEN** ve «El nombre es obligatorio.» y no se crea la categoría

#### Scenario: El visitante no crea una categoría
- **GIVEN** un visitante
- **WHEN** intenta crear una categoría
- **THEN** no se crea ninguna categoría

### Requirement: Crear un sobre en una categoría del plan
El botón «+ Nuevo sobre» MUST pedir el nombre y la categoría de ese plan. Si el plan tiene una sola categoría, esa MUST quedar elegida. Asignado, actividad y disponible MUST empezar en 0.00 y MUST NOT guardarse como float. Si el nombre está vacío, MUST mostrar «El nombre es obligatorio.» y MUST NOT crear el sobre. Al guardar, la fila del sobre MUST aparecer dentro de esa categoría en la misma pantalla. MUST NOT crear el sobre en una categoría de otro usuario. Si el plan no tiene categorías, cuál categoría queda marcada cuando hay varias, si el nombre puede repetirse y el lugar de la fila son PENDIENTE.

#### Scenario: Una sola categoría queda elegida
- **GIVEN** un usuario autenticado cuyo plan mostrado tiene una sola categoría
- **WHEN** usa «+ Nuevo sobre»
- **THEN** esa categoría ya está elegida

#### Scenario: El sobre nace con los tres montos en cero
- **GIVEN** un usuario autenticado que eligió una categoría de su plan
- **WHEN** envía un nombre
- **THEN** el sobre queda en esa categoría con asignado, actividad y disponible en 0.00, y esos montos no son float

#### Scenario: El nombre vacío no crea el sobre
- **GIVEN** un usuario autenticado en el alta de un sobre
- **WHEN** envía el nombre vacío
- **THEN** ve «El nombre es obligatorio.» y no se crea el sobre

#### Scenario: No crea un sobre en un plan ajeno
- **GIVEN** una categoría de un plan de otro usuario
- **WHEN** el usuario autenticado intenta crear un sobre ahí
- **THEN** ese sobre no se crea
