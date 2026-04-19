<?php
// list_models.php - Listar modelos disponibles de Gemini
error_reporting(E_ALL);
ini_set('display_errors', 1);

$config = require 'config.php';
$apiKey = $config['gemini_key'];

echo "<h2>Modelos disponibles en Google Gemini</h2>";

// Usar la API v1 (no v1beta) para listar modelos
$url = "https://generativelanguage.googleapis.com/v1/models?key={$apiKey}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}<br><br>";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    
    if (isset($data['models']) && is_array($data['models'])) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Nombre del Modelo</th><th>Nombre Mostrado</th><th>Versión</th><th>Soporta GenerateContent</th></tr>";
        
        foreach ($data['models'] as $model) {
            $name = $model['name'] ?? 'N/A';
            $displayName = $model['displayName'] ?? 'N/A';
            $version = $model['version'] ?? 'N/A';
            $supportsGenerateContent = in_array('generateContent', $model['supportedGenerationMethods'] ?? []) ? '✅ Sí' : '❌ No';
            
            // Resaltar modelos útiles
            if (strpos($name, 'gemini') !== false && $supportsGenerateContent === '✅ Sí') {
                echo "<tr style='background-color: #90EE90'>";
            } else {
                echo "<tr>";
            }
            echo "<td><strong>{$name}</strong></td>";
            echo "<td>{$displayName}</td>";
            echo "<td>{$version}</td>";
            echo "<td>{$supportsGenerateContent}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<br><h3>Modelos recomendados para usar:</h3>";
        echo "<ul>";
        foreach ($data['models'] as $model) {
            $name = $model['name'] ?? '';
            if (strpos($name, 'gemini') !== false && in_array('generateContent', $model['supportedGenerationMethods'] ?? [])) {
                echo "<li><strong>{$name}</strong> - {$model['displayName']}</li>";
            }
        }
        echo "</ul>";
        
    } else {
        echo "<pre>" . print_r($data, true) . "</pre>";
    }
} else {
    echo "Error: No se pudo obtener la lista de modelos<br>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
?>