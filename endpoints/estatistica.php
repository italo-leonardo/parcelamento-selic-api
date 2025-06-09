<?php
require_once __DIR__ . '/../db/conexao.php';

// Buscar todas as compras
$stmt = $pdo->prepare("SELECT id, valorEntrada FROM compras");
$stmt->execute();
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalCompras = count($compras);

if ($totalCompras === 0) {
    echo json_encode([
        "count" => 0,
        "sum" => 0,
        "avg" => 0,
        "sumTx" => 0,
        "avgTx" => 0
    ]);
    exit;
}

$valorTotalGeral = 0;
$jurosTotalGeral = 0;

foreach ($compras as $compra) {
    $idCompra = $compra['id'];
    $entrada = floatval($compra['valorEntrada']);

    // Buscar parcelas dessa compra
    $stmtParcelas = $pdo->prepare("SELECT valorParcela, jurosAplicado FROM parcelas WHERE idCompra = ?");
    $stmtParcelas->execute([$idCompra]);
    $parcelas = $stmtParcelas->fetchAll(PDO::FETCH_ASSOC);

    $somaParcelas = 0;
    $valorBase = 0;

    foreach ($parcelas as $parcela) {
        $valorParcela = floatval($parcela['valorParcela']);
        $jurosAplicado = floatval($parcela['jurosAplicado']);

        $somaParcelas += $valorParcela;

        // Calcular valor sem juros, se jurosAplicado > 0
        if ($jurosAplicado > 0) {
            $valorBase += $valorParcela / (1 + ($jurosAplicado / 100));
        } else {
            $valorBase += $valorParcela;
        }
    }

    $valorTotalCompra = $entrada + $somaParcelas;
    $valorTotalGeral += $valorTotalCompra;

    $jurosDaCompra = $somaParcelas - $valorBase;
    $jurosTotalGeral += $jurosDaCompra;
}

// Calcular médias
$mediaTotal = $valorTotalGeral / $totalCompras;
$mediaJuros = $jurosTotalGeral / $totalCompras;

// Responder
echo json_encode([
    "count" => $totalCompras,
    "sum" => round($valorTotalGeral, 2),
    "avg" => round($mediaTotal, 2),
    "sumTx" => number_format($jurosTotalGeral, 2, '.', ''),
    "avgTx" => number_format($mediaJuros, 2, '.', '')
]);
