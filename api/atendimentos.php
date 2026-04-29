<?php
// api/atendimentos.php
require_once __DIR__ . '/../config/database.php';
session_start();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $medico_id = $_GET['medico_id'] ?? null;
    $unidade_id = $_GET['unidade_id'] ?? null;
    $mes = $_GET['mes'] ?? null;
    $ano = $_GET['ano'] ?? null;

    $conditions = [];
    $params = [];

    if ($medico_id) {
        $conditions[] = "a.medico_id = ?";
        $params[] = $medico_id;
    }
    if ($unidade_id) {
        $conditions[] = "a.unidade_id = ?";
        $params[] = $unidade_id;
    }
    if ($mes) {
        $conditions[] = "MONTH(a.data) = ?";
        $params[] = $mes;
    }
    if ($ano) {
        $conditions[] = "YEAR(a.data) = ?";
        $params[] = $ano;
    }

    $whereClause = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

    try {
        $sql = "
            SELECT
                a.id, a.data, a.quantidade, a.observacoes,
                a.medico_id, m.nome AS medico_nome, m.tipo AS medico_tipo,
                e.nome AS especialidade_nome,
                a.unidade_id, u.nome AS unidade_nome
            FROM atendimentos a
            JOIN medicos m ON a.medico_id = m.id
            LEFT JOIN especialidades e ON m.especialidade_id = e.id
            JOIN unidades u ON a.unidade_id = u.id
            $whereClause
            ORDER BY a.data DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $atendimentos = $stmt->fetchAll();
        echo json_encode($atendimentos);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data)) $data = $_POST;

    $data_atendimento = $data['data'] ?? null;
    $medico_id = $data['medico_id'] ?? null;
    $unidade_id = $data['unidade_id'] ?? null;
    $quantidade = $data['quantidade'] ?? 1;
    $observacoes = $data['observacoes'] ?? null;

    if (!$data_atendimento || !$medico_id || !$unidade_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Data, profissional e unidade são obrigatórios']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO atendimentos (data, medico_id, unidade_id, quantidade, observacoes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data_atendimento, $medico_id, $unidade_id, $quantidade, $observacoes]);
        echo json_encode(['id' => $pdo->lastInsertId(), 'success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $_GET['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID não fornecido']);
        exit;
    }

    $data_atendimento = $data['data'] ?? null;
    $medico_id = $data['medico_id'] ?? null;
    $unidade_id = $data['unidade_id'] ?? null;
    $quantidade = $data['quantidade'] ?? 1;
    $observacoes = $data['observacoes'] ?? null;

    if (!$data_atendimento || !$medico_id || !$unidade_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Data, profissional e unidade são obrigatórios']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE atendimentos SET data=?, medico_id=?, unidade_id=?, quantidade=?, observacoes=? WHERE id=?");
        $stmt->execute([$data_atendimento, $medico_id, $unidade_id, $quantidade, $observacoes, $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID não fornecido']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM atendimentos WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
