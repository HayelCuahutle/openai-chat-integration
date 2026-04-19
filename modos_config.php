<?php
// modos_config.php
$config_modos = [
    'cero' => [
        'nombre' => 'Modo Cero',
        'activacion' => ['activar modo cero', 'modo 0'],
        'descripcion' => 'Información general del sistema y modos disponibles',
        'alias' => ['0', 'menu', 'ayuda', 'inicio']
    ],
    
    'demanda_contestacion' => [
        'nombre' => 'Modo Demanda-Contestación',
        'activacion' => ['activar modo demanda-contestación', 'activar modo demanda', 'modo demanda'],
        'descripcion' => 'Comparación estructural entre demanda y contestación',
        'alias' => ['demanda', 'contestacion', 'dc']
    ],
    
    'linea_tiempo' => [
        'nombre' => 'Modo Línea de Tiempo',
        'activacion' => ['activar modo línea de tiempo', 'activar modo timeline', 'modo timeline'],
        'descripcion' => 'Ordenamiento cronológico de eventos y documentos',
        'alias' => ['timeline', 'cronologia', 'tiempo']
    ],
    
    'revision_editorial' => [
        'nombre' => 'Modo Revisión Editorial',
        'activacion' => ['activar modo revisión editorial', 'activar modo revisión', 'modo revision'],
        'descripcion' => 'Detección de riesgos e inconsistencias editoriales',
        'alias' => ['editorial', 'revision']
    ],
    
    'revision_tecnica' => [
        'nombre' => 'Modo Revisión Técnica',
        'activacion' => ['activar modo revisión técnica', 'activar modo bestia', 'modo bestia'],
        'descripcion' => 'Evaluación crítica estructural de dictámenes',
        'alias' => ['bestia', 'tecnica']
    ],
    
    'revision_informe' => [
        'nombre' => 'Modo Revisión de Informe',
        'activacion' => ['activar modo revisión de informe', 'activar modo informe técnico', 'activar modo memoria descriptiva'],
        'descripcion' => 'Revisión de informes técnicos y memorias descriptivas',
        'alias' => ['informe', 'memoria', 'memoria descriptiva', 'informe técnico']
    ]
];
?>