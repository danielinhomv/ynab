# Spec Delta

## Purpose

Deja el frontend de la aplicación en Blade con Livewire, según el stack definido, sin añadir pantallas de negocio.

## ADDED Requirements

### Requirement: Frontend Blade con Livewire
La aplicación MUST renderizar sus pantallas con Blade y MUST tener Livewire instalado y operativo para componentes de servidor. MUST seguir usando el pipeline de assets ya presente (Vite y Tailwind). MUST NOT introducir otro framework de frontend.

#### Scenario: Livewire está disponible
- **GIVEN** las dependencias de PHP de la aplicación instaladas
- **WHEN** se solicita una página que usa el layout de la aplicación
- **THEN** Livewire está cargado y la página se renderiza con Blade

#### Scenario: Sin otro stack de frontend
- **GIVEN** el proyecto configurado
- **WHEN** se revisan las dependencias de frontend
- **THEN** no hay un SPA ni un framework de UI distinto de Blade, Livewire y Tailwind
