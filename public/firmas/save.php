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
    // 1. Procesar la imagen base64
    // Remover el encabezado data:image/png;base64,
    $image_parts = explode(";base64,", $base64_image);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);

    // 2. Definir ruta y nombre del archivo
    $folderPath = "firmas/";
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }
    
    $fileName = $oni . ".png";
    $filePath = $folderPath . $fileName;

    // 3. Guardar el archivo físicamente
    file_put_contents($filePath, $image_base64);

    // 4. Actualizar la base de datos
    // Valor a guardar: firma/oni.png (según requerimiento exacto)
    $dbPathValue = "firma/" . $fileName;
    
    $sql = "UPDATE empleados SET firma = ? WHERE oni = ?";
    $params = array($dbPathValue, $oni);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        throw new Exception(print_r(sqlsrv_errors(), true));
    }

    echo json_encode(['success' => true, 'message' => 'Firma guardada correctamente']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
