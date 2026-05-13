<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$oni = isset($_POST['oni']) ? $_POST['oni'] : '';
$base64_image = isset($_POST['image']) ? $_POST['image'] : '';

if (empty($oni) || empty($base64_image)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    // 1. Guardar directamente la cadena Base64 en la base de datos
    // Esto es compatible con FilamentAutograph y generadores de PDF
    
    $sql = "UPDATE empleados SET firma = ? WHERE oni = ?";
    $params = array($base64_image, $oni); // $base64_image ya contiene el DataURL completo
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        throw new Exception(print_r(sqlsrv_errors(), true));
    }

    echo json_encode(['success' => true, 'message' => 'Firma guardada correctamente']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
