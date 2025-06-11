<?php
require_once __DIR__ . '/../db/conexao.php';

// Função para gerar UUID (versão simplificada)
function gerarUUIDv4() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['nome'], $data['valor'])) {
    http_response_code(400); // JSON incompleto
    echo json_encode(['erro' => '400 - Campos obrigatórios: nome e valor']);
    exit;
}

$nome = $data['nome'];
$tipo = isset($data['tipo']) ? $data['tipo'] : null;
$valor = $data['valor'];

if (!is_string($nome) || !is_numeric($valor) || $valor < 0) {
    http_response_code(422); // Dados inválidos
    echo json_encode(['erro' => ' 422 - Dados invalidos']);
    exit;
}

// Gerar ID único (UUID)
$id = gerarUUIDv4();

// Salvar no banco
$stmt = $pdo->prepare("INSERT INTO produtos (id, nome, tipo, valor) VALUES (?, ?, ?, ?)");
$stmt->execute([$id, $nome, $tipo, $valor]);

http_response_code(201);
echo json_encode([
    'mensagem' => '201 - Produto criado com sucesso',
    'id' => $id
]);
?>
