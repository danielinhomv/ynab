# Spec Delta

## Purpose

Permite registrar un ingreso en la vista de una cuenta y actualizar su balance. El gasto queda pendiente de definir.

## ADDED Requirements

### Requirement: Formulario corto de ingreso
En la vista de una cuenta propia, el usuario autenticado MUST poder añadir un ingreso en un solo envío. El formulario MUST pedir solo: fecha, de quién o cómo entra el dinero, descripción (por qué se pagó) y monto. La fecha MUST mostrarse en hoy al abrir el formulario, y el usuario MUST poder cambiarla. Un visitante MUST NOT registrar un ingreso. MUST NOT registrarse un ingreso en una cuenta de otro usuario.

#### Scenario: La fecha abre en hoy
- **GIVEN** un usuario autenticado en la vista de una cuenta suya
- **WHEN** abre el formulario de ingreso
- **THEN** la fecha es la de hoy y ve los cuatro campos, sin otros datos de alta

#### Scenario: Un envío guarda el ingreso de hoy
- **GIVEN** un usuario autenticado con el formulario recién abierto
- **WHEN** informa de quién o cómo entra el dinero, la descripción y un monto numérico, y envía sin cambiar la fecha
- **THEN** queda un ingreso de esa cuenta con la fecha de hoy y esos datos

#### Scenario: No registra en una cuenta ajena
- **GIVEN** una cuenta de otro usuario
- **WHEN** el usuario autenticado intenta añadirle un ingreso
- **THEN** no se crea el ingreso y el balance de esa cuenta no cambia

### Requirement: Error sin cambiar el balance
Si falta un campo o el monto no es numérico, el formulario MUST mostrar el error y MUST NOT guardar el ingreso. El balance de la cuenta MUST permanecer igual. El texto del error es PENDIENTE.

#### Scenario: Monto no numérico
- **GIVEN** un usuario autenticado en el formulario de ingreso
- **WHEN** envía un monto que no es numérico
- **THEN** ve un error, no se guarda el ingreso y el balance no cambia

#### Scenario: Falta un campo
- **GIVEN** un usuario autenticado en el formulario de ingreso
- **WHEN** envía el formulario sin uno de los cuatro campos
- **THEN** ve un error, no se guarda el ingreso y el balance no cambia

### Requirement: Confirmación y balance actualizado
Tras un ingreso válido, el usuario MUST ver una confirmación visible. El balance de esa cuenta MUST ser el balance anterior más el monto del ingreso. El monto MUST NOT persistirse como float. El texto de la confirmación es PENDIENTE. Si el monto se muestra con el number format del plan es PENDIENTE.

#### Scenario: El balance suma el monto
- **GIVEN** una cuenta con un balance conocido
- **WHEN** el usuario guarda un ingreso con un monto numérico válido
- **THEN** ve una confirmación y el balance de la cuenta es el anterior más ese monto

### Requirement: Gasto pendiente de definir
La vista de la cuenta MUST poder añadir un gasto cuando sus datos estén definidos. Esos datos son PENDIENTE. Mientras sigan PENDIENTE, MUST NOT guardarse un gasto y el balance MUST NOT cambiar por un gasto. Cómo un gasto modifica el balance es PENDIENTE.

#### Scenario: No se guarda un gasto sin campos definidos
- **GIVEN** los datos de un gasto en PENDIENTE
- **WHEN** el usuario autenticado está en la vista de su cuenta
- **THEN** no queda un gasto guardado y el balance no cambia por un gasto
