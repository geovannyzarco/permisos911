# Gestor de Permisos de Empleados

Este proyecto es un sistema web empresarial diseñado para la solicitud, validación y flujo de aprobación de permisos laborales de empleados. Está construido sobre **Laravel 12** y **Filament v4**, integrando controles en tiempo real de cupos, límites de horas personales y acumulación de tiempo compensatorio.

---

## 🚀 Tecnologías Principales

*   **Framework Backend:** [Laravel 12](https://laravel.com)
*   **Panel de Administración e Interfaz:** [Filament v4](https://filamentphp.com) (Livewire, Alpine.js, Tailwind CSS)
*   **Seguridad y Permisos:** `spatie/laravel-permission` y `bezhansalleh/filament-shield`
*   **Reportes PDF:** `barryvdh/laravel-dompdf` (Generación de comprobantes oficiales firmados)
*   **Importaciones y Reportes Excel:** `pxlrbt/filament-excel` y `eightynine/filament-excel-import`
*   **Base de Datos:** SQL Server / MySQL (Soporta funciones de fecha y transacciones)

---

## ✨ Características Clave del Sistema

### 1. Gestión Integral de Solicitudes (Fórmula de Permisos)
El sistema permite solicitar 17 tipos de permisos distintos. Al crearse, se inicializan con estados **Pendiente** y requieren un flujo de tres firmas:
1.  **VB (Visto Bueno):** Otorgado por el supervisor o jefe directo.
2.  **Aprobación Jefatura:** Otorgado por el Jefe de la Unidad o Departamento.
3.  **Aprobación Jefe de División:** Aprobación final y definitiva que habilita el PDF oficial.

> [!WARNING]
> Las solicitudes se bloquean para edición y eliminación una vez que algún revisor otorga un visto bueno/aprobación o cuando el registro es marcado como **Tramitado** por Recursos Humanos.

### 2. Control de Permisos Personales (Horas Personales)
*   El sistema recupera automáticamente las horas personales anuales asignadas al horario del empleado.
*   En tiempo real, calcula la duración del permiso solicitado (en base a los campos *Desde* y *Hasta*) y valida que no supere el saldo de horas disponibles del empleado.

### 3. Gestión de Tiempo Compensatorio (Horas Extras)
*   Permite canjear horas extras trabajadas por tiempo libre.
*   Incluye una sección repetible (*Repeater*) donde el usuario debe detallar las jornadas extras con justificaciones y adjuntos.
*   **Validaciones Automatizadas:**
    *   La suma de horas de las jornadas compensadas debe coincidir exactamente con el tiempo solicitado.
    *   Las jornadas extras ingresadas no deben superar los **6 meses** de antigüedad.

### 4. Control de Cupos y Disponibilidad de Grupo
*   Los empleados se organizan en grupos operativos con límites de permisos diarios configurados.
*   El formulario de solicitud alerta en tiempo real si hay conflictos (bloqueos de cupo por otros miembros del grupo ya aprobados en el mismo rango de fechas).
*   Muestra un listado dinámico de disponibilidad con estados `🟢 Disponible` o `🔴 Lleno`.

### 5. Calendario de Permisos Interactivo
*   Desarrollado en **Livewire**, muestra de forma mensual las ausencias programadas.
*   **Restricciones de Privacidad:** Un empleado regular de Nivel 1 solo puede visualizar ausencias de miembros de su propio **Grupo** (o de su **Unidad** si carece de grupo).
*   Permite a los usuarios autorizados hacer clic en un día del calendario para desplegar detalles y descargar el PDF de permisos aprobados.

---

## 👥 Roles y Niveles de Usuario

El sistema clasifica a los empleados mediante cuatro niveles jerárquicos definidos en `Empleado.php`:

1.  **Nivel 1 (`NIVEL_EMPLEADO`):** Personal operativo. Crea sus solicitudes de permiso, visualiza su dashboard personal de estadísticas y consulta el calendario restringido a su grupo.
2.  **Nivel 2 (`NIVEL_JEFE_GRUPO`):** Supervisores. Otorgan el visto bueno inicial (VB) y tienen visibilidad del calendario a nivel de unidad.
3.  **Nivel 3 (`NIVEL_JEFE_UNIDAD`):** Jefes de Unidad o Departamento. Otorgan la aprobación intermedia (Jefatura) de las solicitudes.
4.  **Nivel 4 (`NIVEL_JEFE_DIV`):** Jefes de División. Otorgan la aprobación final y definitiva de las solicitudes.

---

## 🛠️ Instalación y Configuración Local

Siga estos pasos para configurar el entorno de desarrollo en su máquina local:

1.  **Clonar el repositorio y entrar al directorio:**
    ```bash
    git clone <url-del-repositorio> permisos
    cd permisos
    ```

2.  **Instalar dependencias de PHP y JavaScript:**
    ```bash
    composer install
    npm install
    ```

3.  **Configurar variables de entorno:**
    Copie el archivo `.env.example` como `.env` y configure sus credenciales de base de datos y llaves de acceso:
    ```bash
    copy .env.example .env
    ```

4.  **Generar la llave de la aplicación y ejecutar migraciones/seeders:**
    El sistema cuenta con seeders completos para establecer categorías, estados, horarios, tipos de permisos y usuarios de prueba:
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```

5.  **Compilar recursos y levantar servidores de desarrollo:**
    ```bash
    npm run build
    # Para desarrollo continuo:
    npm run dev
    php artisan serve
    ```

---

## 📄 Documentación Adicional

*   **Manual del Usuario (Empleado Nivel 1):** Guía paso a paso para el personal operativo. Disponible en [manual_empleado_nivel_1.md](file:///c:/xampp/htdocs/permisos/public/doc/manual_empleado_nivel_1.md).
*   **Manual de Instalación y Configuración:** Guía técnica para desplegar el proyecto y activar controladores SQL Server en XAMPP. Disponible en [manual_instalacion.md](file:///c:/xampp/htdocs/permisos/public/doc/manual_instalacion.md).
*   **Manual del Administrador (Superadmin):** Detalla el mapeo de usuarios, límites de horas, cupos por grupo e importación de marcaciones. Disponible en [manual_superadmin.md](file:///c:/xampp/htdocs/permisos/public/doc/manual_superadmin.md).

---

## 👥 Desarrollador y Créditos

Este sistema ha sido desarrollado y configurado por:

👤 **Geovanny Escobar**
*   **LinkedIn:** [Geovanny Escobar](https://www.linkedin.com/in/geovannyescobar/)
