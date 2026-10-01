# Spec Delta

## Purpose

Permite editar, desde el plan y en una sola pantalla, el nombre, la moneda única, la fecha, el number format y el currency placement.

## ADDED Requirements

### Requirement: Acceso en un clic desde el plan
Un usuario autenticado MUST abrir la configuración de su plan con un clic desde la vista de ese plan. La pantalla MUST mostrar solo estos campos, ya rellenos con lo guardado: nombre, moneda, fecha, number format y currency placement. MUST usar el dashboard ya existente. MUST NOT ser el ítem «Configuración» del menú del avatar ni la pantalla de cuenta. Un visitante MUST NOT abrirla. MUST NOT abrir el plan de otro usuario. Si el usuario tiene varios planes y el plan abierto sigue PENDIENTE, MUST NOT elegir uno.

#### Scenario: Un clic abre los campos del plan
- **GIVEN** un usuario autenticado en la vista de su único plan
- **WHEN** hace un clic en el acceso de esa vista
- **THEN** ve nombre, moneda, fecha, number format y currency placement con los valores guardados de ese plan

#### Scenario: No es la configuración de la cuenta
- **GIVEN** un usuario autenticado en la vista del plan
- **WHEN** abre esta pantalla
- **THEN** no está en la pantalla de cuenta y el ítem «Configuración» del avatar sigue sin navegar

#### Scenario: No abre un plan ajeno
- **GIVEN** un plan de otro usuario
- **WHEN** el usuario autenticado usa el acceso desde su plan
- **THEN** no ve ni edita los campos de ese otro plan

### Requirement: Guardar sin errores y confirmar
Un envío válido MUST guardar el nombre, la moneda, la fecha, el number format y el currency placement de ese plan, y MUST mostrar una confirmación visible. El nombre vacío MUST mostrar el error en el campo y MUST NOT guardar. Un valor que no es válido MUST mostrar el error en el campo y MUST NOT guardar. El texto de la confirmación y de los errores es PENDIENTE. El significado de la fecha es PENDIENTE: se guarda el valor editado y no se define aquí. Las opciones de moneda, number format y currency placement son PENDIENTE. Si el encabezado «Plan activo» y la moneda del cascarón muestran lo guardado, es PENDIENTE.

#### Scenario: Un guardado válido se confirma
- **GIVEN** un usuario autenticado en la configuración de su plan, con los cinco campos válidos
- **WHEN** guarda
- **THEN** el plan conserva esos valores y ve una confirmación visible

#### Scenario: El nombre vacío no se guarda
- **GIVEN** un usuario autenticado en la configuración de su plan
- **WHEN** deja el nombre vacío y guarda
- **THEN** ve el error en el nombre y el plan no cambia

### Requirement: Una sola moneda, sin reescribir montos
La moneda del plan MUST seguir siendo una. Guardar otra en el selector MUST reemplazar la anterior y MUST NOT dejar dos monedas en ese plan. Si el plan ya tiene montos guardados, cambiar la moneda es PENDIENTE: MUST NOT convertir esos montos ni reescribirlos. MUST NOT crear otro plan desde esta pantalla.

#### Scenario: El selector no agrega una segunda moneda
- **GIVEN** un plan con una moneda y sin montos guardados
- **WHEN** el usuario guarda otra moneda en el selector
- **THEN** el plan queda con esa única moneda nueva

#### Scenario: Con montos no se convierten
- **GIVEN** un plan que ya tiene montos guardados
- **WHEN** el usuario cambia la moneda
- **THEN** esos montos no se convierten ni se reescriben
