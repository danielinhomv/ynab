# Spec Delta

## Purpose

Deja la aplicación Laravel apuntando a PostgreSQL como base de datos, según el stack del proyecto.

## ADDED Requirements

### Requirement: Aplicación configurada para PostgreSQL
La configuración de ejemplo de la aplicación MUST declarar PostgreSQL como conexión de base de datos, con host, puerto, nombre de base, usuario y contraseña listos para completar en el entorno local. La aplicación MUST usar esa conexión de PostgreSQL cuando el entorno no es de pruebas.

#### Scenario: Archivo de ejemplo usa PostgreSQL
- **GIVEN** un entorno nuevo copiado desde la configuración de ejemplo
- **WHEN** se revisa la conexión de base de datos declarada
- **THEN** la conexión es PostgreSQL y no SQLite

#### Scenario: Entorno de pruebas no obliga PostgreSQL
- **GIVEN** la suite de pruebas automatizadas
- **WHEN** se ejecutan las pruebas
- **THEN** pueden usar SQLite en memoria u otra base aislada, sin exigir PostgreSQL
