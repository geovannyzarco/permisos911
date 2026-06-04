# Manual de Usuario - Empleado Nivel 1
## Sistema de Gestión de Permisos

Este manual está diseñado para guiar a los empleados de **Nivel 1** en el uso del sistema de gestión de permisos. Aquí aprenderá a navegar por el sistema, realizar solicitudes personales y compensadas, comprender las validaciones en tiempo real, interactuar con el calendario y mantener su cuenta segura.

---

## 1. El Dashboard (Panel Principal)

Al iniciar sesión en el sistema, la primera pantalla que visualizará es el **Dashboard**. Este panel le ofrece una vista gráfica rápida del estado de sus solicitudes en el año actual:

*   **Mis permisos solicitados por mes (Año actual):** Un gráfico de barras que muestra la distribución mensual de sus permisos que ya han sido **Aprobados**.
*   **Mis permisos por tipo (Año actual):** Un gráfico de dona (*doughnut*) que desglosa visualmente el número de permisos aprobados que tiene, categorizados por su tipo (por ejemplo, Permiso Personal, Consulta Médica, Cumpleaños, etc.).

> [!NOTE]
> Estos gráficos se alimentan exclusivamente de los permisos que cuentan con la aprobación final del **Jefe de División** (Estado: *APROBADO*). Las solicitudes pendientes o anuladas no sumarán a estas estadísticas.

---

## 2. Creación de Permisos Generales

Para iniciar una nueva solicitud de permiso, diríjase al menú lateral izquierdo y haga clic en **Mis Permisos** (bajo la sección *Solicitudes*). Luego, pulse el botón **Crear**.

### Campos del Formulario:
Al crear cualquier tipo de permiso, deberá completar los siguientes campos básicos:
1.  **Fecha de Creación:** Se completa automáticamente con la fecha y hora actual (es de solo lectura).
2.  **Tipo de Permiso:** Un menú desplegable para seleccionar la categoría de su permiso (ver listado de otros permisos en la Sección 5).
3.  **Desde:** Fecha y hora inicial del permiso.
4.  **Hasta:** Fecha y hora final del permiso.
5.  **Motivo:** Explicación obligatoria y detallada del porqué de la solicitud (máximo 255 caracteres).
6.  **Adjunto:** Espacio para subir un archivo justificante (PDF, imagen, etc.) si es necesario. El tamaño máximo del archivo es de **10 MB**.

### Flujo de Aprobación por Defecto:
Una vez que guarda su solicitud, el sistema le asigna automáticamente el estado de **Pendiente** en los tres niveles de revisión requeridos:
*   **VB (Visto Bueno):** Pendiente (revisión de su supervisor inmediato).
*   **Aprobación Jefatura:** Pendiente (revisión del Jefe de su Unidad/Departamento).
*   **Aprobación Jefe de División:** Pendiente (revisión del Jefe de su División).

### Reglas de Edición y Eliminación:
> [!WARNING]
> **Bloqueo de Solicitudes:** Usted únicamente podrá editar o eliminar una solicitud si:
> 1. Todos sus estados de aprobación (VB, Jefatura y División) se encuentran en **PENDIENTE** (ID de estado 4).
> 2. El registro no ha sido marcado como **Tramitado** por el departamento de Recursos Humanos/SAAP.
> 
> Si alguno de los jefes otorga su visto bueno o aprobación, o si es rechazado/anulado, el formulario se bloqueará automáticamente y no podrá modificar ningún dato ni adjunto.

---

## 3. Permiso Personal (ID 1) y sus Validaciones

El **Permiso Personal** es aquel que descuenta tiempo directamente del saldo de horas anuales asignadas a su horario laboral.

### Panel Informativo en Tiempo Real:
Al seleccionar el tipo **PERMISO PERSONAL**, se activará en la parte superior el panel de **Información del empleado**. Este panel le muestra:
*   **Horas asignadas:** Las horas personales anuales correspondientes a su contrato y horario.
*   **Horas utilizadas:** El acumulado de horas de permisos personales ya aprobados en el año en curso.
*   **Horas disponibles:** Su saldo actual neto para solicitar nuevos permisos.

### Validación Crítica:
*   **Cálculo Automático:** Al definir las fechas y horas en los campos **Desde** y **Hasta**, el sistema calculará de forma automática la duración solicitada en horas.
*   **Control de Saldo:** El sistema no le permitirá guardar el formulario si la duración del permiso solicitado supera su saldo de **Horas disponibles**. En caso de excederse, visualizará el siguiente mensaje de error debajo del campo *Hasta*:
    > ❌ *El tiempo solicitado excede el saldo de horas personales disponibles según tu horario.*

---

## 4. Permisos por Tiempo Compensatorio (ID 2)

El **Permiso por Tiempo Compensatorio** se utiliza para solicitar tiempo libre a cambio de horas extras que haya acumulado y justificado previamente.

Al seleccionar el tipo **POR TIEMPO COMPENSATORIO**, se desplegará una sección llamada **Periodos Compensados a Utilizar** (una tabla repetible donde deberá detallar cada periodo trabajado que respalda su solicitud).

### Campos de la Tabla Compensados:
Para cada periodo que agregue, debe especificar:
*   **Desde / Hasta:** Fecha y hora en las que realizó el tiempo compensatorio (horas extras).
*   **Justificación:** Explicación del trabajo realizado durante esas horas extras.
*   **Adjunto:** Documento o reporte firmado que valide la realización de dicho tiempo compensatorio (máximo 10 MB).

### Validaciones Especiales de Compensatorios:

> [!IMPORTANT]
> Para poder guardar exitosamente una solicitud de tiempo compensatorio, se deben cumplir obligatoriamente dos reglas del sistema:
>
> 1. **Coincidencia Exacta de Horas:**
>    La suma total del tiempo detallado en los periodos compensatorios ingresados en la tabla debe ser **exactamente igual** a la duración del permiso principal solicitada arriba (campos generales *Desde* y *Hasta*).
>    *Ejemplo:* Si arriba pide un permiso de 8 horas (de 08:00 a 16:00 del mismo día), abajo en la tabla de compensados debe registrar periodos que sumen exactamente 8 horas de tiempo compensatorio. Si no coinciden, el sistema mostrará un error y bloqueará el guardado:
>    > ❌ *La suma de horas de los periodos compensados (X hrs) debe ser exactamente igual a las horas solicitadas en el permiso (Y hrs).*
>
> 2. **Límite de Antigüedad (6 meses):**
>    Cada periodo compensatorio registrado debe haber ocurrido dentro de los últimos **6 meses** a contar desde la fecha actual. Si intenta ingresar un periodo de tiempo compensatorio acumulado con más de 6 meses de antigüedad, el sistema arrojará un error:
>    > ❌ *El periodo compensado con fecha inicial DD/MM/AAAA excede el límite de 6 meses de antigüedad permitido.*

---

## 5. Catálogo de Otros Permisos

Además del Permiso Personal y Compensatorio, el sistema ofrece una variedad de opciones para justificar ausencias. Estos son los **17 tipos de permisos** disponibles en el sistema:

| ID | Nombre del Permiso | Notas de Uso General |
|---|---|---|
| **1** | **PERMISO PERSONAL** | Descuenta de su saldo anual de horas personales. |
| **2** | **POR TIEMPO COMPENSATORIO** | Requiere justificar las horas extras equivalentes (antigüedad < 6 meses). |
| **3** | **CUMPLEAÑOS** | Permiso especial por el día de su cumpleaños. |
| **4** | **LICENCIA DE 8 DIAS POR MATERNIDAD** | Licencia por maternidad en los términos institucionales. |
| **5** | **DELEGACIONES DEPORTIVAS, CULT. O CIENTIF.** | Justificado mediante invitación u oficio de la delegación. |
| **6** | **TRATAMIENTO EN EL EXTRANJERO** | Requiere dictamen médico oficial y comprobantes. |
| **7** | **CONSULTA MEDICA** | Justificante médico de asistencia (ISSS, hospital o clínica). |
| **8** | **ENFERMEDAD O DUELO** | Por fallecimiento de familiares cercanos o enfermedad propia. |
| **9** | **ESTUDIOS/HORAS SOCIALES** | Para asistencia a clases, exámenes u horas sociales autorizadas. |
| **10** | **DILIGENCIAS JUDICIALES/EXTRAJUDICIALES** | Citaciones de juzgados, fiscalía u otras entidades legales. |
| **11** | **FALTA DE MARCACION** | Justificación ante un olvido o falla al marcar entrada/salida. |
| **12** | **LICENCIA POR ENFERMEDAD SIN INCAPACIDAD** | Ausencia por enfermedad que no genera incapacidad del ISSS (días cortos). |
| **13** | **MISION OFICIAL** | Salidas asignadas en cumplimiento de sus funciones laborales. |
| **14** | **PATERNIDAD** | Días concedidos por el nacimiento de un hijo. |
| **15** | **POR LACTANCIA** | Tiempo diario asignado para lactancia materna. |
| **16** | **POR IMPARTIR CLASES** | Autorización especial para docencia en horarios laborales. |
| **17** | **MATRIMONIO** | Licencia concedida por matrimonio civil/religioso. |

---

## 6. Validaciones de Cupo (Disponibilidad de Grupo)

Para garantizar la continuidad operativa de los departamentos, el sistema cuenta con un límite diario de permisos por grupo de trabajo.

### ¿Cómo funciona el límite de grupo?
Cada empleado pertenece a un grupo de trabajo. Cada grupo tiene asignada una cantidad límite de **Permisos Diarios** permitidos (por ejemplo, un límite de 2 personas ausentes por día).

### Visualización y Alertas en el Formulario:
Cuando esté creando una solicitud y seleccione el rango de fechas (*Desde* y *Hasta*), se activarán dos secciones visuales automáticas:

1.  **Permisos en Conflicto (Bloqueos de Cupo):**
    Si en alguna de las fechas que usted solicita ya se alcanzó el límite máximo de personas con permiso en su grupo, aparecerá una alerta de color rojo:
    > ⚠️ *El día DD/MM/AAAA está bloqueado (Límite: X permisos, Ocupados: Y):*
    > *   *Permiso #123: Nombre del Empleado (Desde - Hasta)*
    
    Si intenta guardar la solicitud estando el cupo lleno, la aplicación detendrá el proceso y mostrará el error:
    > ❌ *No se puede registrar el permiso. El día DD/MM/AAAA excede el límite de X permisos diarios permitidos para el grupo 'Nombre_Grupo'.*

2.  **Disponibilidad del Grupo:**
    Un panel de texto dinámico que le lista día a día el estado del cupo de su grupo en el rango seleccionado:
    *   `🟢 Disponible`: Hay cupos libres para solicitar permiso ese día.
    *   `🔴 Lleno`: El cupo diario está agotado y el día se encuentra bloqueado.

---

## 7. Calendario de Permisos

El **Calendario de Permisos** es una herramienta interactiva para consultar las ausencias programadas. Puede acceder a él a través del menú lateral: **Consultas -> Calendario de Permisos**.

### Restricciones de Visualización (Rol Nivel 1):
Por motivos de privacidad y según la estructura jerárquica:
*   **Si pertenece a un Grupo de Trabajo activo:** El calendario se filtrará automáticamente y **solo** le permitirá visualizar los permisos de sus compañeros de su **propio grupo**. No podrá ver permisos de otras unidades o divisiones.
*   **Si no tiene un Grupo de Trabajo asignado (o pertenece al grupo especial "Sin Grupo" - ID 12):** El calendario le mostrará únicamente los permisos de los miembros de su **propio departamento o Unidad**.

### Uso del Calendario:
*   **Navegación:** Utilice los botones de navegación en la parte superior para avanzar o retroceder de mes, o presionar *Hoy* para volver al mes en curso.
*   **Cuadrícula de Días:** Los días mostrarán bloques de colores que indican el grupo o unidad al que pertenece la ausencia y el número de personas de permiso.
*   **Modal de Detalles:** Al hacer clic sobre cualquier día del calendario, se abrirá una ventana emergente (*Modal*) mostrando la información detallada de los permisos de esa fecha dentro de su ámbito de visualización:
    *   Fotografía y Nombre del empleado.
    *   ONI del empleado.
    *   Tipo de permiso y motivo.
    *   Rango de horas y duración.
    *   Estado de las tres aprobaciones (VB, Jefatura y División).
    *   **Descarga de PDF:** Si el permiso ya está completamente aprobado (Aprobado por el Jefe de División), aparecerá un botón que le permitirá **descargar el PDF del permiso** firmado oficialmente para su registro personal.

---

## 8. Cambio de Contraseña y Datos de Perfil

Es su responsabilidad mantener sus credenciales de acceso seguras. Puede actualizar su contraseña en cualquier momento siguiendo estos pasos:

1.  **Abrir el Menú de Usuario:** Haga clic en su nombre o avatar que se encuentra en la esquina (según el diseño de la barra, típicamente superior derecha o inferior izquierda).
2.  **Seleccionar "Mi Perfil":** Esto le llevará a la página oficial de edición de su perfil.
3.  **Actualizar Contraseña:**
    *   Escriba su nueva contraseña en el campo **Contraseña** (*Password*).
    *   Confirme la contraseña en el campo correspondiente.
    *   *(Opcional)* También puede actualizar su dirección de correo electrónico si es necesario.
4.  **Guardar Cambios:** Pulse el botón **Guardar**. Sus nuevas credenciales estarán activas de inmediato en el próximo inicio de sesión.
