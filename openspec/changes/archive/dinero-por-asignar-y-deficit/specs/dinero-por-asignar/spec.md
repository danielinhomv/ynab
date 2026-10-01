# Spec Delta

## Purpose

Muestra arriba en la vista del plan el dinero por asignar del mes visible y descuenta ahí el déficit acumulado, sin modificar los sobres.

## ADDED Requirements

### Requirement: Total arriba, siempre en la vista del plan
La vista del plan MUST mostrar el dinero por asignar del mes visible en la tarjeta de arriba que ya lleva ese nombre. MUST verse sin un clic y sin abrir otra vista. El monto MUST usar el number format y el currency placement del plan y MUST NOT guardarse como float. MUST NOT mostrar el total de un plan de otro usuario. Un visitante MUST NOT ver esta tarjeta. MUST NOT activar las tarjetas «Ingresos del Mes», «Presupuestado» o «Gastado hasta hoy», ni «Auto-asignar», ni «Resolver ahora». Cuál es el total del mes antes de restar el déficit arrastrado es PENDIENTE: MUST NOT inventarse.

#### Scenario: El total se entiende sin cambiar de vista
- **GIVEN** un usuario autenticado en la vista de su plan
- **WHEN** mira la parte de arriba
- **THEN** ve la tarjeta «Dinero por asignar» con el total del mes visible, con el number format y el currency placement del plan, y la vista no cambió

#### Scenario: No ve el total de otro usuario
- **GIVEN** un plan de otro usuario
- **WHEN** el usuario autenticado abre la vista de su plan
- **THEN** el dinero por asignar no es el de ese otro plan

### Requirement: El déficit se descuenta del mes siguiente y se acumula
Si en un mes lo presupuestado supera al ingreso, la diferencia es el déficit de ese mes. El dinero por asignar de cada mes posterior MUST descontar la suma de los déficit de los meses anteriores. Un mes cuyo presupuestado no supera al ingreso MUST NOT sumar un déficit a ese arrastre. El descuento MUST NOT modificar el nombre, el asignado, la actividad ni el disponible de ningún sobre. El monto MUST NOT guardarse como float. De dónde salen el ingreso y lo presupuestado, si un sobrante reduce el déficit acumulado, y si el déficit del mes visible ya entra en el número de ese mismo mes, es PENDIENTE: MUST NOT inventarse.

#### Scenario: El mes siguiente descuenta el déficit
- **GIVEN** un mes cuyo presupuestado supera al ingreso en un déficit, y el mes siguiente con un total propio antes del arrastre
- **WHEN** el usuario ve el mes siguiente
- **THEN** el dinero por asignar es ese total propio menos el déficit, y ningún sobre cambió de nombre, asignado, actividad o disponible

#### Scenario: Los déficit se acumulan
- **GIVEN** dos meses seguidos, cada uno con su déficit, y un tercer mes con un total propio antes del arrastre
- **WHEN** el usuario ve el tercer mes
- **THEN** el dinero por asignar descuenta la suma de esos dos déficit y ningún sobre cambió sus montos

#### Scenario: Un mes sin déficit no se arrastra
- **GIVEN** un mes cuyo presupuestado no supera al ingreso
- **WHEN** el usuario ve el mes siguiente
- **THEN** ese mes no suma un déficit al descuento

### Requirement: El total negativo se distingue
Si el dinero por asignar es negativo, el monto MUST mostrarse en rojo, en la misma tarjeta, distinto del monto que no es negativo. Si no es negativo, el monto MUST conservar el estilo positivo que esa tarjeta ya tiene.

#### Scenario: El negativo se ve en rojo
- **GIVEN** un mes visible cuyo dinero por asignar es negativo
- **WHEN** el usuario mira la tarjeta de arriba
- **THEN** el monto se muestra en rojo dentro de esa misma tarjeta

#### Scenario: El que no es negativo conserva el estilo
- **GIVEN** un mes visible cuyo dinero por asignar no es negativo
- **WHEN** el usuario mira la tarjeta de arriba
- **THEN** el monto no usa el rojo y conserva el estilo positivo de la tarjeta
