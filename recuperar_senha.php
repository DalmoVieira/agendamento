<?php
require_once __DIR__ . '/includes/header.php';
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow border-0" style="width: 100%; max-width: 500px; border-radius: 1rem;">
        <div class="card-body p-4 p-md-5 text-center">
            <div class="mb-4">
                <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
            </div>
            <h3 class="mb-4 fw-bold text-primary">Recuperação de Senha</h3>
            
            <p class="text-muted mb-4">
                Para garantir a segurança dos dados da Secretaria Municipal de Saúde, a redefinição de senhas para o sistema de escalas deve ser feita diretamente com o setor de Tecnologia.
            </p>
            
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Entre em contato com o administrador do sistema</strong> solicitando uma nova senha provisória de acesso.
            </div>

            <a href="<?php echo $baseUrl; ?>/login.php" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm mt-3">
                <i class="bi bi-arrow-left me-2"></i> Voltar para o Login
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
