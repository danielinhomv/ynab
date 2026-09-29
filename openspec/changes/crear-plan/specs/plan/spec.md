# Spec Delta

## Purpose

Permite al usuario autenticado crear un plan de presupuesto mensualizado de una sola moneda, y guardar con él categorías y sobres de ejemplo.

## ADDED Requirements

### Requirement: Formulario de crear plan
El usuario autenticado MUST poder abrir un formulario de crear plan dentro del dashboard ya existente, en español, con el mismo tratamiento visual de los formularios actuales: etiqueta por campo y error junto al campo. El formulario MUST mostrar solo estos campos: nombre, multimoneda, fecha, number format y currency placement. Multimoneda MUST ser un selector de una única moneda. La ruta concreta del formulario es PENDIENTE.

#### Scenario: El autenticado ve los cinco campos
- **GIVEN** un usuario autenticado
- **WHEN** abre el formulario de crear plan
- **THEN** ve nombre, multimoneda, fecha, number format y currency placement, y no ve otros campos de alta

#### Scenario: El visitante no crea un plan
- **GIVEN** un visitante
- **WHEN** intenta abrir o enviar el formulario de crear plan
- **THEN** no ve el formulario y no se crea ningún plan

### Requirement: Plan de una sola moneda
Un envío válido MUST crear un plan de presupuesto mensualizado perteneciente a ese usuario, con exactamente una moneda: la elegida en multimoneda. MUST guardar también el nombre, la fecha, el number format y el currency placement enviados. MUST NOT guardar una segunda moneda en ese plan. El significado de la fecha es PENDIENTE. Las opciones permitidas de multimoneda, number format y currency placement son PENDIENTE.

#### Scenario: El plan guarda una sola moneda
- **GIVEN** un usuario autenticado en el formulario, con una moneda elegida en multimoneda
- **WHEN** envía el formulario con datos válidos
- **THEN** queda un plan suyo con esa moneda, el nombre, la fecha, el number format y el currency placement enviados, y sin otra moneda

#### Scenario: Otro usuario no recibe ese plan
- **GIVEN** un plan recién creado por un usuario
- **WHEN** otro usuario consulta sus planes
- **THEN** no ve el plan ajeno

### Requirement: Alta en un solo envío con valores por defecto
Al abrir el formulario, multimoneda, fecha, number format y currency placement MUST mostrar un valor por defecto. Esos valores concretos son PENDIENTE. Con el nombre informado y sin cambiar el resto, un solo envío MUST bastar para crear el plan. MUST NOT pedir pasos adicionales ni otras pantallas de alta.

#### Scenario: Crear el plan sin cambiar los valores por defecto
- **GIVEN** un usuario autenticado ante el formulario recién abierto
- **WHEN** informa el nombre y envía el formulario sin modificar los otros campos
- **THEN** se crea el plan con la moneda, la fecha, el number format y el currency placement que el formulario mostraba por defecto

### Requirement: Categorías y sobres de ejemplo
Al crear el plan, el mismo envío MUST guardar categorías de ejemplo y, dentro de ellas, sobres de ejemplo, asociados a ese plan. MUST haber al menos una categoría y al menos un sobre. El catálogo exacto es PENDIENTE; el usuario citó servicios básicos como ejemplo de ese contenido. Los valores iniciales de asignado, actividad y disponible son PENDIENTE. Si se guardan montos, MUST NOT persistirse como float: MUST usarse decimal o enteros en centavos. Este change MUST NOT ofrecer editar esas categorías ni esos sobres.

#### Scenario: El alta deja ejemplos en el plan
- **GIVEN** un usuario autenticado que envía un formulario válido
- **WHEN** el plan se crea
- **THEN** ese plan tiene al menos una categoría de ejemplo y al menos un sobre de ejemplo asociado a ella

#### Scenario: Un alta inválida no deja ejemplos
- **GIVEN** un usuario autenticado en el formulario
- **WHEN** envía datos que no pasan la validación
- **THEN** no se crea el plan y no quedan categorías ni sobres nuevos

### Requirement: Validación clara y confirmación visible
Si falta un dato obligatorio o un valor no es válido, el formulario MUST mostrar el error en el campo correspondiente y MUST NOT crear el plan. El nombre vacío es un dato obligatorio inválido. Qué más es inválido (longitud del nombre, nombre duplicado, valor fuera de las opciones) es PENDIENTE. Tras un alta válida, el usuario MUST ver una confirmación visible de que el plan quedó creado. El texto y el lugar de esa confirmación son PENDIENTE.

#### Scenario: Nombre vacío no crea el plan
- **GIVEN** un usuario autenticado en el formulario
- **WHEN** envía el formulario sin nombre
- **THEN** ve un error en el campo nombre y no se crea el plan

#### Scenario: Valor no permitido no crea el plan
- **GIVEN** un usuario autenticado en el formulario
- **WHEN** envía un valor que no pertenece a las opciones permitidas de un selector
- **THEN** ve un error en ese campo y no se crea el plan

#### Scenario: El alta válida se confirma
- **GIVEN** un usuario autenticado que envía datos válidos
- **WHEN** el plan se crea
- **THEN** ve una confirmación visible de que el plan quedó creado
