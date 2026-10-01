# Spec Delta

## Purpose

Muestra en rojo el sobre que gastó más de lo asignado y permite cubrirlo moviendo el faltante desde otro sobre.

## ADDED Requirements

### Requirement: Sobre en rojo cuando el gasto supera lo asignado
En la vista del plan, un sobre cuya actividad es mayor que su asignado MUST mostrarse en rojo, con el tratamiento de la fila de sobregiro que ya está en esa vista, incluido el distintivo «Sobregiro». El monto MUST verse con el number format y el currency placement del plan. MUST NOT guardarse como float. Un sobre cuya actividad no supera a su asignado MUST NOT mostrarse con ese tratamiento. El delta en curso de `vista-del-plan` dice que un disponible negativo se ve en rojo y que no hay acción para cubrirlo; este requisito agrega el rojo por gasto mayor al asignado y la acción de cubrir. No se reescribe ese otro delta.

#### Scenario: El sobre en problema se distingue al instante
- **GIVEN** un usuario autenticado que ve un sobre suyo cuya actividad es mayor que su asignado
- **WHEN** mira la tabla del mes visible
- **THEN** esa fila se muestra en rojo, con el distintivo «Sobregiro», y el monto se lee con el number format y el currency placement del plan

#### Scenario: Un sobre cubierto no se pinta en rojo
- **GIVEN** un sobre cuya actividad no supera a su asignado
- **WHEN** el usuario lo ve en la tabla
- **THEN** la fila no usa el tratamiento rojo de sobregiro

### Requirement: Clic para cubrir desde otro sobre
Un clic en el sobre en rojo MUST abrir, en la misma vista, la tarjeta «Cubrir sobregiro» que ya está en la interfaz. La tarjeta MUST nombrar ese sobre. MUST ofrecer elegir otro sobre del mismo plan y confirmar el movimiento del monto sugerido. MUST NOT abrir otra pantalla. MUST NOT ofrecer cubrir desde el dinero por asignar. MUST NOT usar el aviso «Resolver ahora». El sobre de origen MUST NOT ser el mismo sobre en problema. Un sobre de otro usuario MUST NOT aparecer como origen. Si el clic también abre la edición en fila de `vista-del-plan`, es PENDIENTE.

#### Scenario: El clic nombra el sobre y pide otro
- **GIVEN** un usuario autenticado que ve un sobre suyo en rojo
- **WHEN** hace clic en ese sobre
- **THEN** ve la tarjeta «Cubrir sobregiro» en la misma vista, con el nombre de ese sobre, y puede elegir otro sobre del mismo plan

#### Scenario: No se cubre desde el mismo sobre ni desde dinero por asignar
- **GIVEN** la tarjeta abierta para un sobre en rojo
- **WHEN** el usuario mira de dónde puede tomar el dinero
- **THEN** no puede elegir ese mismo sobre ni el dinero por asignar

### Requirement: Monto faltante sugerido y movimiento en pocos pasos
La tarjeta MUST sugerir el faltante, que es la actividad menos el asignado del sobre en problema. Confirmar MUST sumar ese monto al asignado de ese sobre y restarlo del asignado del sobre elegido. La actividad de ambos MUST permanecer igual. El movimiento MUST guardarse como decimal, no como float, y MUST afectar solo el mes visible. Los demás meses MUST NOT cambiar. Si el monto sugerido se puede editar antes de confirmar, es PENDIENTE: hasta resolverlo, la confirmación mueve el faltante sugerido y no se agrega otro monto.

#### Scenario: Confirmar mueve el faltante sugerido
- **GIVEN** un sobre en rojo con un faltante y otro sobre del mismo plan elegido en la tarjeta
- **WHEN** el usuario confirma
- **THEN** el asignado del sobre en problema aumenta en el faltante, el asignado del sobre elegido disminuye en el mismo monto, la actividad de ambos no cambia y los otros meses quedan igual

### Requirement: Al quedar cubierto desaparece el rojo
Si después del movimiento la actividad del sobre ya no supera a su asignado, ese sobre MUST dejar de mostrarse en rojo. La vista MUST mostrar un mensaje claro de resultado. El texto exacto de ese mensaje, el de no haber otro sobre y el de no poder completar el movimiento es PENDIENTE. Si no hay otro sobre en el plan, MUST NOT moverse dinero y MUST verse un mensaje. Si el asignado del sobre de origen es menor que el faltante, el movimiento MUST NOT guardarse hasta resolver esa pregunta abierta. Si, además de asignado, hay que reescribir el disponible guardado, es PENDIENTE: este cambio no define esa fórmula y MUST NOT inventarla.

#### Scenario: El rojo desaparece al cubrir
- **GIVEN** un sobre en rojo y un movimiento confirmado que deja su asignado mayor o igual que su actividad
- **WHEN** el usuario vuelve a ver la tabla
- **THEN** ese sobre ya no se muestra en rojo y hay un mensaje claro de resultado

#### Scenario: Sin otro sobre no se mueve dinero
- **GIVEN** un sobre en rojo y ningún otro sobre en el mismo plan
- **WHEN** el usuario abre la tarjeta
- **THEN** no se mueve dinero y ve un mensaje

#### Scenario: Origen insuficiente no se guarda
- **GIVEN** un sobre de origen cuyo asignado es menor que el faltante sugerido
- **WHEN** el usuario confirma
- **THEN** no se guarda el movimiento
