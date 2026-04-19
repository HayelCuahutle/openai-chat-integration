<?php
// guardar_delimitacion.php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$respuesta = strtolower(trim($input['respuesta'] ?? ''));

if (!$respuesta) {
    echo json_encode(['success' => false, 'error' => 'No se recibió respuesta']);
    exit;
}

// Verificar que hay un modo activo que requiere delimitación
if (!isset($_SESSION['modo_actual']) || !isset($_SESSION['requiere_delimitacion'])) {
    echo json_encode(['success' => false, 'error' => 'No hay un modo activo que requiera delimitación']);
    exit;
}

$modo = $_SESSION['modo_actual'];
$tipo = $_SESSION['tipo_delimitacion'] ?? 'total_parcial';
$error = null;
$delimitacion_valida = false;

if ($tipo === 'total_parcial') {
    // Validar "total" o "parcial"
    if ($respuesta === 'total' || $respuesta === 'parcial') {
        $delimitacion_valida = true;
        $_SESSION['delimitacion'] = $respuesta;
        $_SESSION['fase_actual'] = 2;
        $mensaje = '✅ Delimitación guardada. Ahora puede subir el archivo.';
    } else {
        $error = "Debe indicar 'total' o 'parcial'";
    }
} 
elseif ($tipo === 'rangos') {
    // Validar formato "parcial, demanda:1-15, contestacion:16-30"
    if ($respuesta === 'total') {
        $delimitacion_valida = true;
        $_SESSION['delimitacion'] = 'total';
        $_SESSION['fase_actual'] = 2;
        $mensaje = '✅ Delimitación guardada. Ahora puede subir el archivo.';
    }
    elseif (preg_match('/parcial.*?demanda:\s*(\d+)-(\d+).*?contestacion:\s*(\d+)-(\d+)/i', $respuesta, $matches)) {
        $delimitacion_valida = true;
        $_SESSION['delimitacion'] = $respuesta;
        $_SESSION['fase_actual'] = 2;
        $mensaje = '✅ Delimitación guardada. Ahora puede subir el archivo.';
    } else {
        $error = "Formato inválido. Use: 'parcial, demanda:1-15, contestacion:16-30' o 'total'";
    }
}

if ($delimitacion_valida) {
    echo json_encode([
        'success' => true, 
        'mensaje' => $mensaje
    ]);
} else {
    echo json_encode(['success' => false, 'error' => $error]);
}
?>