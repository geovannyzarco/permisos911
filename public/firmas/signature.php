<?php
require_once 'db.php';

$oni = isset($_GET['oni']) ? $_GET['oni'] : '';

if (empty($oni)) {
    header("Location: index.php?error=empty");
    exit;
}

// Buscar el empleado en la base de datos
$sql = "SELECT nombre, oni FROM empleados WHERE oni = ?";
$params = array($oni);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$empleado = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$empleado) {
    header("Location: index.php?error=notfound");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Captura de Firma | <?php echo htmlspecialchars($empleado['nombre']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Firma Digital</h1>
        <p class="subtitle">Por favor, firme dentro del recuadro blanco</p>

        <div id="alert" class="alert"></div>

        <div class="info-box">
            <div class="info-label">Nombre del Empleado</div>
            <div class="info-value"><?php echo htmlspecialchars($empleado['nombre']); ?></div>
            
            <div class="info-label">Número de ONI</div>
            <div class="info-value"><?php echo htmlspecialchars($empleado['oni']); ?></div>
        </div>

        <div class="signature-wrapper" id="signature-wrapper">
            <canvas id="signature-pad"></canvas>
        </div>

        <div class="actions">
            <button type="button" id="save-btn" class="btn btn-primary">
                <span>Guardar Firma</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            </button>
            
            <button type="button" id="clear-btn" class="btn btn-outline">
                <span>Limpiar Firma</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
            
            <a href="index.php" class="btn btn-outline" style="text-decoration: none; margin-top: 12px; display: flex;">
                <span>Volver al Inicio</span>
            </a>
        </div>
    </div>

    <!-- Cargar Signature Pad Localmente -->
    <script src="js/signature_pad.umd.js"></script>
    <script src="js/app.js"></script>
    <script>
        // Pasar variables de PHP a JS
        const employeeONI = "<?php echo $empleado['oni']; ?>";
    </script>
</body>
</html>
