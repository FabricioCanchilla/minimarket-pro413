<?php
require_once __DIR__ . '/config.php';

/**
 * Envía una solicitud HTTP al backend.
 * Devuelve [codigo_http, datos_decodificados]. Si no hay conexión, codigo = 0.
 */
function api_request(string $metodo, string $ruta, ?array $datos = null): array
{
    $opciones = [
        'method'        => $metodo,
        'header'        => "Accept: application/json\r\nContent-Type: application/json\r\n",
        'ignore_errors' => true, // permite leer también las respuestas 4xx y 5xx
        'timeout'       => 10,
    ];
    if ($datos !== null) {
        $opciones['content'] = json_encode($datos);
    }

    $contexto  = stream_context_create(['http' => $opciones]);
    $respuesta = @file_get_contents(API_URL . $ruta, false, $contexto);

    // PHP 8.4+ usa una función; las versiones anteriores, una variable especial
    $cabeceras = function_exists('http_get_last_response_headers')
        ? http_get_last_response_headers()
        : ($http_response_header ?? null);

    if ($respuesta === false || empty($cabeceras)) {
        return [0, ['error' => 'No se pudo conectar con el backend (' . API_URL . ')']];
    }

    preg_match('#HTTP/\S+\s+(\d{3})#', $cabeceras[0], $m);
    return [(int)($m[1] ?? 0), json_decode($respuesta, true)];
}