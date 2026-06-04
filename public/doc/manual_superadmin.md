# Manual del Super Administrador (Superadmin)
## Sistema de Gestión de Permisos

Este manual está dirigido a los administradores del sistema. Detalla el uso de los módulos administrativos de **Filament v4**, la configuración de parámetros críticos de negocio (límites de horas, cupos de grupo) y la gestión general de la plataforma.

---

## 1. Mapeo Crítico: Usuarios vs. Empleados

Para que un usuario pueda utilizar el sistema de manera correcta (ver sus saldos, solicitar permisos y ver el calendario restringido), su cuenta de usuario **debe estar vinculada a un registro de empleado**.

### Procedimiento de Mapeo:
1.  Vaya al módulo **Administración -> Usuarios**.
2.  Edite un usuario existente o cree uno nuevo.
3.  En el campo **Empleado Asociado** (o equivalente en el formulario), seleccione el empleado correspondiente usando su nombre u ONI.
4.  Asigne los roles correspondientes mediante el panel de roles provisto por Filament Shield (`super_admin`, `admin`, `jefe_division`, `jefe_unidad`, `jefe_grupo` o `empleado`).
5.  Guarde el registro.

> [!CAUTION]
> **Sin Vinculación:** Si un usuario inicia sesión con el rol de empleado pero no tiene un registro de `Empleado` asociado a su cuenta, el sistema mostrará alertas en el panel y no le permitirá realizar solicitudes, ya que carece de saldo de horas y grupo de trabajo definidos.

---

## 2. Configuración de la Estructura Organizativa

La estructura del personal se organiza jerárquicamente en **Divisiones**, **Unidades (o Departamentos)** y **Grupos de Trabajo**.

### A. Divisiones (`Divisions`)
Representa el nivel más alto de la institución (ej. División de Operaciones, División de Administración). Configure cada una desde **Administración -> Divisiones**.

### B. Unidades (`Unidads`)
Subdivisiones asociadas a una División específica. Se administran en **Administración -> Unidades**. Al crear una unidad, es obligatorio especificar a qué División pertenece.

### C. Grupos de Trabajo (`Grupos`)
Son los equipos operativos finales que pertenecen a una Unidad. Se administran en **Administración -> Grupos**.

> [!IMPORTANT]
> **Configuración de Cupos Diarios (`permisos_diarios`):**
> Al editar o crear un Grupo, encontrará el campo **Límite de Permisos Diarios**.
> *   Este número define cuántas personas de ese mismo grupo pueden tener permisos aprobados/pendientes en un mismo día.
> *   *Ejemplo:* Si define el límite en `2`, el sistema bloqueará automáticamente a un tercer empleado de ese grupo que intente solicitar un permiso en esa misma fecha.
> *   Si deja este campo vacío o asigna al empleado al grupo especial "Sin Grupo" (ID 12), el sistema no le aplicará restricciones de cupos diarios.

---

## 3. Horarios y Límites de Horas Personales

El saldo de horas disponibles para permisos personales (Tipo de Permiso ID 1) se configura en los **Horarios**.

1.  Vaya al módulo **Administración -> Horarios**.
2.  Cree o edite un horario laboral.
3.  Establezca el campo **Horas Personales**. Este valor representa el límite anual de horas personales que los empleados con este horario tienen derecho a solicitar.
4.  Asocie este horario a los empleados correspondientes en el módulo de **Empleados**.

---

## 4. Gestión de Empleados (`Empleados`)

En el módulo **Solicitudes/Administración -> Empleados**, usted puede gestionar las fichas de todo el personal:
*   **Identificación:** ONI (obligatorio y único), nombre, correo, teléfono y dirección.
*   **Fotografía y Firma:** Permite subir archivos multimedia de la foto y la firma escaneada del empleado.
*   **Parámetros Laborales:** Asignación de Grupo, Unidad, Categoría, Horario y Nivel (determina los permisos de aprobación del 1 al 4).
*   **Estado:** Activo (permite operar en el sistema) o Inactivo/Suspendido (bloquea el acceso).

---

## 5. Importación Masiva de Marcaciones

El sistema permite importar registros de asistencia (entradas/salidas) mediante archivos externos en formatos `.dat`, `.txt` o `.csv`.

### Procedimiento:
1.  Vaya a **Administración -> Marcaciones**.
2.  En la esquina superior derecha, haga clic en el botón **Importar Marcaciones**.
3.  En el cuadro de diálogo, suba el archivo de marcaciones de su reloj marcador.
4.  Haga clic en **Importar**.
5.  El sistema procesará las líneas mediante el servicio de importación, registrando las nuevas marcaciones y omitiendo automáticamente los registros duplicados para evitar inconsistencias.
6.  Al finalizar, recibirá una notificación en pantalla con el resumen: *Importadas: X | Duplicadas/Omitidas: Y*.

---

## 6. Control Global de Solicitudes (`GestionPermisos`)

Como Superadmin, usted tiene acceso al módulo **Gestión de Permisos**:
*   **Visualización Completa:** Puede ver todos los permisos de todos los empleados de la institución.
*   **Filtros Avanzados:** Filtrar por fechas, estados de Visto Bueno (VB), Jefatura o División.
*   **Acción de Tramitado:** Cuando Recursos Humanos procesa manualmente el permiso en el sistema central SAAP, usted o el personal de RRHH debe editar el permiso y marcar la casilla **Tramitado**. Esto bloqueará definitivamente el registro para cualquier edición posterior del usuario y de las jefaturas.
*   **Corrección Administrativa:** Puede editar motivos, fechas o anular solicitudes directamente en caso de errores en la cadena de aprobación.

---

## 7. Desarrollador y Soporte
Este sistema fue desarrollado por:

👤 **Geovanny Escobar**
*   **LinkedIn:** [Geovanny Escobar](https://www.linkedin.com/in/geovannyescobar/)
