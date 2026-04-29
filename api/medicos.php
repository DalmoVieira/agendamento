<?php
// api/medicos.php
require_once __DIR__ . '/../config/database.php';
session_start();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT m.*, e.nome AS especialidade_nome
            FROM medicos m
            LEFT JOIN especialidades e ON m.especialidade_id = e.id
            ORDER BY m.tipo, m.nome
        ");
        echo json_encode($stmt->fetchAll());
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

    $nome = $data['nome'] ?? '';
    $tipo = $data['tipo'] ?? 'medico';
    $crm = $data['crm'] ?? '';
    $cro = $data['cro'] ?? '';
    $especialidade_id = !empty($data['especialidade_id']) ? $data['especialidade_id'] : null;
    $jornada_horas = !empty($data['jornada_horas']) ? $data['jornada_horas'] : null;

    if (empty(trim($nome))) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome é obrigatório']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO medicos (nome, tipo, crm, cro, especialidade_id, jornada_horas) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([trim($nome), $tipo, $crm, $cro, $especialidade_id, $jornada_horas]);
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

    $nome = $data['nome'] ?? '';
    $tipo = $data['tipo'] ?? 'medico';
    $crm = $data['crm'] ?? '';
    $cro = $data['cro'] ?? '';
    $especialidade_id = !empty($data['especialidade_id']) ? $data['especialidade_id'] : null;
    $jornada_horas = !empty($data['jornada_horas']) ? $data['jornada_horas'] : null;

    if (empty(trim($nome))) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome é obrigatório']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE medicos SET nome=?, tipo=?, crm=?, cro=?, especialidade_id=?, jornada_horas=? WHERE id=?");
        $stmt->execute([trim($nome), $tipo, $crm, $cro, $especialidade_id, $jornada_horas, $id]);
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
        $stmt = $pdo->prepare("DELETE FROM medicos WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
