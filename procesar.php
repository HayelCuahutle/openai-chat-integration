<?php
// procesar.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

require 'vendor/autoload.php';
require 'modos_config.php';

$modos_includes = [
    'includes/ModoDemandaContestacion.php',
    'includes/ModoLineaTiempo.php',
    'includes/ModoRevisionEditorial.php',
    'includes/ModoRevisionTecnica.php',
    'includes/ModoRevisionInforme.php'
];

foreach ($modos_includes as $include) {
    if (file_exists($include)) {
        require_once $include;
    }
}

use OpenAI;
use Spatie\PdfToImage\Pdf;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (ob_get_length()) ob_clean();

header('Content-Type: application/json');

function enviarJSON($datos) {
    if (ob_get_length()) ob_clean();
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function estimarTokensTexto($texto) {
    return ceil(strlen($texto) / 4);
}

try {
    if (!isset($_FILES['pdf'])) {
        enviarJSON(["success" => false, "error" => "No se recibió el archivo"]);
    }

    if ($_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => "El archivo excede el límite de upload_max_filesize",
            UPLOAD_ERR_FORM_SIZE => "El archivo excede el límite del formulario",
            UPLOAD_ERR_PARTIAL => "El archivo fue subido parcialmente",
            UPLOAD_ERR_NO_FILE => "No se subió ningún archivo",
            UPLOAD_ERR_NO_TMP_DIR => "Falta la carpeta temporal",
            UPLOAD_ERR_CANT_WRITE => "No se pudo escribir el archivo en disco",
            UPLOAD_ERR_EXTENSION => "Una extensión detuvo la subida"
        ];
        
        $errorMsg = $uploadErrors[$_FILES['pdf']['error']] ?? "Error de subida: " . $_FILES['pdf']['error'];
        enviarJSON(["success" => false, "error" => $errorMsg]);
    }

    $modo_actual = $_SESSION['modo_actual'] ?? null;
    if (!$modo_actual) {
        enviarJSON([
            "success" => false, 
            "error" => "Debes activar un modo primero. Escribe 'Activar modo cero' para ver los modos disponibles."
        ]);
    }

    $modos_con_delimitacion = ['demanda_contestacion', 'linea_tiempo', 'revision_editorial', 'revision_tecnica'];
    if (in_array($modo_actual, $modos_con_delimitacion)) {
        if (!isset($_SESSION['delimitacion'])) {
            enviarJSON([
                "success" => false,
                "error" => "Este modo requiere que primero indiques si el análisis es total o parcial. Escribe tu respuesta en el chat."
            ]);
        }
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['pdf']['tmp_name']);
    finfo_close($finfo);

    if ($mimeType !== 'application/pdf' && $mimeType !== 'application/octet-stream') {
        enviarJSON([
            "success" => false,
            "error" => "El archivo debe ser PDF. Tipo detectado: " . $mimeType
        ]);
    }

    $maxSize = 50 * 1024 * 1024;
    if ($_FILES['pdf']['size'] > $maxSize) {
        enviarJSON([
            "success" => false,
            "error" => "El archivo es demasiado grande. Máximo 50MB"
        ]);
    }

    $config = [];
    if (file_exists('config.php')) {
        $config = require 'config.php';
    } else {
        enviarJSON([
            "success" => false,
            "error" => "Error de configuración: No se encuentra el archivo config.php"
        ]);
    }

    if (empty($config['openai_key'])) {
        enviarJSON([
            "success" => false,
            "error" => "Error de configuración: API key de OpenAI no configurada"
        ]);
    }

    $uploadDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
    $fileName = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES["pdf"]["name"]);
    $filePath = $uploadDir . $fileName;
    
    if (!move_uploaded_file($_FILES["pdf"]["tmp_name"], $filePath)) {
        throw new Exception("No se pudo guardar el archivo en " . $uploadDir);
    }

    if (!file_exists($filePath) || filesize($filePath) == 0) {
        throw new Exception("El archivo no se guardó correctamente");
    }

    try {
        $pdf = new Pdf($filePath);
        $pdf->setOutputFormat('jpg')
            ->setCompressionQuality(70)
            ->setResolution(100);
    } catch (Exception $e) {
        throw new Exception("Error al procesar el PDF: " . $e->getMessage());
    }
    
    $pages = $pdf->getNumberOfPages();
    
    $maxPages = 50;
    $pagesToProcess = min($pages, $maxPages);
    
    $limitWarning = "";
    if ($pages > $maxPages) {
        $limitWarning = "⚠️ *El PDF tiene {$pages} páginas. Se analizaron las primeras {$maxPages} para mantener el rendimiento.*\n\n";
    }

    $textoCompleto = "";
    $processedPages = 0;
    
    for ($page = 1; $page <= $pagesToProcess; $page++) {
        try {
            $imagePath = $uploadDir . "page_{$page}_" . time() . ".jpg";
            $pdf->setPage($page)->saveImage($imagePath);
            
            if (!file_exists($imagePath)) {
                continue;
            }
            
            $client = OpenAI::client($config['openai_key']);
            $response = $client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Extrae todo el texto de esta página del PDF. Devuelve SOLO el texto extraído, sin comentarios.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:image/jpeg;base64," . base64_encode(file_get_contents($imagePath))
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 4000
            ]);
            
            $textoCompleto .= "=== PÁGINA " . $page . " ===\n";
            $textoCompleto .= $response->choices[0]->message->content . "\n\n";
            $processedPages++;
            
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            
        } catch (Exception $e) {
            $textoCompleto .= "=== PÁGINA " . $page . " ===\n[Error al procesar página: " . $e->getMessage() . "]\n\n";
        }
    }

    if (file_exists($filePath)) {
        unlink($filePath);
    }

    if (empty(trim($textoCompleto))) {
        throw new Exception("No se pudo extraer texto del PDF");
    }

    // Validación de tokens
    $totalTokens = estimarTokensTexto($textoCompleto);
    $maxTokens = 100000;
    
    if ($totalTokens > $maxTokens) {
        enviarJSON([
            "success" => false,
            "error" => "El documento extraído tiene aproximadamente {$totalTokens} tokens, lo que supera el límite de {$maxTokens}. Por favor, reduce el tamaño del documento."
        ]);
    }

    $delimitacion = $_SESSION['delimitacion'] ?? null;
    $resultado = "";

    switch ($modo_actual) {
        case 'demanda_contestacion':
            if (class_exists('ModoDemandaContestacion')) {
                $modo = new ModoDemandaContestacion($textoCompleto, $delimitacion);
                $resultado = $modo->generarAnalisis();
            } else {
                $resultado = "**Error:** Clase ModoDemandaContestacion no encontrada";
            }
            break;
            
        case 'linea_tiempo':
            if (class_exists('ModoLineaTiempo')) {
                $modo = new ModoLineaTiempo($textoCompleto);
                $resultado = $modo->generarAnalisis();
            } else {
                $resultado = "**Error:** Clase ModoLineaTiempo no encontrada";
            }
            break;
            
        case 'revision_editorial':
            if (class_exists('ModoRevisionEditorial')) {
                $modo = new ModoRevisionEditorial($textoCompleto);
                $resultado = $modo->generarAnalisis();
            } else {
                $resultado = "**Error:** Clase ModoRevisionEditorial no encontrada";
            }
            break;
            
        case 'revision_tecnica':
            if (class_exists('ModoRevisionTecnica')) {
                $modo = new ModoRevisionTecnica($textoCompleto);
                $resultado = $modo->generarAnalisis();
            } else {
                $resultado = "**Error:** Clase ModoRevisionTecnica no encontrada";
            }
            break;
            
        case 'revision_informe':
            if (class_exists('ModoRevisionInforme')) {
                $modo = new ModoRevisionInforme($textoCompleto);
                $resultado = $modo->generarAnalisis();
            } else {
                $resultado = "**Error:** Clase ModoRevisionInforme no encontrada";
            }
            break;
            
        default:
            $resultado = "**Modo no reconocido:** " . $modo_actual;
    }
    
    $infoTokens = "\n\n---\n**📊 Información de uso:**\n- Tokens estimados del texto extraído: " . number_format($totalTokens) . "\n";
    $resultado = $limitWarning . $resultado . $infoTokens;
    
    unset($_SESSION['delimitacion']);
    unset($_SESSION['requiere_delimitacion']);
    unset($_SESSION['tipo_delimitacion']);

    enviarJSON([
        "success" => true,
        "resumen" => $resultado,
        "paginas" => $processedPages,
        "total_paginas" => $pages,
        "modo" => $modo_actual,
        "tokens_estimados" => $totalTokens
    ]);

} catch (Exception $e) {
    if (isset($filePath) && file_exists($filePath)) {
        unlink($filePath);
    }
    
    enviarJSON([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>