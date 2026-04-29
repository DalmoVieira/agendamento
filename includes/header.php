<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);

// Detecção automática da base do sistema para suportar subpastas (ex: /agendamento)
$baseUrl = '';
if (strpos($_SERVER['REQUEST_URI'], '/agendamento') !== false) {
    $baseUrl = '/agendamento';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prefeitura de Rio Claro RJ - Secretaria Municipal de Saúde</title>
    <!-- Favicon -->
    <link rel="icon" href="https://rioclaro.rj.gov.br/wp-content/uploads/2025/02/cropped-favicon-pmrcrj-32x32.jpeg" sizes="32x32">
    <link rel="icon" href="https://rioclaro.rj.gov.br/wp-content/uploads/2025/02/cropped-favicon-pmrcrj-192x192.jpeg" sizes="192x192">
    <!-- Bootstrap CSS e JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/style.css">
    <script>window.baseUrl = '<?php echo $baseUrl; ?>';</script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="<?php echo $baseUrl ?: '/'; ?>" class="d-flex align-items-center text-white text-decoration-none">
                    <img src="https://rioclaro.rj.gov.br/wp-content/uploads/2024/10/logo-blank.png" alt="Logo" class="me-3" style="max-height: 60px;">
                    <div>
                        <h1 class="h4 mb-0">Prefeitura de Rio Claro RJ</h1>
                        <h2 class="h6 mb-0 text-white-50">Secretaria Municipal de Saúde</h2>
                    </div>
                </a>
            </div>
            <div>
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo $baseUrl; ?>/admin/index.php" class="btn btn-outline-light btn-sm me-2">Painel Admin</a>
                    <a href="<?php echo $baseUrl; ?>/api/auth.php?action=logout" class="btn btn-light btn-sm fw-bold">Sair</a>
                <?php else: ?>
                    <a href="<?php echo $baseUrl; ?>/login.php" class="btn btn-light btn-sm fw-bold">Área do Servidor</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
