<?php
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove a pasta base do início da URL
$request = str_replace('/parcelamento-selic-api', '', $request);

if ($request === '/produtos' && $method === 'POST') {
    require_once 'endpoints/produtos.php';
    exit;
}

if ($request === '/compras' && $method === 'POST') {
    require_once 'endpoints/compras.php';
    exit;
}

http_response_code(404);
echo json_encode(['erro' => 'Endpoint não encontrado']);
?>
