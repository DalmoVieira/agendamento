<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid mb-5">
    <?php require_once __DIR__ . '/../includes/admin_nav.php'; ?>

    <div class="container">
        <h2 class="mb-4">Bem-vindo ao Painel de Controle</h2>
        <p class="text-muted">Utilize o menu acima para gerenciar os dados do sistema.</p>

        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-person-badge display-4 text-primary mb-3"></i>
                        <h5 class="card-title">Profissionais</h5>
                        <p class="card-text text-muted">Gerencie médicos e dentistas.</p>
                        <a href="/admin/medicos.php" class="btn btn-outline-primary mt-2">Acessar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-calendar-range display-4 text-success mb-3"></i>
                        <h5 class="card-title">Escalas</h5>
                        <p class="card-text text-muted">Configure os horários de atendimento.</p>
                        <a href="/admin/escalas.php" class="btn btn-outline-success mt-2">Acessar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-clipboard2-data display-4 text-warning mb-3"></i>
                        <h5 class="card-title">Atendimentos</h5>
                        <p class="card-text text-muted">Registre os atendimentos diários.</p>
                        <a href="/admin/atendimentos.php" class="btn btn-outline-warning mt-2">Acessar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
