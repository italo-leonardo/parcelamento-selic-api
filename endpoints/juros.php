<?php
require_once __DIR__ . '/../db/conexao.php';

function formatarData($dataISO) {
    $partes = explode('-', $dataISO); // yyyy-mm-dd
    return $partes[2] . '/' . $partes[1] . '/' . $partes[0]; // dd/mm/yyyy
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['dataInicio'], $data['dataFinal'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'JSON incompleto']);
    exit;
}

$dataInicio = $data['dataInicio'];
$dataFinal = $data['dataFinal'];

// Validações de datas
if ($dataInicio > $dataFinal || $dataInicio < '2010-01-01' || $dataFinal > date('Y-m-d')) {
    http_response_code(422);
    echo json_encode(['erro' => 'Intervalo de datas inválido']);
    exit;
}

// Verificar se intervalo é maior que 10 anos
$inicio = new DateTime($dataInicio);
$fim = new DateTime($dataFinal);
$diff = $inicio->diff($fim);

if ($diff->y > 10) {
    http_response_code(422);
    echo json_encode(['erro' => 'O intervalo entre as datas não pode ser superior a 10 anos']);
    exit;
}

// Converter formato para dd/mm/yyyy
$dataInicioFormatada = formatarData($dataInicio);
$dataFinalFormatada = formatarData($dataFinal);

// Consultar a API do Banco Central com série 11 (SELIC meta)
$baseUrl = 'https://api.bcb.gov.br/dados/serie/bcdata.sgs.11/dados';
$queryParams = http_build_query([
    'formato' => 'json',
    'dataInicial' => $dataInicioFormatada,
    'dataFinal' => $dataFinalFormatada
]);

$url = "$baseUrl?$queryParams";

$response = file_get_contents($url);

if ($response === FALSE) {
    http_response_code(500);
    echo json_encode(['erro' => '500 - Erro ao consultar a API da SELIC']);
    exit;
}

$dados = json_decode($response, true);

// Calcular a média da taxa SELIC
$total = 0;
$count = 0;

foreach ($dados as $dia) {
    $valor = str_replace(',', '.', $dia['valor']); // corrigir vírgula
    $total += floatval($valor);
    $count++;
}

if ($count == 0) {
    http_response_code(422);
    echo json_encode(['erro' => '422 - Nenhum dado de taxa SELIC encontrado no período informado']);
    exit;
}

$media = $total / $count;

// Atualizar taxa no banco (convertendo para decimal: ex: 13.65 → 0.1365)
$stmt = $pdo->prepare("UPDATE juros SET taxa = ?, ultimaAtualizacao = NOW() WHERE id = 1");
$stmt->execute([$media / 100]);

http_response_code(200);
echo json_encode([
    'novaTaxa' => round($media, 2) . '%',
    'quantidadeDias' => $count,
    'periodo' => "$dataInicio até $dataFinal"
]);
