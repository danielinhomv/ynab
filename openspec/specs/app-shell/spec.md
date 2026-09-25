# app-shell Specification

## Purpose

Define el cascarón visual de la aplicación: barra superior y barra lateral vacías, con la estructura de las vistas de referencia y sin funcionalidad de negocio.

## Requirements

### Requirement: Layout base con barras vacías
La ruta principal MUST mostrar un layout con tres regiones: barra superior a lo ancho, barra lateral izquierda y área de contenido. La barra superior y la barra lateral MUST existir y MUST estar vacías de ítems de navegación, cuentas, totales, botones de acción y datos de presupuesto. El área de contenido MUST estar presente y MUST NOT mostrar formularios de registro, landing de marketing ni reglas de negocio.

#### Scenario: Visitante ve el cascarón vacío
- **GIVEN** un visitante abre la aplicación
- **WHEN** solicita la ruta principal
- **THEN** ve barra superior, barra lateral izquierda y área de contenido, y las dos barras no contienen enlaces, listas ni datos de negocio

#### Scenario: Sin funcionalidad de negocio
- **GIVEN** la pantalla del layout base
- **WHEN** el visitante la inspecciona
- **THEN** no hay autenticación, planes, cuentas, sobres ni dinero por asignar
