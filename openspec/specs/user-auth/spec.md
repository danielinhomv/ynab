# user-auth Specification

## Purpose

Permite crear una cuenta de usuario, iniciar sesión y cerrar sesión, en una landing propia distinta del dashboard.

## Requirements

### Requirement: Inicio de sesión al inicio
Un visitante MUST ver al inicio la pantalla de iniciar sesión con el diseño de landing (presentación a la izquierda, formulario a la derecha), con correo y contraseña. MUST NOT mostrar el chrome del dashboard ni el formulario de crear cuenta. Desde ahí MUST poder ir a crear cuenta. Credenciales inválidas MUST mostrar un error y MUST NOT autenticar. Tras un inicio de sesión válido MUST ver el dashboard.

#### Scenario: Visitante ve la landing de iniciar sesión
- **GIVEN** un visitante
- **WHEN** solicita la ruta principal
- **THEN** ve la landing con el formulario de correo y contraseña, y no ve confirmar contraseña ni la barra lateral de presupuesto

#### Scenario: Acceso a crear cuenta
- **GIVEN** un visitante en iniciar sesión
- **WHEN** elige crear cuenta
- **THEN** ve el formulario de nombre, correo, contraseña y confirmar contraseña en la landing, sin el dashboard

#### Scenario: Inicio de sesión válido
- **GIVEN** una cuenta de usuario existente
- **WHEN** el visitante envía el correo y la contraseña correctos
- **THEN** queda autenticado y ve el dashboard

#### Scenario: Credenciales inválidas
- **GIVEN** un visitante en inicio de sesión
- **WHEN** envía correo o contraseña incorrectos
- **THEN** ve un error y no queda autenticado

### Requirement: Crear cuenta
Un visitante MUST poder crear cuenta con nombre, correo, contraseña y confirmar contraseña sobre la misma landing (sin dashboard), solo después de elegir crear cuenta. MUST poder volver a iniciar sesión. Tras un registro válido MUST quedar autenticado y ver el dashboard. Si las contraseñas no coinciden o el correo ya existe, MUST mostrar un error y MUST NOT crear la cuenta de usuario.

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

#### Scenario: Vuelve a iniciar sesión
- **GIVEN** un visitante en crear cuenta
- **WHEN** elige iniciar sesión
- **THEN** ve el formulario de correo y contraseña en la landing

### Requirement: Logout
El usuario autenticado MUST poder abrir el menú del avatar en la barra superior y MUST ver **Cerrar sesión**. Al usarlo MUST ver la landing de iniciar sesión, no el dashboard.

#### Scenario: Abre el menú del avatar
- **GIVEN** un usuario autenticado en el dashboard
- **WHEN** abre el menú del avatar
- **THEN** ve Cerrar sesión y Configuración

#### Scenario: Cierra sesión
- **GIVEN** un usuario autenticado en el dashboard
- **WHEN** usa el botón de logout
- **THEN** deja de estar autenticado y ve la landing de iniciar sesión

### Requirement: Configuración en el menú
El ítem **Configuración** del menú del avatar MUST ser visible y MUST NOT navegar ni abrir una vista.

#### Scenario: Configuración no hace nada
- **GIVEN** un usuario autenticado en el dashboard
- **WHEN** elige Configuración en el menú del avatar
- **THEN** no cambia de pantalla
