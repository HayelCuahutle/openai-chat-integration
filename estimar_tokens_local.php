<?php
// estimar_tokens_local.php
// Esta versión NO consume tokens de OpenAI
// Usa extracción local de texto para estimar el costo

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 300);

require_once __DIR__ . '/vendor/autoload.php';
use Smalot\PdfParser\Parser;

header('Content-Type: application/json');

// Verificar si se recibió archivo
if (!isset($_FILES['pdf'])) {
    echo json_encode(['success' => false, 'error' => 'No se recibió archivo']);
    exit;
}

if ($_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
    $errorMsg = '';
    switch ($_FILES['pdf']['error']) {
        case UPLOAD_ERR_INI_SIZE:
            $errorMsg = 'El archivo excede el límite de upload_max_filesize';
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $errorMsg = 'El archivo excede el límite del formulario';
            break;
        case UPLOAD_ERR_PARTIAL:
            $errorMsg = 'El archivo fue subido parcialmente';
            break;
        case UPLOAD_ERR_NO_FILE:
            $errorMsg = 'No se subió ningún archivo';
            break;
        default:
            $errorMsg = 'Error al subir el archivo';
    }
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

$file = $_FILES['pdf'];
$filePath = $file['tmp_name'];

// Verificar tipo de archivo
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filePath);
finfo_close($finfo);

if ($mimeType !== 'application/pdf' && $mimeType !== 'application/octet-stream') {
    echo json_encode(['success' => false, 'error' => 'El archivo debe ser PDF. Tipo detectado: ' . $mimeType]);
    exit;
}

// Verificar tamaño (máximo 50MB)
$maxSize = 50 * 1024 * 1024;
if (filesize($filePath) > $maxSize) {
    echo json_encode(['success' => false, 'error' => 'El archivo es demasiado grande. Máximo 50MB']);
    exit;
}

try {
    // Extraer texto del PDF localmente (NO usa API)
    $parser = new Parser();
    $pdf = $parser->parseFile($filePath);
    
    // Obtener número de páginas
    $pages = $pdf->getPages();
    $totalPages = count($pages);
    
    // Extraer todo el texto
    $textoCompleto = $pdf->getText();
    
    // Si no se pudo extraer texto (PDF escaneado sin OCR)
    if (empty(trim($textoCompleto))) {
        // Fallback: estimar por tamaño de archivo
        $fileSizeMB = filesize($filePath) / 1024 / 1024;
        $totalPages = max(1, round($fileSizeMB * 10)); // Aprox 10 páginas por MB
        $textoCompleto = "";
        $esEscaneado = true;
    } else {
        $esEscaneado = false;
    }
    
    // Función para estimar tokens (1 token ≈ 4 caracteres en español)
    function estimarTokensLocal($texto) {
        return ceil(strlen($texto) / 4);
    }
    
    // Calcular tokens estimados
    if (!$esEscaneado && !empty($textoCompleto)) {
        $totalTokens = estimarTokensLocal($textoCompleto);
        $avgTokensPerPage = round($totalTokens / max(1, $totalPages));
    } else {
        // PDF escaneado: estimación por páginas
        $avgTokensPerPage = 800; // Promedio conservador
        $totalTokens = $totalPages * $avgTokensPerPage;
    }
    
    // Costos estimados (gpt-4o: ~$0.0025 por 1K tokens de entrada)
    $costPer1kTokens = 0.0025;
    $estimatedCost = ($totalTokens / 1000) * $costPer1kTokens;
    
    // Determinar recomendación
    $recomendacion = '';
    
    if ($esEscaneado) {
        if ($totalTokens > 50000) {
            $recomendacion = '⚠️⚠️⚠️ CRÍTICO - PDF escaneado de muchas páginas. El costo real podría ser mayor.';
        } elseif ($totalTokens > 20000) {
            $recomendacion = '⚠️ ALTO - PDF escaneado, considera si es necesario procesarlo.';
        } else {
            $recomendacion = '⚠️ MEDIO - PDF escaneado, el costo real puede variar.';
        }
    } else {
        if ($totalTokens > 50000) {
            $recomendacion = '⚠️⚠️⚠️ ALTO - El costo estimado es elevado.';
        } elseif ($totalTokens > 20000) {
            $recomendacion = '⚠️ MEDIO - El costo estimado es moderado.';
        } else {
            $recomendacion = '✅ BAJO - El costo estimado es razonable.';
        }
    }
    
    // Preparar respuesta
    $respuesta = [
        'success' => true,
        'total_pages' => $totalPages,
        'sample_pages' => $totalPages,
        'avg_tokens_per_page' => $avgTokensPerPage,
        'projected_tokens' => $totalTokens,
        'estimated_cost_usd' => round($estimatedCost, 4),
        'detailed_pages' => [],
        'cost_per_1k' => $costPer1kTokens,
        'recomendacion' => $recomendacion,
        'tipo_estimacion' => $esEscaneado ? '⚠️ Estimación por páginas (PDF escaneado)' : '✅ Extracción local de texto',
        'es_escaneado' => $esEscaneado,
        'message' => "Archivo PDF con {$totalPages} página(s). " . ($esEscaneado ? "PDF escaneado - estimación por páginas." : "Texto extraído localmente.")
    ];
    
    // Agregar preview del texto (primeros 200 caracteres) si existe
    if (!empty($textoCompleto)) {
        $respuesta['texto_preview'] = substr(trim($textoCompleto), 0, 200) . (strlen($textoCompleto) > 200 ? '...' : '');
    }
    
    echo json_encode($respuesta);
    
} catch (Exception $e) {
    // Si falla la librería, usar método de respaldo por tamaño de archivo
    $fileSizeMB = filesize($filePath) / 1024 / 1024;
    $totalPages = max(1, round($fileSizeMB * 8));
    $totalTokens = $totalPages * 800;
    $estimatedCost = ($totalTokens / 1000) * 0.0025;
    
    echo json_encode([
        'success' => true,
        'total_pages' => $totalPages,
        'sample_pages' => 0,
        'avg_tokens_per_page' => 800,
        'projected_tokens' => $totalTokens,
        'estimated_cost_usd' => round($estimatedCost, 4),
        'detailed_pages' => [],
        'cost_per_1k' => 0.0025,
        'recomendacion' => '⚠️ Estimación por tamaño de archivo (falló extracción)',
        'tipo_estimacion' => '⚠️ Estimación aproximada por tamaño',
        'es_escaneado' => true,
        'message' => "Error al extraer texto: " . $e->getMessage() . ". Estimación por tamaño del archivo."
    ]);
}
?>