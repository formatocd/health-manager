
# 🏥 Health Manager

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white) ![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=for-the-badge&logo=php&logoColor=white) ![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white) ![TailwindCSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

**Health Manager** es una plataforma que permite a los usuarios llevar un registro de citas, control de peso y expedientes médicos, con la capacidad de compartir estos datos de forma segura con otros usuarios (familiares, médicos o cuidadores).

## ✨ Características Principales

* **📅 Calendario Interactivo:** Gestión visual de citas médicas y eventos de salud.
* **⚖️ Control de Salud:** Registro y gráficas de evolución de peso.
* **📂 Historial Médico:** Almacenamiento digital de informes y documentos.
* **🤝 Compartición de Datos:** Sistema para compartir datos con otros usuarios (modo "Solo Lectura").
* **👥 Roles y Usuarios:** Panel de administración para gestión de usuarios.
* **🚀 Auto-Instalable:** Sistema de despliegue "Zero-Touch".

## 🛠️ Requisitos del Sistema

* PHP 8.4 o superior.
* Extensiones PHP requeridas por Laravel (Ctype, cURL, DOM, Fileinfo, Filter, Hash, Mbstring, OpenSSL, PCRE, PDO, Session, Tokenizer, XML).
* [Base de datos compatible con Laravel](https://laravel.com/docs/12.x/database) (MySQL, MariaDB, PostgreSQL, SQLite, etc).

## 📦 Instalación

Para cualquiera de los dos métodos de instalación, es necesario disponer de las siguientes herramientas:
* **Git**
* **Composer**
* **NPM** (Node.js)

Elige el método que se adapte a tu entorno:

### 🅰️ Opción A: Hosting con Consola (VPS / SSH)
Estas operaciones se realizan directamente conectado a la consola de tu servidor.

1.  **Clonar el repositorio:**
    ```bash
    git clone [https://github.com/tu-usuario/health-manager.git](https://github.com/tu-usuario/health-manager.git)
    cd health-manager
    ```

2.  **Instalar dependencias y construir assets:**
    ```bash
    composer install --no-dev --optimize-autoloader
    npm install && npm run build
    ```

3.  **Configuración inicial:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Nota: No es necesario ejecutar migraciones; el sistema las lanzará automáticamente al entrar en la web.*

### 🅱️ Opción B: Preparación en Local para Hosting Compartido (Sin Consola / FTP)
Estas operaciones se realizan en tu ordenador personal antes de subir los archivos.

1.  **Clonar el repositorio en tu equipo:**
    ```bash
    git clone [https://github.com/tu-usuario/health-manager.git](https://github.com/tu-usuario/health-manager.git)
    cd health-manager
    ```

2.  **Instalar dependencias y construir assets:**
    ```bash
    composer install --no-dev --optimize-autoloader
    npm install && npm run build
    ```

3.  **Configuración inicial:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Subida de ficheros:**
    Sube **todos** los archivos y carpetas del proyecto (incluyendo `vendor` y `public/build`) a tu hosting mediante FTP o Gestor de Archivos.

## ⚙️ Configuración del Entorno

Una vez tengas los archivos en el servidor (ya sea por Opción A o B), debes configurar el archivo **`.env`** y el servidor web.

### 1. Editar el archivo `.env`
Asegúrate de configurar las siguientes variables con los datos de tu hosting:

* **Aplicación (Producción):**
    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://tudominio.com
    ```
* **Base de Datos:**
    ```env
    DB_CONNECTION=mysql
    DB_HOST=host.de.tu.bd
    DB_PORT=3306
    DB_DATABASE=nombre_de_tu_bd
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_contraseña
    ```
* **Servidor de Correo (SMTP):**
    Necesario para el envío de notificaciones y recuperación de contraseñas.
    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.tuservidor.com
    MAIL_PORT=587
    MAIL_USERNAME=tu_email@tudominio.com
    MAIL_PASSWORD=tu_contraseña_email
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS="no-reply@tudominio.com"
    MAIL_FROM_NAME="${APP_NAME}"
    ```

### 2. Configurar Carpeta Pública
⚠️ **Importante:** Debes configurar tu servidor web o hosting para que el dominio apunte a la carpeta **/public** del proyecto, no a la raíz.

## 📖 Instrucciones de Uso

### 1. Primer Acceso (Creación del Administrador)
El sistema cuenta con una política de **"First User Takeover"**:
* Al entrar a la web por primera vez, el sistema detectará la base de datos vacía y **creará las tablas automáticamente**.
* Te redirigirá al registro obligatoriamente.
* **El primer usuario registrado** será el **ADMINISTRADOR** de la aplicación.

### 2. Gestión Diaria
* **Calendario:** Haz clic en cualquier día para ver los detalles de los registros que contenga. Para añadir un registro pulsa en el botón `+` de la esquina inferior derecha y selecciona el tipo de registro.
* **Perfil y Nick:** Configura tu `username` (Nick) en tu perfil para que otros te encuentren.
* **Compartir Datos:** Ve a `Perfil` -> `Compartir Datos` e introduce el Nick de un usuario existente para darle acceso de lectura.

## 📄 Licencia
Este proyecto es de código abierto bajo la licencia [MIT](https://opensource.org/licenses/MIT).
