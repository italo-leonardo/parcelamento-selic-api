<?php
require_once __DIR__ . '/../db/conexao.php';

// Verifica se há um parâmetro 'id' na URL (GET)
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    // Buscar apenas uma compra específica
    $stmt = $pdo->prepare("SELECT * FROM compras WHERE id = ?");
    $stmt->execute([$id]);
    $compra = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$compra) {
        http_response_code(404);
        echo json_encode(['erro' => '404 - Compra não encontrada']);
        exit;
    }

    // Buscar parcelas dessa compra
    $stmtParcelas = $pdo->prepare("SELECT numero, valorParcela, jurosAplicado FROM parcelas WHERE idCompra = ?");
    $stmtParcelas->execute([$id]);
    $compra['parcelas'] = $stmtParcelas->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode($compra);
    exit;
}

// Se nenhum ID for passado, listar todas as compras
$stmt = $pdo->prepare("SELECT * FROM compras");
$stmt->execute();
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$compras || count($compras) === 0) {
    http_response_code(404);
    echo json_encode(['mensagem' => '404 - Nenhuma compra encontrada']);
    exit;
}

// Buscar parcelas de cada compra
foreach ($compras as &$compra) {
    $stmtParcelas = $pdo->prepare("SELECT numero, valorParcela, jurosAplicado FROM parcelas WHERE idCompra = ?");
    $stmtParcelas->execute([$compra['id']]);
    $compra['parcelas'] = $stmtParcelas->fetchAll(PDO::FETCH_ASSOC);
}

http_response_code(200);
echo json_encode($compras);
?>
