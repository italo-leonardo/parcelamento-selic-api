<?php
require_once __DIR__ . '/../db/conexao.php';

// Verifica se o ID foi passado na URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parâmetro id obrigatório']);
    exit;
}

// Verifica se a compra existe
$stmt = $pdo->prepare("SELECT COUNT(*) FROM compras WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->fetchColumn() == 0) {
    http_response_code(404);
    echo json_encode(['erro' => 'Compra não encontrada']);
    exit;
}

try {
    // Inicia transação
    $pdo->beginTransaction();

    // Excluir parcelas relacionadas
    $stmt = $pdo->prepare("DELETE FROM parcelas WHERE idCompra = ?");
    $stmt->execute([$id]);

    // Excluir a compra
    $stmt = $pdo->prepare("DELETE FROM compras WHERE id = ?");
    $stmt->execute([$id]);

    // Confirma transação
    $pdo->commit();

    http_response_code(200);
    echo json_encode(['mensagem' => 'Compra e parcelas excluídas com sucesso']);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao excluir compra', 'detalhe' => $e->getMessage()]);
}
