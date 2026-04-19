<?php
// activar_modo.php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'modos_config.php';

function enviarJSON($datos) {
    if (ob_get_length()) ob_clean();
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['comando'])) {
        enviarJSON(['success' => false, 'error' => 'Comando no recibido']);
    }
    
    $comando = strtolower(trim($input['comando']));
    
    global $config_modos;
    
    // Textos fijos
    define('NOTA_RESPONSABILIDAD', '"Mextopo es un asistente de inteligencia artificial utilizado como herramienta de apoyo. El contenido generado puede contener errores u omisiones. La revisión final y la responsabilidad del dictamen corresponden exclusivamente al perito firmante."');
    define('AVISO_PRIVACIDAD', "Los documentos que se cargan en esta sesión son utilizados únicamente para el análisis solicitado por el usuario. No se hacen públicos ni son visibles para otros usuarios o despachos. Cada sesión es independiente. El manejo interno de los archivos es responsabilidad del operador. Para mayor tranquilidad, se recomienda eliminar la conversación una vez concluido el análisis.");
    define('TERMINOS_USO', "El uso de MXTopo Dictámenes implica la aceptación de que esta herramienta es únicamente un sistema de apoyo documental. No sustituye criterio profesional ni responsabilidad del perito. No garantiza resultados técnicos o jurídicos. No reemplaza revisión humana. El acceso es personal o por despacho y no debe compartirse sin autorización.");
    
    // Construir mapa de alias
    $alias_to_modo = [];
    foreach ($config_modos as $modo_key => $modo) {
        foreach ($modo['activacion'] as $activacion) {
            $alias_to_modo[strtolower(trim($activacion))] = $modo_key;
        }
        foreach ($modo['alias'] as $alias) {
            $alias_to_modo[strtolower(trim('modo ' . $alias))] = $modo_key;
            $alias_to_modo[strtolower(trim('activar modo ' . $alias))] = $modo_key;
        }
    }
    
    // Detectar modo
    function detectarModo($comando, $alias_to_modo) {
        $comando = strtolower(trim($comando));
        
        if (isset($alias_to_modo[$comando])) {
            return $alias_to_modo[$comando];
        }
        
        if (preg_match('/^(?:activar\s+)?modo\s+(.+)$/', $comando, $matches)) {
            $modo_buscado = strtolower(trim($matches[1]));
            $modo_buscado = str_replace(['0', '1', '2', '3'], ['cero', 'uno', 'dos', 'tres'], $modo_buscado);
            
            $clave_buscar = 'modo ' . $modo_buscado;
            if (isset($alias_to_modo[$clave_buscar])) {
                return $alias_to_modo[$clave_buscar];
            }
        }
        
        return null;
    }
    
    $modo_detectado = detectarModo($comando, $alias_to_modo);
    
    if (!$modo_detectado) {
        $modos_lista = "";
        foreach ($config_modos as $key => $modo) {
            if ($key != 'cero') {
                $modos_lista .= "| `Activar modo {$modo['nombre']}` | {$modo['descripcion']} | " . implode(', ', $modo['alias']) . " |\n";
            }
        }
        
        enviarJSON([
            'success' => false,
            'accion' => 'modo_no_reconocido',
            'mensaje' => "**Modo no reconocido**\n\n**Modos disponibles:**\n\n" .
                         "| Modo | Descripción | Alias |\n" .
                         "|------|-------------|-------|\n" .
                         "| `Activar modo cero` | Información general del sistema | 0, menu, ayuda |\n" .
                         $modos_lista
        ]);
    }
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['modo_actual'] = $modo_detectado;
    $_SESSION['fase_actual'] = 1;
    $_SESSION['documentos'] = [];
    $_SESSION['delimitacion'] = null;
    
    switch ($modo_detectado) {
        case 'cero':
            $modos_disponibles = "";
            foreach ($config_modos as $key => $modo) {
                if ($key != 'cero') {
                    $modos_disponibles .= "| **{$modo['nombre']}** | {$modo['descripcion']} |\n";
                }
            }
            
            $respuesta = "**📋 MODO CERO - INFORMACIÓN DEL SISTEMA**\n\n" .
                         "**1) MODOS DISPONIBLES**\n\n" .
                         "| Modo | Descripción |\n" .
                         "|------|-------------|\n" .
                         $modos_disponibles .
                         "\n**2) USO DEL SISTEMA**\n\n" .
                         "**REGLAS DE USO:**\n" .
                         "• Para platicar o preguntar, escribe normalmente.\n" .
                         "• Para trabajar documentos, SIEMPRE activa un modo.\n\n" .
                         "**USO BÁSICO DEL SISTEMA:**\n" .
                         "1. Activa el modo que necesites\n" .
                         "2. Sigue las instrucciones de Fase 1 (subir documentos / definir alcance)\n" .
                         "3. Una vez entregados los archivos, Mextopo ejecutará el análisis automáticamente\n\n" .
                         "**3) AVISO DE PRIVACIDAD OPERATIVO**\n" . AVISO_PRIVACIDAD . "\n\n" .
                         "**4) ACEPTACIÓN DE TÉRMINOS DE USO**\n" . TERMINOS_USO . "\n\n" .
                         "**5) NOTA DE RESPONSABILIDAD**\n" . NOTA_RESPONSABILIDAD;
            
            enviarJSON([
                'success' => true,
                'modo' => 'cero',
                'fase' => 1,
                'requiere_delimitacion' => false,
                'mensaje' => $respuesta
            ]);
            break;
            
        case 'demanda_contestacion':
            $_SESSION['requiere_delimitacion'] = true;
            $_SESSION['tipo_delimitacion'] = 'rangos';
            enviarJSON([
                'success' => true,
                'modo' => 'demanda_contestacion',
                'fase' => 1,
                'requiere_delimitacion' => true,
                'tipo_delimitacion' => 'rangos',
                'mensaje' => "**⚖️ MODO DEMANDA-CONTESTACIÓN - FASE 1**\n\n" .
                            "**📋 SOLICITUD DE DELIMITACIÓN**\n\n" .
                            "Suba el/los archivo(s) correspondientes.\n\n" .
                            "**IMPORTANTE:** Antes de subir el archivo, debe indicar:\n" .
                            "• Si el análisis es **total** o **parcial**\n" .
                            "• Si es parcial, especifique: **demanda:páginas, contestacion:páginas**\n\n" .
                            "_Ejemplo: parcial, demanda:1-15, contestacion:16-30_\n\n" .
                            "Escriba su respuesta en el chat."
            ]);
            break;
            
        case 'linea_tiempo':
            $_SESSION['requiere_delimitacion'] = true;
            $_SESSION['tipo_delimitacion'] = 'total_parcial';
            enviarJSON([
                'success' => true,
                'modo' => 'linea_tiempo',
                'fase' => 1,
                'requiere_delimitacion' => true,
                'tipo_delimitacion' => 'total_parcial',
                'mensaje' => "**📅 MODO LÍNEA DE TIEMPO - FASE 1**\n\n" .
                            "**📋 SOLICITUD DE DELIMITACIÓN**\n\n" .
                            "Suba el/los documento(s) o pegue el texto a analizar.\n\n" .
                            "**IMPORTANTE:** Antes de subir el archivo, indique si el análisis será **total** o **parcial**.\n\n" .
                            "Escriba su respuesta en el chat."
            ]);
            break;
            
        case 'revision_editorial':
            $_SESSION['requiere_delimitacion'] = true;
            $_SESSION['tipo_delimitacion'] = 'total_parcial';
            enviarJSON([
                'success' => true,
                'modo' => 'revision_editorial',
                'fase' => 1,
                'requiere_delimitacion' => true,
                'tipo_delimitacion' => 'total_parcial',
                'mensaje' => "**📝 MODO REVISIÓN EDITORIAL - FASE 1**\n\n" .
                            "**📋 SOLICITUD DE DOCUMENTO**\n\n" .
                            "Suba el dictamen a revisar.\n\n" .
                            "**IMPORTANTE:** Antes de subir el archivo, indique si el análisis será **total** o **parcial**.\n\n" .
                            "Escriba su respuesta en el chat."
            ]);
            break;
            
        case 'revision_tecnica':
            $_SESSION['requiere_delimitacion'] = true;
            $_SESSION['tipo_delimitacion'] = 'total_parcial';
            enviarJSON([
                'success' => true,
                'modo' => 'revision_tecnica',
                'fase' => 1,
                'requiere_delimitacion' => true,
                'tipo_delimitacion' => 'total_parcial',
                'mensaje' => "**🦁 MODO REVISIÓN TÉCNICA (MODO BESTIA) - FASE 1**\n\n" .
                            "**📋 SOLICITUD DE DICTAMEN**\n\n" .
                            "Suba el dictamen a analizar.\n\n" .
                            "**IMPORTANTE:** Antes de subir el archivo, indique si el análisis será **total** o **parcial**.\n\n" .
                            "Escriba su respuesta en el chat."
            ]);
            break;
            
        case 'revision_informe':
            $_SESSION['requiere_delimitacion'] = false;
            enviarJSON([
                'success' => true,
                'modo' => 'revision_informe',
                'fase' => 1,
                'requiere_delimitacion' => false,
                'mensaje' => "**📊 MODO REVISIÓN DE INFORME - FASE 1**\n\n" .
                            "**📋 SOLICITUD DE DOCUMENTO**\n\n" .
                            "Suba el informe técnico o memoria descriptiva a revisar.\n\n" .
                            "El análisis será **integral automáticamente** (no requiere delimitación adicional)."
            ]);
            break;
            
        default:
            enviarJSON([
                'success' => false,
                'error' => 'Modo no implementado'
            ]);
    }
    
} catch (Exception $e) {
    enviarJSON([
        'success' => false,
        'error' => 'Error interno: ' . $e->getMessage()
    ]);
}
?>