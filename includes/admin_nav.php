<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo $baseUrl; ?>/admin/index.php">Painel Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/admin/especialidades.php"><i class="bi bi-tags"></i> Especialidades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/admin/unidades.php"><i class="bi bi-hospital"></i> Unidades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/admin/medicos.php"><i class="bi bi-person-badge"></i> Profissionais</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/admin/escalas.php"><i class="bi bi-calendar-range"></i> Escalas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $baseUrl; ?>/admin/atendimentos.php"><i class="bi bi-clipboard2-data"></i> Atendimentos</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
