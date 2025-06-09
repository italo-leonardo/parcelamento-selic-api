<?php
require_once __DIR__ . '/../db/conexao.php';

// Lê os dados do corpo da requisição
$data = json_decode(file_get_contents("php://input"), true);

// Verifica se os campos obrigatórios foram enviados
if (!isset($data['id'], $data['valorEntrada'], $data['qtdParcelas'], $data['idProduto'])) {
    http_response_code(400); // JSON incompleto
    echo json_encode(['erro' => 'JSON incompleto']);
    exit;
}

$id = $data['id'];
$entrada = $data['valorEntrada'];
$parcelas = $data['qtdParcelas'];
$idProduto = $data['idProduto'];

// Validações básicas
if (!is_string($id) || !is_numeric($entrada) || !is_numeric($parcelas) || $parcelas < 0 || !is_string($idProduto) || $entrada < 0) {
    http_response_code(422); // Dados inválidos
    echo json_encode(['erro' => 'Dados invalidos']);
    exit;
}

// Verifica se ID da compra já existe
$stmt = $pdo->prepare("SELECT COUNT(*) FROM compras WHERE id = ?");
$stmt->execute([$id]);
if ($stmt->fetchColumn() > 0) {
    http_response_code(422); // ID duplicado
    echo json_encode(['erro' => 'ID duplicado']);
    exit;
}

// Verifica se o produto existe e pega o valor
$stmt = $pdo->prepare("SELECT valor FROM produtos WHERE id = ?");
$stmt->execute([$idProduto]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    http_response_code(422); // Produto inexistente
    echo json_encode(['erro' => 'Produto inexistente']);
    exit;
}

$valorProduto = $produto['valor'];

// Verifica se entrada é maior que o valor do produto
if ($entrada > $valorProduto) {
    http_response_code(422);
    echo json_encode(['erro' => 'Entrada maior que o valor do produto']);
    exit;
}

// Calcula valor restante
$valorRestante = $valorProduto - $entrada;

// Simula juros apenas se parcelas > 6
$taxaJuros = 0.0;
$jurosAplicado = 0.0;
$valorParcela = 0.0;

if ($parcelas > 6) {
    // Buscar taxa da tabela de juros
    $stmt = $pdo->prepare("SELECT taxa FROM juros WHERE id = 1");
    $stmt->execute();
    $juros = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$juros) {
        http_response_code(500);
        echo json_encode(['erro' => 'Taxa de juros não definida. Atualize usando o endpoint /juros']);
        exit;
    }

    $taxaJuros = floatval($juros['taxa']); // ex: 0.1365
    $valorFinal = $valorRestante * pow(1 + $taxaJuros, $parcelas); // juros compostos
    $valorParcela = $valorFinal / $parcelas;
    $jurosAplicado = $taxaJuros * 100; // salvar como %
    echo json_encode([
    'taxaUsada' => ($taxaJuros * 100) . '%',
    'valorParcela' => number_format($valorParcela, 2, '.', '')
    ]);
    
} else {
    $valorParcela = $parcelas > 0 ? $valorRestante / $parcelas : 0;
}

// Salva a compra
$stmt = $pdo->prepare("INSERT INTO compras (id, valorEntrada, qtdParcelas, idProduto) VALUES (?, ?, ?, ?)");
$stmt->execute([$id, $entrada, $parcelas, $idProduto]);

// Salva parcelas
for ($i = 1; $i <= $parcelas; $i++) {
    $stmt = $pdo->prepare("INSERT INTO parcelas (idCompra, numero, valorParcela, jurosAplicado) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $i, $valorParcela, $jurosAplicado]);
}

http_response_code(201); // Compra criada com sucesso
?>
