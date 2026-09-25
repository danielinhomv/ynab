# Spec Delta

## MODIFIED Requirements

### Requirement: Layout base con barras vacías
El usuario autenticado MUST ver un dashboard con tres regiones según la vista de referencia: barra superior a lo ancho (marca, plan, usuario), barra lateral izquierda (navegación y cuentas) y área de contenido del presupuesto. Un visitante MUST NOT ver este dashboard; MUST ver la landing de crear cuenta. El dashboard MUST NOT incluir el formulario de registro ni la columna de marketing de la landing.

#### Scenario: Usuario autenticado ve el dashboard
- **GIVEN** un usuario autenticado
- **WHEN** solicita la ruta principal
- **THEN** ve barra superior, barra lateral con navegación y área de presupuesto, y no ve el formulario de crear cuenta ni el titular de la landing

#### Scenario: Visitante no ve el dashboard en la ruta principal
- **GIVEN** un visitante
- **WHEN** solicita la ruta principal
- **THEN** ve la landing de crear cuenta y no ve la barra lateral de cuentas ni la navegación de presupuesto

#### Scenario: Dashboard y landing son vistas distintas
- **GIVEN** las dos pantallas de la aplicación
- **WHEN** se comparan visitante y usuario autenticado
- **THEN** no comparten el mismo chrome: la landing no tiene aside de presupuesto y el dashboard no tiene el formulario de registro
