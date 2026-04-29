<?php
// api/escalas.php
require_once __DIR__ . '/../config/database.php';
session_start();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT
                e.id, e.dia_semana, e.horario_inicio, e.horario_fim,
                m.id AS medico_id, m.nome AS medico_nome, m.tipo AS medico_tipo,
                m.crm, m.cro, m.jornada_horas,
                esp.id AS especialidade_id, esp.nome AS especialidade_nome,
                u.id AS unidade_id, u.nome AS unidade_nome
            FROM escalas e
            JOIN medicos m ON e.medico_id = m.id
            LEFT JOIN especialidades esp ON m.especialidade_id = esp.id
            JOIN unidades u ON e.unidade_id = u.id
            ORDER BY u.nome,
                FIELD(e.dia_semana, 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'),
                e.horario_inicio
        ");
        $escalas = $stmt->fetchAll();
        echo json_encode($escalas);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Para métodos de escrita (POST, PUT, DELETE), exige sessão
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

// POST: Criar
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data)) {
        $data = $_POST;
    }

    $medico_id = $data['medico_id'] ?? null;
    $unidade_id = $data['unidade_id'] ?? null;
    $dia_semana = $data['dia_semana'] ?? null;
    $horario_inicio = $data['horario_inicio'] ?? null;
    $horario_fim = $data['horario_fim'] ?? null;

    if (!$medico_id || !$unidade_id || !$dia_semana || !$horario_inicio || !$horario_fim) {
        http_response_code(400);
        echo json_encode(['error' => 'Todos os campos são obrigatórios']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO escalas (medico_id, unidade_id, dia_semana, horario_inicio, horario_fim) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$medico_id, $unidade_id, $dia_semana, $horario_inicio, $horario_fim]);
        echo json_encode(['id' => $pdo->lastInsertId(), 'success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// PUT/DELETE handled via POST action or proper methods... 
// PHP usually doesn't populate $_POST for PUT/DELETE, we use file_get_contents.
// Since it's a full REST API, let's parse raw body for PUT.
if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $_GET['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID não fornecido']);
        exit;
    }

    $medico_id = $data['medico_id'] ?? null;
    $unidade_id = $data['unidade_id'] ?? null;
    $dia_semana = $data['dia_semana'] ?? null;
    $horario_inicio = $data['horario_inicio'] ?? null;
    $horario_fim = $data['horario_fim'] ?? null;

    if (!$medico_id || !$unidade_id || !$dia_semana || !$horario_inicio || !$horario_fim) {
        http_response_code(400);
        echo json_encode(['error' => 'Todos os campos são obrigatórios']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE escalas SET medico_id=?, unidade_id=?, dia_semana=?, horario_inicio=?, horario_fim=? WHERE id=?");
        $stmt->execute([$medico_id, $unidade_id, $dia_semana, $horario_inicio, $horario_fim, $id]);
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
        $stmt = $pdo->prepare("DELETE FROM escalas WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
