<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prefeitura de Rio Claro RJ - Secretaria Municipal de Saúde</title>
    <!-- Bootstrap CSS e JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="https://rioclaro.rj.gov.br/wp-content/uploads/2024/10/logo-blank.png" alt="Logo" class="me-3" style="max-height: 60px;">
                <div>
                    <h1 class="h4 mb-0">Prefeitura de Rio Claro RJ</h1>
                    <h2 class="h6 mb-0 text-white-50">Secretaria Municipal de Saúde</h2>
                </div>
            </div>
            <div>
                <?php if ($isLoggedIn): ?>
                    <a href="/admin/index.php" class="btn btn-outline-light btn-sm me-2">Painel Admin</a>
                    <a href="/api/auth.php?action=logout" class="btn btn-light btn-sm fw-bold">Sair</a>
                <?php else: ?>
                    <a href="/login.php" class="btn btn-light btn-sm fw-bold">Área do Servidor</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
