# Spec Delta

## Purpose

Permite crear una cuenta de usuario, iniciar sesión, cerrar sesión y actualizar nombre y correo, en una landing propia distinta del dashboard.

## ADDED Requirements

### Requirement: Crear cuenta al inicio
Un visitante MUST ver al inicio una pantalla de crear cuenta con el diseño de landing (presentación a la izquierda, formulario a la derecha), con nombre, correo, contraseña y confirmar contraseña. MUST NOT mostrar el chrome del dashboard (navegación, cuentas, presupuesto). Tras un registro válido MUST quedar autenticado y ver el dashboard. Si las contraseñas no coinciden o el correo ya existe, MUST mostrar un error y MUST NOT crear la cuenta de usuario.

#### Scenario: Visitante ve la landing de crear cuenta
- **GIVEN** un visitante
- **WHEN** solicita la ruta principal
- **THEN** ve la landing con el formulario de nombre, correo, contraseña y confirmar contraseña, y no ve la barra lateral de presupuesto ni el listado de cuentas

#### Scenario: Registro válido autentica
- **GIVEN** un visitante en crear cuenta
- **WHEN** envía nombre, correo y contraseñas coincidentes que no están en uso
- **THEN** queda autenticado y ve el dashboard, no la landing

#### Scenario: Contraseñas distintas
- **GIVEN** un visitante en la pantalla de crear cuenta
- **WHEN** envía contraseña y confirmar contraseña distintas
- **THEN** ve un error y no se crea la cuenta de usuario

#### Scenario: Correo ya registrado
- **GIVEN** un correo que ya pertenece a una cuenta de usuario
- **WHEN** un visitante intenta crear cuenta con ese correo
- **THEN** ve un error y no se crea otra cuenta de usuario

### Requirement: Inicio de sesión
Un visitante MUST poder iniciar sesión con correo y contraseña sobre la misma landing (sin dashboard). Desde crear cuenta MUST poder ir a inicio de sesión, y desde inicio de sesión MUST poder ir a crear cuenta. Credenciales inválidas MUST mostrar un error y MUST NOT autenticar.

#### Scenario: Acceso entre crear cuenta e inicio de sesión
- **GIVEN** un visitante en crear cuenta
- **WHEN** elige iniciar sesión
- **THEN** ve el formulario de correo y contraseña en la landing, sin el dashboard

#### Scenario: Inicio de sesión válido
- **GIVEN** una cuenta de usuario existente
- **WHEN** el visitante envía el correo y la contraseña correctos
- **THEN** queda autenticado y ve el dashboard

#### Scenario: Credenciales inválidas
- **GIVEN** un visitante en inicio de sesión
- **WHEN** envía correo o contraseña incorrectos
- **THEN** ve un error y no queda autenticado

### Requirement: Logout
El usuario autenticado MUST poder cerrar sesión desde el dashboard. Tras el logout MUST ver la landing de crear cuenta, no el dashboard.

#### Scenario: Cierra sesión
- **GIVEN** un usuario autenticado en el dashboard
- **WHEN** usa el botón de logout
- **THEN** deja de estar autenticado y ve la landing de crear cuenta

### Requirement: Configuración de cuenta de usuario
El usuario autenticado MUST poder ver y actualizar el nombre y el correo de su cuenta de usuario dentro del dashboard. Un visitante MUST NOT acceder a esa pantalla.

#### Scenario: Usuario autenticado actualiza nombre y correo
- **GIVEN** un usuario autenticado en configuración de cuenta de usuario
- **WHEN** guarda un nombre y un correo válidos
- **THEN** su cuenta de usuario queda con esos valores

#### Scenario: Visitante no accede a la configuración
- **GIVEN** un visitante
- **WHEN** solicita la configuración de cuenta de usuario
- **THEN** no ve esa pantalla y se le pide autenticarse
