<?php
require_once __DIR__ . '/../db/conexao.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'], $data['nome'], $data['valor'])) {
    http_response_code(400); // JSON incompleto
    exit;
}

$id = $data['id'];
$nome = $data['nome'];
$tipo = isset($data['tipo']) ? $data['tipo'] : null;
$valor = $data['valor'];

if (!is_string($id) || !is_string($nome) || !is_numeric($valor) || $valor < 0) {
    http_response_code(422); // Dados inválidos
    exit;
}

// Verificar se ID já existe
$stmt = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE id = ?");
$stmt->execute([$id]);
if ($stmt->fetchColumn() > 0) {
    http_response_code(422); // ID duplicado
    exit;
}

$stmt = $pdo->prepare("INSERT INTO produtos (id, nome, tipo, valor) VALUES (?, ?, ?, ?)");
$stmt->execute([$id, $nome, $tipo, $valor]);

http_response_code(201); // Produto criado
?>
