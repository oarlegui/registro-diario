# Registro-Diario

Sistema de registro diario de visitantes para control de acceso, diseñado específicamente para despliegue en entornos de hosting cPanel.

## Características

- ✅ **Autenticación de usuarios** con contraseñas hasheadas (bcrypt)
- ✅ **Diseño responsivo** inspirado en Google Workspace
- ✅ **Formulario de registro** completo con todos los campos requeridos
- ✅ **Validación de RUT chileno** con formato automático
- ✅ **Autocompletado** para nombres y empresas desde datos previos
- ✅ **Hora de entrada automática** al enviar el formulario
- ✅ **Tab de salidas pendientes** para actualizar hora de salida
- ✅ **Protección CSRF** en todos los formularios
- ✅ **Base de datos MySQL** con schema incluido
- ✅ **Arquitectura modular** fácil de mantener y escalar

## Estructura del Proyecto

```
registro-diario/
├── app/                          # Lógica PHP del backend
│   ├── Auth.php                  # Clase de autenticación
│   ├── CSRF.php                  # Protección contra CSRF
│   ├── Record.php                # Modelo de registros
│   ├── save_record.php           # Guardar nuevo registro
│   ├── update_exit_time.php      # Actualizar hora de salida
│   ├── get_pending_records.php   # Obtener registros pendientes
│   ├── autocomplete.php          # Sugerencias de autocompletado
│   └── logout.php                # Cerrar sesión
├── config/
│   └── database.php              # Configuración de base de datos
├── database/
│   └── schema.sql                # Schema de base de datos MySQL
├── public/                       # Archivos públicos
│   ├── css/
│   │   └── style.css             # Estilos (diseño Google Workspace)
│   ├── js/
│   │   └── app.js                # JavaScript del frontend
│   ├── index.php                 # Dashboard principal
│   └── login.php                 # Página de login
├── .htaccess                     # Configuración Apache
└── README.md                     # Este archivo
```

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite habilitado
- Extensiones PHP:
  - PDO
  - pdo_mysql
  - session

## Instalación en cPanel

### Paso 1: Preparar Archivos

1. Descargue o clone este repositorio
2. Comprima todos los archivos en un archivo ZIP

### Paso 2: Subir Archivos

1. Inicie sesión en su cPanel
2. Vaya a **Administrador de Archivos** (File Manager)
3. Navegue a la carpeta `public_html` (o la carpeta donde desea instalar)
4. Suba el archivo ZIP
5. Haga clic derecho en el archivo ZIP y seleccione **Extraer** (Extract)
6. Elimine el archivo ZIP después de la extracción

### Paso 3: Crear Base de Datos

1. En cPanel, vaya a **MySQL® Databases**
2. Cree una nueva base de datos (ej: `registro_diario`)
3. Cree un nuevo usuario MySQL con contraseña segura
4. Asigne el usuario a la base de datos con **TODOS LOS PRIVILEGIOS**
5. Anote el nombre de la base de datos, usuario y contraseña

### Paso 4: Importar Schema de Base de Datos

1. En cPanel, vaya a **phpMyAdmin**
2. Seleccione la base de datos que creó
3. Haga clic en la pestaña **Importar** (Import)
4. Seleccione el archivo `database/schema.sql`
5. Haga clic en **Continuar** (Go)

O puede ejecutar manualmente las queries del archivo `database/schema.sql` en el SQL tab.

### Paso 5: Configurar Conexión a Base de Datos

1. Edite el archivo `config/database.php`
2. Actualice las siguientes constantes con sus credenciales de cPanel:

```php
define('DB_HOST', 'localhost');              // Usualmente localhost en cPanel
define('DB_NAME', 'su_nombre_de_bd');        // Nombre de su base de datos
define('DB_USER', 'su_usuario_mysql');       // Su usuario MySQL
define('DB_PASS', 'su_contraseña_mysql');    // Su contraseña MySQL
```

### Paso 6: Configurar Permisos

Asegúrese de que los siguientes directorios tengan permisos adecuados:

```bash
chmod 755 public/
chmod 755 app/
chmod 644 config/database.php
```

En cPanel, puede hacer clic derecho en carpetas/archivos y seleccionar **Cambiar Permisos** (Change Permissions).

### Paso 7: Verificar .htaccess

El archivo `.htaccess` debería estar en la raíz del proyecto. Si no lo ve:

1. En el Administrador de Archivos, haga clic en **Configuración** (Settings)
2. Marque **Mostrar Archivos Ocultos** (Show Hidden Files)
3. Verifique que `.htaccess` esté presente

## Acceso Inicial

Después de la instalación, puede acceder al sistema:

**URL:** `http://su-dominio.com/public/login.php`

**Credenciales por defecto:**
- Email: `admin@registro-diario.local`
- Contraseña: `admin123`

⚠️ **IMPORTANTE:** Cambie estas credenciales inmediatamente después del primer inicio de sesión.

## Uso del Sistema

### Registrar Nueva Visita

1. Inicie sesión en el sistema
2. En la pestaña **Nuevo Registro**:
   - Complete todos los campos obligatorios (marcados con *)
   - El RUT se validará automáticamente según formato chileno
   - La fecha se establece por defecto a hoy
   - Use el autocompletado para nombres y empresas de registros anteriores
   - La hora de entrada se registra automáticamente al guardar
3. Haga clic en **Guardar Registro**

### Actualizar Salida

1. Vaya a la pestaña **Salidas Pendientes**
2. Verá todos los registros sin hora de salida
3. Ingrese la hora de salida en el campo correspondiente
4. Haga clic en **Actualizar**

## Campos del Formulario

| Campo | Requerido | Descripción |
|-------|-----------|-------------|
| Fecha | Sí | Fecha del registro (por defecto: hoy) |
| Nombre | Sí | Nombre completo del visitante |
| RUT | Sí | RUT chileno (validado automáticamente) |
| Teléfono | No | Número de teléfono de contacto |
| Clasificación | Sí | Cliente/Proveedor/Otro |
| Empresa | No | Nombre de la empresa (autocompletado) |
| Empresa de Transporte | No | Nombre de la empresa transportista |
| Patente | No | Patente del vehículo |
| N° Factura/Guía | No | Número de factura o guía |
| Motivo de Visita | Sí | Descripción del motivo de la visita |

## Seguridad

El sistema incluye las siguientes medidas de seguridad:

- ✅ Contraseñas hasheadas con bcrypt
- ✅ Protección CSRF en todos los formularios
- ✅ Validación de sesiones
- ✅ Prepared statements para prevenir SQL injection
- ✅ Sanitización de entrada de datos
- ✅ Headers de seguridad HTTP
- ✅ Protección de archivos sensibles vía .htaccess

## Crear Nuevos Usuarios

Para crear nuevos usuarios, puede ejecutar el siguiente SQL en phpMyAdmin:

```sql
INSERT INTO users (email, password, role) VALUES 
('nuevo@email.com', PASSWORD_HASH_AQUI', 'user');
```

Para generar un hash de contraseña, puede usar este código PHP:

```php
<?php
echo password_hash('su_contraseña', PASSWORD_BCRYPT);
?>
```

## Personalización

### Cambiar Colores

Edite las variables CSS en `public/css/style.css`:

```css
:root {
    --primary-color: #1a73e8;      /* Color principal */
    --secondary-color: #34a853;    /* Color secundario */
    --danger-color: #ea4335;       /* Color de error */
    /* ... */
}
```

### Modificar Clasificaciones

Edite las opciones en `public/index.php`:

```html
<select id="classification" name="classification" required>
    <option value="Cliente">Cliente</option>
    <option value="Proveedor">Proveedor</option>
    <option value="Otro">Otro</option>
    <!-- Agregue más opciones aquí -->
</select>
```

Y actualice la definición de la columna en `database/schema.sql`:

```sql
classification ENUM('Cliente', 'Proveedor', 'Otro', 'Nueva_Opcion') NOT NULL,
```

## Soporte y Mantenimiento

### Backup de Base de Datos

Es recomendable hacer backups regulares:

1. En cPanel, vaya a **phpMyAdmin**
2. Seleccione su base de datos
3. Haga clic en **Exportar** (Export)
4. Seleccione **Rápido** (Quick) y formato **SQL**
5. Haga clic en **Continuar** (Go)

### Logs de Errores

Los errores se registran en el log de errores de PHP. En cPanel:

1. Vaya a **Métricas** > **Errores** (Metrics > Errors)
2. Revise los logs para diagnóstico

### Actualizar el Sistema

1. Haga backup de su base de datos y archivos
2. Descargue la nueva versión
3. Reemplace los archivos (excepto `config/database.php`)
4. Revise el changelog por cambios en la base de datos

## Solución de Problemas

### Error de conexión a base de datos

- Verifique las credenciales en `config/database.php`
- Confirme que el usuario tenga permisos en la base de datos
- Verifique que el host sea correcto (usualmente `localhost`)

### Página en blanco

- Active la visualización de errores temporalmente en PHP
- Revise los logs de errores de Apache/PHP
- Verifique los permisos de archivos y directorios

### .htaccess no funciona

- Verifique que mod_rewrite esté habilitado
- Contacte a su proveedor de hosting si es necesario

### Sesión no persiste

- Verifique que PHP session esté habilitado
- Revise permisos en el directorio de sesiones de PHP

## Licencia

Este proyecto está disponible para uso libre.

## Contacto

Para soporte o consultas, contacte al administrador del sistema.

---

**Nota:** Este sistema fue diseñado específicamente para entornos de hosting cPanel y cumple con las mejores prácticas de seguridad para aplicaciones PHP.
