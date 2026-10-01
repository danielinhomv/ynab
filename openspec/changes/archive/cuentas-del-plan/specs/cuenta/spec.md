# Spec Delta

## Purpose

Permite agregar cuentas a un plan, como referencia de dónde entra el dinero, sin conectar un banco real.

## ADDED Requirements

### Requirement: Pop-up para elegir el tipo
El botón «Agregar cuenta» MUST abrir un pop-up para elegir el tipo de cuenta. Las únicas opciones MUST ser tarjeta de crédito y cuenta indefinida (efectivo). MUST NOT ofrecer conectar un banco real ni otro tipo. Un visitante MUST NOT abrir ese pop-up.

#### Scenario: El botón muestra los dos tipos
- **GIVEN** un usuario autenticado en el dashboard
- **WHEN** usa «Agregar cuenta»
- **THEN** ve un pop-up con tarjeta de crédito y cuenta indefinida (efectivo), y no ve una opción de conectar un banco

#### Scenario: El visitante no agrega una cuenta
- **GIVEN** un visitante
- **WHEN** intenta abrir el alta de una cuenta
- **THEN** no ve el pop-up y no se crea ninguna cuenta

### Requirement: Tres campos al elegir el tipo
Al elegir un tipo, el mismo flujo MUST pedir solo estos campos: nombre, de dónde entra el dinero y balance actual. El balance MUST ser un dato numérico. MUST guardar la cuenta en el plan con ese tipo y esos tres datos. MUST NOT persistir el balance como float. El tipo elegido MUST ser el único tipo de esa cuenta. Qué control es «de dónde entra el dinero», si el balance admite negativos o decimales, y a qué plan se asocia cuando hay varios, es PENDIENTE.

#### Scenario: Guardar tarjeta de crédito
- **GIVEN** un usuario autenticado que eligió tarjeta de crédito
- **WHEN** envía nombre, de dónde entra el dinero y un balance numérico válido
- **THEN** queda una cuenta de ese tipo en el plan, con esos tres datos y sin conexión a un banco

#### Scenario: Guardar cuenta indefinida
- **GIVEN** un usuario autenticado que eligió cuenta indefinida (efectivo)
- **WHEN** envía nombre, de dónde entra el dinero y un balance numérico válido
- **THEN** queda una cuenta de ese tipo en el plan, con esos tres datos

#### Scenario: Un plan admite más de una cuenta
- **GIVEN** un plan que ya tiene una cuenta
- **WHEN** el usuario crea otra con datos válidos
- **THEN** el plan tiene las dos cuentas

### Requirement: Error en el pop-up y confirmación de éxito
Si falta un campo o el balance no es numérico, el pop-up MUST mostrar el error y MUST NOT crear la cuenta. El texto de ese error es PENDIENTE. Tras un alta válida, MUST mostrarse un pop-up con el texto «cuenta creada exitosamente».

#### Scenario: Balance no numérico no crea la cuenta
- **GIVEN** un usuario autenticado que ya eligió un tipo
- **WHEN** envía un balance que no es numérico
- **THEN** ve un error en el pop-up y no se crea la cuenta

#### Scenario: Falta un campo no crea la cuenta
- **GIVEN** un usuario autenticado que ya eligió un tipo
- **WHEN** envía el formulario sin uno de los tres campos
- **THEN** ve un error en el pop-up y no se crea la cuenta

#### Scenario: El alta válida confirma con el texto pedido
- **GIVEN** un usuario autenticado que envía los tres campos con un balance numérico válido
- **WHEN** la cuenta se crea
- **THEN** ve un pop-up con el texto «cuenta creada exitosamente»

### Requirement: Icono de editar y vista propia
Cada cuenta de la lista MUST mostrar un icono de editar. Un clic en la cuenta, y no en ese icono, MUST abrir la vista propia de esa cuenta. MUST NOT abrir la vista de una cuenta de otro usuario. Qué hace el icono de editar y qué muestra la vista, aparte de ser la de esa cuenta, es PENDIENTE.

#### Scenario: El clic abre la vista de esa cuenta
- **GIVEN** un usuario autenticado que ve una cuenta suya en la lista
- **WHEN** hace clic en la cuenta, fuera del icono de editar
- **THEN** ve la vista propia de esa cuenta

#### Scenario: No abre una cuenta ajena
- **GIVEN** una cuenta de otro usuario
- **WHEN** el usuario autenticado intenta abrirla
- **THEN** no ve la vista de esa cuenta

#### Scenario: La lista muestra el icono de editar
- **GIVEN** un usuario autenticado con una cuenta en la lista
- **WHEN** ve esa cuenta
- **THEN** ve un icono de editar en ella
