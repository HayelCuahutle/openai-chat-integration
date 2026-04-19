# MXTopo Dictámenes - Asistente Técnico Documental

## 📌 Descripción General

**MXTopo Dictámenes** es un sistema web de asistencia técnica diseñado específicamente para abogados, peritos y profesionales del ámbito legal y estructural. Su objetivo principal es optimizar el flujo de trabajo en la elaboración y revisión de dictámenes periciales, mediante un asistente conversacional que analiza documentos y extrae información clave según el contexto del caso.

El sistema permite cargar archivos (PDF, DOCX, XLSX, TXT) y, dependiendo del "modo de análisis" activado, genera resúmenes, líneas de tiempo, confrontas de demandas, revisiones editoriales o análisis técnicos profundos.

## ⚙️ ¿Cómo funciona?

1. **Selección del modo de análisis**:  
   El usuario puede elegir entre varios modos especializados desde el menú lateral o mediante comandos de texto:
   - `Modo Cero` (base)
   - `Demanda-Contestación`
   - `Línea de Tiempo`
   - `Revisión Editorial`
   - `Modo Bestia` (revisión técnica profunda)
   - `Revisión de Informe`

2. **Configuración del alcance (si aplica)**:  
   Algunos modos solicitan al usuario especificar si el análisis es **total** (documento completo) o **parcial** (rangos de páginas por sección, ej. demanda vs contestación).

3. **Carga del archivo**:  
   Una vez definido el modo y alcance, el usuario adjunta un documento. El sistema procesa el archivo localmente (sin consumo de tokens externos) y muestra una **estimación de costo** basada en número de páginas y tokens proyectados.

4. **Análisis y respuesta**:  
   El sistema envía el archivo y el contexto al backend (`procesar.php`), que devuelve un análisis estructurado (tablas, alertas, recomendaciones). El resultado se muestra en el chat con formato legible y opción de copiar la respuesta.

5. **Interfaz amigable y responsive**:  
   El frontend está construido con HTML5, CSS3 y JavaScript vanilla. Incluye:
   - Barra lateral con accesos rápidos.
   - Indicador de modo activo.
   - Sugerencias interactivas (chips).
   - Confirmación de análisis antes de consumir recursos.
   - Adaptación a dispositivos móviles.

## 🔐 Privacidad

Los documentos cargados se utilizan únicamente durante la sesión activa. No se almacenan ni comparten con otros usuarios. Cada sesión es independiente.

---

📌 **Estado del proyecto**: Activo / En uso profesional  
📅 **Versión**: 2.0  
💰 **Estimación local gratuita** (sin consumo de tokens externos)
