<?php

declare(strict_types=1);

// 1. Cargar las rutas de configuración
$BASE = dirname(__DIR__, 2); // Ajusta la profundidad de la carpeta si es necesario
$CFG = is_file($BASE . '/config/conexion.php') ? ($BASE . '/config/conexion.php') : ($BASE . '/backend/config/db.php');
$FCT = is_file($BASE . '/config/facturama.php') ? ($BASE . '/config/facturama.php') : ($BASE . '/backend/config/facturama.php');

require_once $CFG;
if (is_file($FCT)) require_once $FCT;

// 2. Validar parámetros de entrada (GET)
$idFactura = isset($_GET['id_factura']) ? (int)$_GET['id_factura'] : 0;
$formato = isset($_GET['formato']) ? strtolower(trim((string)$_GET['formato'])) : '';

if ($idFactura <= 0 || !in_array($formato, ['pdf', 'xml'])) {
    http_response_code(400);
    die('Parámetros inválidos. Se requiere id_factura y formato (pdf o xml).');
}

try {
    /** @noinspection PhpUndefinedClassInspection */
    $conn = DB::get();

    // 3. Obtener el facturama_id y el folio de tu base de datos
    $q = $conn->prepare('SELECT facturama_id, folio FROM facturas WHERE id = ? LIMIT 1');
    $q->execute([$idFactura]);
    $factura = $q->fetch(PDO::FETCH_ASSOC);

    if (!$factura || empty($factura['facturama_id'])) {
        http_response_code(404);
        die('Factura no encontrada o aún no ha sido timbrada.');
    }

    $facturamaId = $factura['facturama_id'];
    $folio = $factura['folio'];

    // 4. Usar tu función nativa de facturama.php para descargar
    list($bodyContent, $contentType) = facturama_download_issued($formato, $facturamaId);

    // NUEVO: Verificamos si la respuesta de Facturama es un JSON
    $jsonResponse = json_decode($bodyContent, true);
    
    if (is_array($jsonResponse) && isset($jsonResponse['Content'])) {
        // Facturama devolvió el formato JSON {"Content": "JVBERi0xLjQK...", ...}
        $archivoFinal = base64_decode($jsonResponse['Content']);
    } else {
        // Si no es JSON, verificamos si es un texto Base64 puro o binario crudo
        $cuerpoLimpio = trim($bodyContent);
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $cuerpoLimpio)) {
            $archivoFinal = base64_decode($cuerpoLimpio);
        } else {
            $archivoFinal = $bodyContent;
        }
    }

    $nombreArchivo = "Factura_{$folio}.{$formato}";

    // 5. Configurar headers para forzar la descarga o visualización
    if ($formato === 'pdf') {
        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"{$nombreArchivo}\""); 
    } else {
        header("Content-Type: application/xml");
        header("Content-Disposition: attachment; filename=\"{$nombreArchivo}\"");
    }
    header('Content-Length: ' . strlen($archivoFinal));
    header('Cache-Control: private, max-age=0, must-revalidate');

    // Limpiar cualquier salida previa que pueda corromper el PDF (espacios en blanco, BOMs)
    if (ob_get_length()) ob_clean();

    // 6. Imprimir el archivo al navegador
    echo $archivoFinal;
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    // Registrar el error en el log que tienes en facturama.php si existe
    if (function_exists('facturas_log')) {
        facturas_log('DESCARGA_ERROR', $e);
    }
    die('Error al procesar la descarga: ' . $e->getMessage());
}