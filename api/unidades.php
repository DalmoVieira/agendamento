<?php
// api/unidades.php
require_once __DIR__ . '/../config/database.php';
session_start();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM unidades ORDER BY nome");
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
    $endereco = $data['endereco'] ?? '';
    $telefone = $data['telefone'] ?? '';

    if (empty(trim($nome))) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome é obrigatório']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO unidades (nome, endereco, telefone) VALUES (?, ?, ?)");
        $stmt->execute([trim($nome), $endereco, $telefone]);
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
    $nome = $data['nome'] ?? '';
    $endereco = $data['endereco'] ?? '';
    $telefone = $data['telefone'] ?? '';

    if (!$id || empty(trim($nome))) {
        http_response_code(400);
        echo json_encode(['error' => 'ID e Nome são obrigatórios']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE unidades SET nome=?, endereco=?, telefone=? WHERE id=?");
        $stmt->execute([trim($nome), $endereco, $telefone, $id]);
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
        $stmt = $pdo->prepare("DELETE FROM unidades WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
