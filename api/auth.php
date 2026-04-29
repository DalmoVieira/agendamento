<?php
// api/auth.php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';

    if (empty($username) || empty($password) || $captcha === '') {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios.']);
        exit;
    }

    if (!isset($_SESSION['captcha_answer']) || (int)$captcha !== $_SESSION['captcha_answer']) {
        echo json_encode(['success' => false, 'message' => 'Resultado da soma incorreto. Tente novamente.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuário ou senha incorretos.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro interno do servidor.']);
    }
    exit;
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit;
}

echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
exit;
