<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>MXTopo Dictámenes - Asistente Técnico Documental</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: #1a2634;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 12px;
        }

        .chat-wrapper {
            width: 100%;
            max-width: 1600px;
            height: calc(100vh - 24px);
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            overflow: hidden;
        }

        .sidebar {
            width: 280px;
            background: #f8fafc;
            border-right: 1px solid #e9edf2;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 28px 20px 20px;
        }

        .logo-main {
            font-size: 26px;
            font-weight: 700;
            color: #0a1929;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .logo-sub {
            font-size: 16px;
            font-weight: 500;
            color: #2d3a4f;
            margin-bottom: 12px;
        }

        .logo-desc {
            font-size: 13px;
            color: #546e7a;
            line-height: 1.5;
            margin-bottom: 20px;
            padding-right: 10px;
        }

        .mode-indicator-sidebar {
            background: #eef2f6;
            border-radius: 100px;
            padding: 10px 16px;
            font-size: 14px;
            color: #0a1929;
            border: 1px solid #dce3ec;
            display: inline-block;
        }

        .mode-indicator-sidebar span {
            font-weight: 600;
            color: #1565c0;
        }

        .quick-actions {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .quick-actions-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 16px;
        }

        .action-list {
            list-style: none;
        }

        .action-item {
            padding: 10px 14px;
            margin-bottom: 4px;
            border-radius: 10px;
            font-size: 14px;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-item:hover {
            background: #e6edf5;
            color: #1565c0;
        }

        .action-item i {
            font-size: 18px;
            width: 24px;
            color: #546e7a;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #e9edf2;
            font-size: 12px;
            color: #64748b;
        }

        .sidebar-footer div {
            margin-bottom: 6px;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
            min-width: 0;
        }

        .chat-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e9edf2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            flex-shrink: 0;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .header-title-main {
            font-size: 18px;
            font-weight: 600;
            color: #0a1929;
        }

        .header-title-mode {
            font-size: 14px;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 100px;
        }

        .connection-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #2e7d32;
            background: #e8f5e9;
            padding: 6px 14px;
            border-radius: 100px;
            flex-shrink: 0;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #2e7d32;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            scroll-behavior: smooth;
            background: #ffffff;
            min-height: 0;
        }

        .message {
            display: flex;
            margin-bottom: 24px;
            animation: fadeIn 0.3s ease;
            width: 100%;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-message {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            flex-shrink: 0;
        }

        .assistant-avatar {
            background: #0a1929;
            color: white;
            margin-right: 14px;
        }

        .user-avatar {
            background: #2d3a4f;
            color: white;
            margin-left: 14px;
        }

        .message-content {
            max-width: calc(100% - 100px);
            background: #f8fafc;
            border-radius: 16px;
            padding: 16px 20px;
            font-size: 14px;
            line-height: 1.6;
            color: #1e293b;
            border: 1px solid #e9edf2;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .user-message .message-content {
            background: #0a1929;
            color: white;
            border: none;
        }

        .message-time {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 6px;
        }

        .table-responsive {
            overflow-x: auto;
            margin: 16px 0;
            border-radius: 12px;
            border: 1px solid #e9edf2;
            max-width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            background: white;
            min-width: 600px;
        }

        th {
            background: #f1f5f9;
            color: #0a1929;
            font-weight: 600;
            padding: 12px 10px;
            text-align: left;
            border-bottom: 2px solid #dce3ec;
            font-size: 12px;
            white-space: nowrap;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e9edf2;
            vertical-align: top;
        }

        .severity-high {
            background: #ffebee;
            color: #b71c1c;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
        }

        .severity-medium {
            background: #fff8e1;
            color: #b76e1c;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .severity-low {
            background: #e8f5e9;
            color: #1b5e20;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .info-block {
            background: #f1f9fe;
            border-left: 4px solid #1565c0;
            padding: 16px 20px;
            border-radius: 10px;
            margin: 16px 0;
            font-size: 14px;
        }

        .warning-block {
            background: #fff8e1;
            border-left: 4px solid #ffb300;
            padding: 16px 20px;
            border-radius: 10px;
            margin: 16px 0;
        }

        .footer-note {
            background: #f8fafc;
            border-radius: 10px;
            padding: 16px;
            font-size: 12px;
            color: #546e7a;
            text-align: center;
            margin-top: 24px;
            border: 1px solid #e9edf2;
        }

        .chat-input-area {
            padding: 20px 24px;
            background: white;
            border-top: 1px solid #e9edf2;
            flex-shrink: 0;
        }

        .suggestions {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .suggestion-chip {
            background: #f1f5f9;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            color: #1e293b;
            border: 1px solid #dce3ec;
            white-space: nowrap;
        }

        .suggestion-chip:hover {
            background: #0a1929;
            color: white;
            border-color: #0a1929;
        }

        .input-container {
            display: flex;
            gap: 10px;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #dce3ec;
            border-radius: 16px;
            padding: 4px 4px 4px 20px;
            width: 100%;
            flex-wrap: wrap;
        }

        #chatInput {
            flex: 1;
            padding: 14px 0;
            border: none;
            background: transparent;
            font-size: 14px;
            outline: none;
            color: #1e293b;
            min-width: 200px;
        }

        #chatInput::placeholder {
            color: #94a3b8;
        }

        .file-upload {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            flex-wrap: wrap;
        }

        .file-label {
            background: white;
            padding: 10px 18px;
            border-radius: 100px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: #1e293b;
            border: 1px solid #dce3ec;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .file-label:hover {
            background: #0a1929;
            color: white;
            border-color: #0a1929;
        }

        input[type="file"] {
            display: none;
        }

        .submit-btn {
            background: #0a1929;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 100px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            white-space: nowrap;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            background: #1e2f40;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(10, 25, 41, 0.2);
        }

        .submit-btn:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .file-name {
            font-size: 12px;
            color: #64748b;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .loading {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #546e7a;
        }

        .loading-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #e2e8f0;
            border-top-color: #0a1929;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .input-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            font-size: 11px;
            color: #94a3b8;
            flex-wrap: wrap;
            gap: 10px;
        }

        .copy-btn {
            cursor: pointer;
            color: #1565c0;
        }

        .copy-btn:hover {
            text-decoration: underline;
        }

        .confirm-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .confirm-btn {
            background: #0a1929;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
        }

        .cancel-btn {
            background: #e2e8f0;
            color: #1e293b;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
        }

        .estimation-message {
            border-left: 4px solid #ffb300;
            background: #fff8e1;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        @media (max-width: 1024px) {
            .sidebar {
                display: none;
            }
            
            body {
                padding: 8px;
            }
            
            .message-content {
                max-width: calc(100% - 70px);
            }
        }

        @media (max-width: 768px) {
            .input-container {
                flex-direction: column;
                align-items: stretch;
                background: transparent;
                border: none;
                padding: 0;
            }
            
            #chatInput {
                background: #f8fafc;
                border: 1px solid #dce3ec;
                border-radius: 12px;
                padding: 12px 16px;
                width: 100%;
            }
            
            .file-upload {
                justify-content: space-between;
            }
            
            .file-label, .submit-btn {
                flex: 1;
                text-align: center;
                justify-content: center;
            }
            
            .suggestions {
                overflow-x: auto;
                padding-bottom: 4px;
                flex-wrap: nowrap;
            }
            
            .suggestion-chip {
                flex-shrink: 0;
            }
        }
    </style>
</head>
<body>

<div class="chat-wrapper">
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-main">MXTopo</div>
            <div class="logo-sub">Dictámenes</div>
            <div class="logo-desc">Asistente técnico documental para apoyo estructural en dictámenes periciales</div>
            
            <div class="mode-indicator-sidebar" id="sidebarMode">
                Modo: <span id="sidebarModeText">Modo Cero</span>
            </div>
        </div>

        <div class="quick-actions">
            <div class="quick-actions-title">ACCIONES RÁPIDAS</div>
            <ul class="action-list">
                <li class="action-item" onclick="setSuggestion('Activar modo cero')">
                    <i>📋</i> Modo Cero
                </li>
                <li class="action-item" onclick="setSuggestion('Activar modo Demanda-Contestación')">
                    <i>⚖️</i> Demanda-Contestación
                </li>
                <li class="action-item" onclick="setSuggestion('Activar modo Línea de tiempo')">
                    <i>📅</i> Línea de Tiempo
                </li>
                <li class="action-item" onclick="setSuggestion('Activar modo Revisión Editorial')">
                    <i>📝</i> Revisión Editorial
                </li>
                <li class="action-item" onclick="setSuggestion('Activar modo Bestia')">
                    <i>🦁</i> Modo Bestia
                </li>
                <li class="action-item" onclick="setSuggestion('Activar modo Revisión de Informe')">
                    <i>📊</i> Revisión Informe
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div>💰 Al adjuntar PDF se mostrará el costo estimado</div>
            <div>PDF · DOCX · XLSX · TXT</div>
            <div style="margin-top: 12px;">v2.0 · Estimación local gratuita</div>
        </div>
    </div>

    <div class="main-content">
        <div class="chat-header">
            <div class="header-title">
                <span class="header-title-main">Conversación general</span>
                <span class="header-title-mode" id="headerMode">/ Modo Cero</span>
            </div>
            <div class="connection-status">
                <span class="status-dot"></span>
                Conectado
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <div class="message assistant-message">
                <div class="message-avatar assistant-avatar">M</div>
                <div class="message-content">
                    <strong># MXTopo</strong><br>
                    <strong>## Dictámenes</strong><br><br>
                    Asistente técnico documental para apoyo estructural en dictámenes periciales<br><br>
                    
                    <strong>Modo: Modo Cero</strong><br><br>
                    
                    <strong>### ACCIONES RÁPIDAS</strong><br>
                    - Modo Cero<br>
                    - Demanda-Contestación<br>
                    - Línea de Tiempo<br>
                    - Revisión Editorial<br>
                    - Modo Bestia<br><br>
                    
                    <strong>### Conversación general / Modo Cero</strong><br>
                    1. Selecciona un modo de análisis desde el menú lateral o escribe "Activar modo [nombre]".<br>
                    2. Para algunos modos, se te pedirá especificar si el análisis es total o parcial.<br>
                    3. Una vez seleccionado el modo, adjunta tu archivo.<br><br>
                    
                    <strong>### AVISO DE PRIVACIDAD OPERATIVO</strong><br>
                    Los documentos que se cargan en esta sesión son utilizados únicamente para el análisis solicitado por el usuario. No se hacen públicos ni son visibles para otros usuarios o despachos. Cada sesión es independiente.<br><br>
                    
                    <strong>### ACEPTACIÓN DE TÉRMINOS DE USO</strong><br>
                    El uso de MXTopo Dictámenes implica la aceptación de que esta herramienta es únicamente un sistema de apoyo documental. No sustituye criterio profesional ni responsabilidad del perito.<br><br>
                    
                    <strong>### NOTA DE RESPONSABILIDAD</strong><br>
                    "Mextopo es un asistente de inteligencia artificial utilizado como herramienta de apoyo. El contenido generado puede contener errores u omisiones. La revisión final y la responsabilidad del dictamen corresponden exclusivamente al perito firmante."<br><br>
                    
                    <div class="message-time">Justo ahora</div>
                </div>
            </div>
        </div>

        <div class="chat-input-area">
            <div class="suggestions" id="suggestions">
                <span class="suggestion-chip" onclick="setSuggestion('Activar modo cero')">📋 Modo Cero</span>
                <span class="suggestion-chip" onclick="setSuggestion('Activar modo Demanda-Contestación')">⚖️ Demanda-Contestación</span>
                <span class="suggestion-chip" onclick="setSuggestion('Activar modo Línea de tiempo')">📅 Línea de Tiempo</span>
                <span class="suggestion-chip" onclick="setSuggestion('Activar modo Revisión Editorial')">📝 Revisión Editorial</span>
                <span class="suggestion-chip" onclick="setSuggestion('Activar modo Bestia')">🦁 Modo Bestia</span>
                <span class="suggestion-chip" onclick="setSuggestion('Activar modo Revisión de Informe')">📊 Revisión Informe</span>
            </div>
            
            <form id="uploadForm">
                <div class="input-container">
                    <input type="text" id="chatInput" placeholder="Escribe tu mensaje o comando (ej: Activar modo cero)..." autocomplete="off">
                    
                    <div class="file-upload">
                        <input type="file" id="pdfFile" accept=".pdf,.docx,.xlsx,.txt">
                        <label for="pdfFile" class="file-label">
                            <span>📎</span> ADJUNTAR
                        </label>
                        <span class="file-name" id="fileName"></span>
                        
                        <button type="submit" class="submit-btn" id="submitBtn">ENVIAR</button>
                    </div>
                </div>
            </form>
            
            <div class="input-footer">
                <span>💰 Estimación LOCAL sin consumo de tokens</span>
                <span>PDF · DOCX · XLSX · TXT</span>
                <span class="copy-btn" onclick="copyLastResponse()">📋 Copiar respuesta</span>
            </div>
        </div>
    </div>
</div>

<script>
// Elementos del DOM
const chatMessages = document.getElementById('chatMessages');
const pdfFile = document.getElementById('pdfFile');
const fileName = document.getElementById('fileName');
const uploadForm = document.getElementById('uploadForm');
const submitBtn = document.getElementById('submitBtn');
const chatInput = document.getElementById('chatInput');
const headerMode = document.getElementById('headerMode');
const sidebarModeText = document.getElementById('sidebarModeText');

// Variables
let esperandoDelimitacion = false;
let modoActual = 'cero';
let tipoDelimitacion = null;
let lastResponse = '';
let pendingFile = null;
let pendingFileConfirmed = false;

// Scroll automático
function scrollToBottom(smooth = true) {
    chatMessages.scrollTo({
        top: chatMessages.scrollHeight,
        behavior: smooth ? 'smooth' : 'auto'
    });
}

// Mostrar nombre del archivo y estimar costo automáticamente
pdfFile.addEventListener('change', async function() {
    const archivo = this.files[0];
    if (!archivo) {
        fileName.textContent = '';
        pendingFile = null;
        pendingFileConfirmed = false;
        return;
    }
    
    fileName.textContent = archivo.name;
    pendingFile = archivo;
    pendingFileConfirmed = false;
    
    // Solo estimar costo si es PDF
    if (archivo.type === 'application/pdf') {
        await estimarCostoAutomatico(archivo);
    } else {
        enviarMensaje(`📎 Archivo seleccionado: ${archivo.name}\n\n⚠️ La estimación de costo solo está disponible para archivos PDF. Puedes enviarlo directamente si ya tienes un modo activo.`, false);
    }
});

// Función para estimar costo automáticamente (VERSIÓN LOCAL SIN CONSUMO DE TOKENS)
async function estimarCostoAutomatico(archivo) {
    const formData = new FormData();
    formData.append('pdf', archivo);
    
    mostrarLoading("Analizando PDF localmente para estimar costo...");
    
    try {
        const response = await fetch('estimar_tokens_local.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        quitarLoading();
        
        if (result.success) {
            let recomendacionEmoji = '';
            if (result.recomendacion.includes('CRÍTICO') || (result.recomendacion.includes('ALTO') && result.recomendacion.includes('elevado'))) {
                recomendacionEmoji = '⚠️⚠️⚠️';
            } else if (result.recomendacion.includes('ALTO') || (result.recomendacion.includes('MEDIO') && result.recomendacion.includes('moderado'))) {
                recomendacionEmoji = '⚠️';
            } else {
                recomendacionEmoji = '✅';
            }
            
            let tipoIcono = '';
            let tipoTexto = '';
            if (result.es_escaneado) {
                tipoIcono = '📷';
                tipoTexto = 'PDF escaneado (estimación por páginas)';
            } else {
                tipoIcono = '📄';
                tipoTexto = 'PDF con texto (estimación precisa)';
            }
            
            const mensajeEstimacion = `
**💰 ESTIMACIÓN DE COSTO - ${archivo.name}**

| Concepto | Valor |
|----------|-------|
| **${tipoIcono} Total de páginas** | ${result.total_pages} |
| **Tokens promedio por página** | ${result.avg_tokens_per_page.toLocaleString()} |
| **Tokens totales proyectados** | ${result.projected_tokens.toLocaleString()} |
| **Costo estimado** | **$${result.estimated_cost_usd} USD** |
| **Tipo de estimación** | ${tipoTexto} |
| **Recomendación** | ${recomendacionEmoji} ${result.recomendacion} |

**ℹ️ Notas:**
- Esta estimación se realizó **LOCALMENTE**
- El costo real puede variar según el contenido de cada página
- Solo se consumirán tokens si confirmas el análisis

¿Deseas continuar con el análisis de este archivo?
            `;
            
            mostrarConfirmacionEstimacion(mensajeEstimacion, archivo);
        } else {
            enviarMensaje(`❌ Error al estimar costo: ${result.error}`, false);
            pendingFile = null;
            pendingFileConfirmed = false;
        }
    } catch (error) {
        quitarLoading();
        enviarMensaje(`❌ Error de conexión al estimar: ${error.message}`, false);
        pendingFile = null;
        pendingFileConfirmed = false;
    }
}

// Mostrar confirmación de estimación
function mostrarConfirmacionEstimacion(mensaje, archivo) {
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = 'message assistant-message';
    
    const avatarDiv = document.createElement('div');
    avatarDiv.className = 'message-avatar assistant-avatar';
    avatarDiv.textContent = 'M';
    mensajeDiv.appendChild(avatarDiv);
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content estimation-message';
    contentDiv.innerHTML = formatearMensaje(mensaje);
    
    const buttonsDiv = document.createElement('div');
    buttonsDiv.className = 'confirm-buttons';
    
    const confirmBtn = document.createElement('button');
    confirmBtn.textContent = '✅ Sí, continuar';
    confirmBtn.className = 'confirm-btn';
    confirmBtn.onclick = () => {
        mensajeDiv.remove();
        pendingFileConfirmed = true;
        enviarMensaje(`✅ Confirmado. El archivo "${archivo.name}" será procesado.`, false);
        enviarArchivo(archivo);
    };
    
    const cancelBtn = document.createElement('button');
    cancelBtn.textContent = '❌ No, cancelar';
    cancelBtn.className = 'cancel-btn';
    cancelBtn.onclick = () => {
        mensajeDiv.remove();
        enviarMensaje(`❌ Análisis cancelado para "${archivo.name}". Puedes adjuntar otro archivo.`, false);
        pendingFile = null;
        pendingFileConfirmed = false;
        pdfFile.value = '';
        fileName.textContent = '';
    };
    
    buttonsDiv.appendChild(confirmBtn);
    buttonsDiv.appendChild(cancelBtn);
    contentDiv.appendChild(buttonsDiv);
    
    mensajeDiv.appendChild(contentDiv);
    
    const timeDiv = document.createElement('div');
    timeDiv.className = 'message-time';
    timeDiv.textContent = getCurrentTime();
    mensajeDiv.appendChild(timeDiv);
    
    chatMessages.appendChild(mensajeDiv);
    scrollToBottom(true);
}

// Función para enviar archivo
async function enviarArchivo(archivo) {
    if (esperandoDelimitacion) {
        alert('Primero debes indicar si el análisis es total o parcial. Escribe tu respuesta en el chat.');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'ENVIANDO...';
    
    enviarMensaje(`📎 Procesando archivo: ${archivo.name}`, true);
    mostrarLoading(`Analizando en modo ${modoActual}...`);
    
    const formData = new FormData();
    formData.append('pdf', archivo);
    formData.append('modo', modoActual);
    
    try {
        const response = await fetch('procesar.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        quitarLoading();
        
        if (result.success) {
            enviarMensaje(result.resumen, false, true);
        } else {
            enviarMensaje("❌ Error: " + result.error, false, true);
        }
    } catch (error) {
        quitarLoading();
        enviarMensaje("❌ Error de conexión: " + error.message, false, true);
    }
    
    submitBtn.disabled = false;
    submitBtn.textContent = 'ENVIAR';
    pdfFile.value = '';
    fileName.textContent = '';
    pendingFile = null;
    pendingFileConfirmed = false;
}

// Función para formatear mensaje
function formatearMensaje(texto) {
    if (!texto) return '';
    
    let formateado = texto
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
    
    formateado = formateado.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    formateado = formateado.replace(/\#\#\#\s(.*?)(?:\n|$)/g, '<strong style="font-size: 1.1em;">$1</strong><br>');
    formateado = formateado.replace(/\#\#\s(.*?)(?:\n|$)/g, '<strong style="font-size: 1.2em;">$1</strong><br>');
    formateado = formateado.replace(/\#\s(.*?)(?:\n|$)/g, '<strong style="font-size: 1.3em;">$1</strong><br>');
    
    // Tablas
    if (formateado.includes('|') && formateado.includes('\n|')) {
        const lines = formateado.split('\n');
        let inTable = false;
        let tableHtml = '';
        let newLines = [];
        
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            
            if (line.trim().startsWith('|') && line.includes('|')) {
                if (!inTable) {
                    inTable = true;
                    tableHtml = '<div class="table-responsive">\n<table>\n';
                }
                
                if (line.includes('---') || line.includes('===')) {
                    continue;
                }
                
                const cells = line.split('|').filter(cell => cell.trim() !== '');
                
                if (i === 0 || (i > 0 && lines[i-1] && lines[i-1].includes('---'))) {
                    tableHtml += '<thead>\n<tr>\n';
                    cells.forEach(cell => {
                        tableHtml += `<th>${cell.trim()}</th>\n`;
                    });
                    tableHtml += '</tr>\n</thead>\n<tbody>\n';
                } else {
                    tableHtml += '<tr>\n';
                    cells.forEach(cell => {
                        const cleanCell = cell.trim();
                        if (cleanCell === 'ALTO') {
                            tableHtml += `<td><span class="severity-high">${cleanCell}</span></td>\n`;
                        } else if (cleanCell === 'MEDIO') {
                            tableHtml += `<td><span class="severity-medium">${cleanCell}</span></td>\n`;
                        } else if (cleanCell === 'BAJO') {
                            tableHtml += `<td><span class="severity-low">${cleanCell}</span></td>\n`;
                        } else {
                            tableHtml += `<td>${cleanCell}</td>\n`;
                        }
                    });
                    tableHtml += '</tr>\n';
                }
            } else {
                if (inTable) {
                    tableHtml += '</tbody>\n</table>\n</div>';
                    newLines.push(tableHtml);
                    inTable = false;
                }
                newLines.push(line);
            }
        }
        
        if (inTable) {
            tableHtml += '</tbody>\n</table>\n</div>';
            newLines.push(tableHtml);
        }
        
        formateado = newLines.join('\n');
    }
    
    formateado = formateado.replace(/\n/g, '<br>');
    return formateado;
}

// Enviar mensaje
function enviarMensaje(texto, esUsuario = false, esStream = false) {
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `message ${esUsuario ? 'user-message' : 'assistant-message'}`;
    
    const avatarDiv = document.createElement('div');
    avatarDiv.className = `message-avatar ${esUsuario ? 'user-avatar' : 'assistant-avatar'}`;
    avatarDiv.textContent = esUsuario ? '👤' : 'M';
    mensajeDiv.appendChild(avatarDiv);
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    
    const timeDiv = document.createElement('div');
    timeDiv.className = 'message-time';
    timeDiv.textContent = getCurrentTime();
    
    if (!esUsuario && esStream) {
        contentDiv.innerHTML = '';
        mensajeDiv.appendChild(contentDiv);
        mensajeDiv.appendChild(timeDiv);
        chatMessages.appendChild(mensajeDiv);
        
        let i = 0;
        function escribir() {
            if (i < texto.length) {
                contentDiv.innerHTML += texto.charAt(i);
                i++;
                setTimeout(escribir, 10);
                scrollToBottom(true);
            } else {
                contentDiv.innerHTML = formatearMensaje(texto);
                scrollToBottom(true);
            }
        }
        escribir();
    } else {
        contentDiv.innerHTML = formatearMensaje(texto);
        mensajeDiv.appendChild(contentDiv);
        mensajeDiv.appendChild(timeDiv);
        chatMessages.appendChild(mensajeDiv);
        scrollToBottom(true);
    }
    
    if (!esUsuario) {
        lastResponse = texto;
    }
}

// Loading
function mostrarLoading(mensaje = "Procesando...") {
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'message assistant-message';
    loadingDiv.id = 'loadingMessage';
    loadingDiv.innerHTML = `
        <div class="message-avatar assistant-avatar">M</div>
        <div class="message-content">
            <div class="loading">
                <span class="loading-spinner"></span>
                ${mensaje}
            </div>
        </div>
    `;
    chatMessages.appendChild(loadingDiv);
    scrollToBottom(true);
}

function quitarLoading() {
    const loading = document.getElementById('loadingMessage');
    if (loading) loading.remove();
}

// Copiar respuesta
function copyLastResponse() {
    if (lastResponse) {
        navigator.clipboard.writeText(lastResponse.replace(/<[^>]*>/g, ''));
        alert('Respuesta copiada');
    }
}

// Actualizar modo
function actualizarModo(modo, fase) {
    modoActual = modo;
    const nombres = {
        'cero': 'Modo Cero',
        'demanda_contestacion': 'Demanda-Contestación',
        'linea_tiempo': 'Línea de Tiempo',
        'revision_editorial': 'Revisión Editorial',
        'revision_tecnica': 'Modo Bestia',
        'revision_informe': 'Revisión Informe'
    };
    const texto = nombres[modo] || modo;
    headerMode.innerHTML = `/ ${texto}`;
    sidebarModeText.textContent = texto;
}

// Función para enviar delimitación
async function enviarDelimitacion(respuesta) {
    mostrarLoading("Procesando delimitación...");
    
    try {
        const response = await fetch('guardar_delimitacion.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ respuesta: respuesta })
        });
        
        const data = await response.json();
        quitarLoading();
        
        if (data.success) {
            enviarMensaje("✅ " + data.mensaje, false);
            esperandoDelimitacion = false;
            chatInput.placeholder = "Escribe tu mensaje o comando (ej: Activar modo cero)...";
        } else {
            enviarMensaje("❌ " + (data.error || 'Error al procesar delimitación'), false);
        }
    } catch (error) {
        quitarLoading();
        enviarMensaje("❌ Error de conexión", false);
    }
}

// Evento Enter
chatInput.addEventListener('keypress', async function(e) {
    if (e.key === 'Enter' && this.value.trim()) {
        const mensaje = this.value.trim();
        
        if (esperandoDelimitacion) {
            e.preventDefault();
            await enviarDelimitacion(mensaje);
            this.value = '';
            return;
        }
        
        enviarMensaje(mensaje, true);
        
        mostrarLoading("Procesando comando...");
        
        try {
            const response = await fetch('activar_modo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ comando: mensaje })
            });
            
            const data = await response.json();
            quitarLoading();
            
            if (data.success) {
                actualizarModo(data.modo, data.fase);
                enviarMensaje(data.mensaje, false, true);
                
                if (data.requiere_delimitacion) {
                    esperandoDelimitacion = true;
                    tipoDelimitacion = data.tipo_delimitacion;
                    
                    if (data.tipo_delimitacion === 'rangos') {
                        chatInput.placeholder = "Escribe 'total' o 'parcial, demanda:1-15, contestacion:16-30'...";
                    } else {
                        chatInput.placeholder = "Escribe 'total' o 'parcial'...";
                    }
                }
            } else {
                enviarMensaje('❌ ' + (data.mensaje || 'Error'), false, true);
            }
        } catch (error) {
            quitarLoading();
            enviarMensaje('❌ Error de conexión', false, true);
        }
        
        this.value = '';
    }
});

// Submit archivo - SIN ALERTA DE SELECCIONAR ARCHIVO
uploadForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Si no hay archivo, simplemente no hace nada
    if (!pendingFile) {
        return;
    }
    
    if (esperandoDelimitacion) {
        alert('Primero debes indicar si el análisis es total o parcial. Escribe tu respuesta en el chat.');
        return;
    }
    
    // Si ya hay confirmación pendiente, usar directamente
    if (pendingFileConfirmed) {
        enviarArchivo(pendingFile);
    } else {
        // Si no hay confirmación, estimar primero (solo si es PDF)
        if (pendingFile.type === 'application/pdf') {
            await estimarCostoAutomatico(pendingFile);
        } else {
            // Para archivos no PDF, enviar directamente
            enviarArchivo(pendingFile);
        }
    }
});

// Función para establecer sugerencia
function setSuggestion(texto) {
    chatInput.value = texto;
    chatInput.focus();
}

function getCurrentTime() {
    return new Date().toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
}

// Scroll inicial
scrollToBottom(false);
</script>

</body>
</html>