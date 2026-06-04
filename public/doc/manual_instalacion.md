# Manual de Instalación y Configuración
## Sistema de Gestión de Permisos

Este manual describe detalladamente los pasos para desplegar y configurar el sistema de gestión de permisos en un entorno de desarrollo local (como XAMPP en Windows) o en un servidor de producción.

---

## 1. Requisitos del Sistema

Para el correcto funcionamiento de la aplicación, asegúrese de contar con las siguientes herramientas:
*   **PHP:** Versión `8.2` o superior.
*   **Gestor de Dependencias PHP:** [Composer](https://getcomposer.org/) v2.x.
*   **Gestor de Paquetes JS:** [Node.js](https://nodejs.org/) (incluye NPM) v18.x o superior.
*   **Servidor Web:** Apache (incluido en XAMPP), Nginx o IIS.
*   **Base de Datos:**
    *   **Principal:** Microsoft SQL Server (2012 o superior).
    *   **Alternativa:** MySQL / MariaDB (también soportado).
*   **Extensiones PHP requeridas:**
    *   `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`.
    *   **Para SQL Server:** `sqlsrv` y `pdo_sqlsrv` habilitadas en PHP.
    *   **Para MySQL:** `pdo_mysql` habilitada en PHP.

---

## 2. Configuración Especial para SQL Server (en Windows / XAMPP)

Si utilizará Microsoft SQL Server como base de datos, deberá instalar manualmente los controladores de PHP. Siga estos pasos:

### Paso 1: Descargar los controladores de PHP para SQL Server
1. Visite la descarga oficial de Microsoft: [Microsoft Drivers for PHP for SQL Server](https://learn.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server).
2. Descargue el instalador compatible con su versión de PHP (por ejemplo, PHP 8.2 requiere la versión 5.11 o superior de los controladores).

### Paso 2: Copiar las extensiones al directorio de PHP
1. Extraiga el archivo descargado.
2. Copie los archivos `.dll` correspondientes a su versión de PHP y arquitectura (Thread Safe - `ts` - es la que suele usar XAMPP) en la carpeta `C:\xampp\php\ext\`.
   *   *Ejemplo para PHP 8.2 en 64 bits:*
       *   `php_sqlsrv_82_ts_x64.dll`
       *   `php_pdo_sqlsrv_82_ts_x64.dll`

### Paso 3: Habilitar las extensiones en `php.ini`
1. Abra el archivo de configuración `C:\xampp\php\php.ini`.
2. Busque la sección de extensiones y agregue las siguientes líneas:
   ```ini
   extension=php_sqlsrv_82_ts_x64.dll
   extension=php_pdo_sqlsrv_82_ts_x64.dll
   ```
3. Guarde el archivo y reinicie su servidor Apache desde el Panel de XAMPP.

### Paso 4: Instalar el Microsoft ODBC Driver
Para que los controladores PHP puedan comunicarse con SQL Server, debe instalar el **Microsoft ODBC Driver for SQL Server** en el sistema operativo Windows. Descárguelo e instálelo desde la página oficial de Microsoft.

---

## 3. Instalación de la Aplicación

Siga los pasos descritos a continuación para levantar la aplicación desde cero:

### Paso 1: Descargar el Código Fuente
Coloque la carpeta del proyecto dentro del directorio raíz de su servidor web (por ejemplo, en `C:\xampp\htdocs\permisos\`).

### Paso 2: Instalar Dependencias del Backend (PHP)
Abra una terminal en la raíz del proyecto y ejecute:
```bash
composer install
```

### Paso 3: Configurar Variables de Entorno
1. Cree una copia del archivo `.env.example` y renómbrela a `.env`:
   ```bash
   copy .env.example .env
   ```
2. Abra el archivo `.env` y configure la conexión de base de datos elegida.

#### Opción A: Configuración para SQL Server (Configuración por Defecto)
```env
DB_CONNECTION=sqlsrv
DB_HOST=192.168.120.1 # IP o localhost de su SQL Server
DB_PORT=1433
DB_DATABASE=permisos_dev
DB_USERNAME=gescobar
DB_PASSWORD=su_contraseña
DB_ENCRYPT=no
DB_TRUST_SERVER_CERTIFICATE=true
```

#### Opción B: Configuración Alternativa para MySQL
Si decide cambiar a MySQL, comente las líneas de SQL Server y descomente el bloque correspondiente en el `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=root
DB_PASSWORD=
```

### Paso 4: Generar la Llave de Seguridad de Laravel
Genere el `APP_KEY` necesario para el cifrado de datos y sesiones:
```bash
php artisan key:generate
```

### Paso 5: Ejecutar Migraciones e Inicializar Datos (Seeders)
Ejecute las migraciones para estructurar la base de datos y poblar las tablas con los datos base de configuración requeridos (estados, categorías, tipos de permisos, horarios y usuarios de prueba):
```bash
php artisan migrate --seed
```

### Paso 6: Crear el Enlace Simbólico de Almacenamiento
Cree el enlace para que las firmas y archivos adjuntos cargados a la carpeta privada de storage sean accesibles desde la web:
```bash
php artisan storage:link
```

### Paso 7: Instalar y Compilar Recursos Frontend
Instale los paquetes de Node.js y compile los scripts y estilos de Tailwind CSS/Vite:
```bash
npm install
npm run build
```

---

## 4. Configuración de Servidor Web y Virtual Host

Para que las URLs funcionen correctamente con nombres amigables (ej. `http://permisos.local`), se recomienda configurar un Virtual Host en Apache:

### Configurar Apache (`httpd-vhosts.conf`):
1. Abra el archivo `C:\xampp\apache\conf\extra\httpd-vhosts.conf`.
2. Añada la siguiente configuración al final del archivo:
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/xampp/htdocs/permisos/public"
       ServerName permisos.local
       <Directory "C:/xampp/htdocs/permisos/public">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

### Configurar archivo `hosts` en Windows:
1. Abra el Bloc de notas con privilegios de Administrador.
2. Abra el archivo `C:\Windows\System32\drivers\etc\hosts`.
3. Añada la siguiente línea al final:
   ```text
   127.0.0.1       permisos.local
   ```
4. Guarde el archivo y reinicie Apache. Ahora podrá ingresar al sistema desde `http://permisos.local`.

---

## 5. Soporte y Contacto
Este proyecto fue configurado y estructurado por el desarrollador. Si tiene dudas sobre la instalación o el código fuente, puede contactar al autor:

👤 **Geovanny Escobar**
*   **LinkedIn:** [Geovanny Escobar](https://www.linkedin.com/in/geovannyescobar/)
