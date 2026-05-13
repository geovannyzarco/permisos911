<?php
require_once 'db.php';

$error = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'notfound') {
        $error = 'El ONI ingresado no existe en la base de datos.';
    } else if ($_GET['error'] == 'empty') {
        $error = 'Por favor ingrese un ONI.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Búsqueda de Empleado | Sistema de Firmas</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Búsqueda</h1>
        <p class="subtitle">Ingrese el ONI del empleado para continuar</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="signature.php" method="GET">
            <div class="form-group">
                <label for="oni">Número de ONI</label>
                <input type="text" name="oni" id="oni" placeholder="Ej: A12345" required autofocus>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <span>Buscar Empleado</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
        </form>
    </div>
</body>
</html>
