# RemeoMedicalService 

Sistema de gestión de turnos médicos desarrollado en **PHP**, **MySQL** y **HTML/CSS/JS**.

---

##  Descripción del Proyecto

RemeoMedicalService es una aplicación web para administrar turnos de enfermeros a domicilio. Permite que coordinadores asignen turnos, administradores supervisen el sistema, y enfermeros visualicen sus asignaciones.

**Rama actual:** `BaseProyecto` (Base del proyecto con autenticación funcional)

---

##  Inicio Rápido

### Requisitos
- XAMPP instalado (con Apache y MySQL)
- PHP 7.4+
- MySQL 5.7+

### Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone <url-del-repositorio>
   cd RemeoMedicalService
   git checkout BaseProyecto
   ```

2. **Configurar XAMPP:** Elige una opción

   **Opción A: DocumentRoot (Recomendado)**
   ```
   Cambia en C:\xampp\apache\conf\httpd.conf:
   DocumentRoot "C:/xampp/htdocs/RemeoMedicalService/public"
   ```

   **Opción B: Virtual Host** (Edita `C:\xampp\apache\conf\extra\httpd-vhosts.conf`)
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/xampp/htdocs/RemeoMedicalService/public"
       ServerName remeo.local
       <Directory "C:/xampp/htdocs/RemeoMedicalService/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
   Agrega en `C:\Windows\System32\drivers\etc\hosts`:
   ```
   127.0.0.1 remeo.local
   ```

3. **Crear base de datos:**
   ```bash
   # Opción 1: Por terminal
   mysql -u root < sql/schema.sql
   php reload_database.php

   # Opción 2: Por phpMyAdmin
   # Importa sql/schema.sql y ejecuta php reload_database.php
   ```

4. **Iniciar XAMPP:**
   - Inicia Apache y MySQL desde el Panel de Control de XAMPP

5. **Acceder a la aplicación:**
   - http://localhost (si usaste DocumentRoot)
   - http://localhost/remeo (si usaste Alias)
   - http://remeo.local (si usaste Virtual Host)

---

## 👥 Usuarios de Prueba

| Rol | Email | Contraseña | Acceso |
|-----|-------|-----------|--------|
| **Coordinador** | coordinador@remeo.local | demo1234 | Ver/Crear turnos |
| **Administrador** | admin@remeo.local | demo1234 | Acceso total |
| **Enfermero** | enfermero1@remeo.local | demo1234 | Ver mis turnos |

---

##  Estructura del Proyecto

```
RemeoMedicalService/
├── config/
│   ├── database.php          # Configuración de BD
│   └── session.php           # Configuración de sesiones
├── public/
│   ├── index.php             # Router principal
│   ├── .htaccess             # Reescritura de URLs
│   ├── css/
│   │   └── style.css         # Estilos principales
│   └── js/
│       └── app.js            # Scripts frontend
├── src/
│   ├── controllers/          # Lógica de aplicación
│   │   ├── AuthController.php
│   │   └── TurnoController.php
│   ├── models/               # Modelos de datos
│   │   ├── Model.php
│   │   ├── User.php
│   │   ├── Turno.php
│   │   ├── Domicilio.php
│   │   ├── Historial.php
│   │   └── ReglaLaboral.php
│   ├── helpers/              # Funciones auxiliares
│   │   └── Auth.php          # Gestión de autenticación
│   └── views/                # Vistas HTML/PHP
│       ├── layouts/
│       │   ├── header.php
│       │   └── footer.php
│       ├── auth/
│       │   ├── login.php
│       │   └── logout.php
│       ├── turno/
│       │   ├── list.php
│       │   └── mis_turnos.php
│       └── dashboard.php
├── sql/
│   ├── schema.sql            # Esquema de BD
│   ├── sample_data.sql       # Datos de prueba
│   └── reload_data.sql       # Script para recargar datos
├── storage/
│   └── logs/                 # Archivos de log
├── reload_database.php       # Script de recarga de BD
└── README.md                 # Este archivo
```

---

##  Base de Datos

### Tablas Principales

| Tabla | Descripción |
|-------|-------------|
| **usuarios** | Coordinadores, administradores y enfermeros |
| **turnos** | Turnos asignados con fecha, hora y estado |
| **domicilios** | Ubicaciones de los turnos |
| **historial** | Registro de cambios realizados |
| **reglas_laborales** | Parámetros de validación de turnos |

### Campos Importantes

**usuarios:**
- `id`, `nombre`, `rol` (coordinador/administrador/enfermero), `email`, `password_hash`

**turnos:**
- `id`, `fecha`, `hora_inicio`, `hora_fin`, `estado`, `enfermero_id`, `domicilio_id`, `coordinador_id`

**reglas_laborales:**
- `horas_maximas`, `descanso_minimo` (para validaciones futuras)

---

##  Autenticación y Roles

### Sistema de Sesiones
- Sesiones PHP seguras con HTTPOnly cookies
- Timeout de 1 hora
- Validación de credenciales con bcrypt

### Control de Acceso
- **Coordinador**: Ver/Crear turnos, asignar a enfermeros
- **Administrador**: Acceso total al sistema
- **Enfermero**: Ver solo sus turnos asignados

### Rutas Protegidas
```
/login          → Público (sin autenticación)
/logout         → Requiere autenticación
/dashboard      → Requiere autenticación
/turnos         → Requiere coordinador o admin
/mis-turnos     → Requiere enfermero
```

---

##  Funcionalidades Implementadas (Base)

 **Autenticación**
- Login con validación de credenciales
- Logout seguro
- Gestión de sesiones
- Timeout de sesión

 **Roles y Permisos**
- Sistema de 3 roles
- Control de acceso por ruta
- Dashboards personalizados

 **Gestión de Turnos (Lectura)**
- Listado de turnos (coordinador/admin)
- Mis turnos asignados con un calendario interactivo para enfermero(enfermero)
- Visualización con estados

 **Base de Datos**
- Esquema relacional completo
- Integridad referencial
- Datos de prueba incluidos

---

##  Flujo de Trabajo

### Para Desarrolladores

1. **Clona la rama BaseProyecto:**
   ```bash
   git clone <repo>
   git checkout BaseProyecto
   ```

2. **Configura el entorno:**
   ```bash
   php reload_database.php
   ```

3. **Inicia desarrollo en una rama feature:**
   ```bash
   git checkout -b feature/tu-feature
   ```

4. **Haz commit y push:**
   ```bash
   git add .
   git commit -m "feat: descripción de cambios"
   git push origin feature/tu-feature
   ```

5. **Crea Pull Request a `Dev`:**
   - Desde `feature/tu-feature` → `Dev`

### Desde BaseProyecto hacia Dev
- Todos los cambios en BaseProyecto deben sincronizarse a Dev mediante Pull Request
- La rama `Dev` integra todas las features
- Desde `Dev` se hacen releases a `main/master`

---

##  Próximas Funcionalidades

- [ ] CRUD completo de turnos (crear, editar, eliminar)
- [ ] Asignación automática/manual de turnos a enfermeros
- [ ] Validación de reglas laborales (horas máximas, descanso)
- [ ] Gestión de usuarios (crear, editar, desactivar)
- [ ] Historial completo de cambios
- [ ] Reportes y estadísticas
- [ ] Notificaciones
- [ ] API REST (opcional)

---

##  Notas Importantes

### Seguridad
- Las contraseñas se hashean con bcrypt
- Sesiones con HTTPOnly cookies
- Validación de permisos en cada ruta
- CSRF protection (por implementar)

### Desarrollo
- Mantén la estructura de carpetas
- Sigue el patrón MVC
- Usa PDO para consultas seguras
- Documenta cambios significativos

### Base de Datos
- Para resetear datos: `php reload_database.php`
- Para cambiar credenciales: edita `reload_database.php`
- Los hashes de contraseña son bcrypt con cost=10

---

##  Contacto / Soporte

- **Rama:** BaseProyecto
- **Estado:**  Funcional (Autenticación e interfaz base)
- **Última actualización:** Mayo 2026

---

##  Licencia

Este proyecto es privado. Todos los derechos reservados.
