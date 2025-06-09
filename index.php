<?php
$requestUri = $_SERVER['REQUEST_URI'];
$request = parse_url($requestUri, PHP_URL_PATH); // remove ?id=...

$method = $_SERVER['REQUEST_METHOD'];

$request = str_replace('/parcelamento-selic-api', '', $request);

if ($request === '/produtos' && $method === 'POST') {
    require_once 'endpoints/produtos.php';
    exit;
}

if ($request === '/compras' && $method === 'POST') {
    require_once 'endpoints/compras.php';
    exit;
}

if ($request === '/compras' && $method === 'GET') {
    require_once 'endpoints/compras_get.php';
    exit;
}

if ($request === '/juros' && $method === 'PUT') {
    require_once 'endpoints/juros.php';
    exit;
}

if ($request === '/estatistica' && $method === 'GET') {
    require_once 'endpoints/estatistica.php';
    exit;
}

if ($request === '/compras' && $method === 'DELETE') {
    require_once 'endpoints/compras_delete.php';
    exit;
}


// Default 404
http_response_code(404);
echo json_encode(['erro' => 'Endpoint não encontrado']);
?>
