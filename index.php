<?php
require_once __DIR__ . '/includes/header.php';
?>

<main class="container my-5">
    <ul class="nav nav-pills nav-fill mb-4 bg-white shadow-sm rounded p-2" id="publicTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="escalas-tab" data-bs-toggle="tab" data-bs-target="#escalas" type="button" role="tab" aria-controls="escalas" aria-selected="true">
                <i class="bi bi-calendar-week"></i> Escalas de Atendimento
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="atendimentos-tab" data-bs-toggle="tab" data-bs-target="#atendimentos" type="button" role="tab" aria-controls="atendimentos" aria-selected="false">
                <i class="bi bi-clipboard2-data"></i> Atendimentos Realizados
            </button>
        </li>
    </ul>

    <div class="tab-content" id="publicTabsContent">
        <!-- ABA ESCALAS -->
        <div class="tab-pane fade show active" id="escalas" role="tabpanel" aria-labelledby="escalas-tab">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body bg-light rounded">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Tipo de Profissional</label>
                            <select id="filtro-tipo" class="form-select">
                                <option value="">Todos</option>
                                <option value="medico">Médico</option>
                                <option value="dentista">Odontológico</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Unidade de Saúde</label>
                            <select id="filtro-unidade" class="form-select">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Especialidade</label>
                            <select id="filtro-especialidade" class="form-select">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Profissional</label>
                            <select id="filtro-medico" class="form-select">
                                <option value="">Todos</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="unidade-info" class="alert alert-info d-none mb-4"></div>

            <div id="escalas-container">
                <div class="text-center text-muted py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Carregando escalas...</p>
                </div>
            </div>
        </div>

        <!-- ABA ATENDIMENTOS -->
        <div class="tab-pane fade" id="atendimentos" role="tabpanel" aria-labelledby="atendimentos-tab">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body bg-light rounded">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Unidade de Saúde</label>
                            <select id="filtro-atend-unidade" class="form-select">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Mês</label>
                            <select id="filtro-atend-mes" class="form-select">
                                <option value="">Todos</option>
                                <option value="1">Janeiro</option>
                                <option value="2">Fevereiro</option>
                                <option value="3">Março</option>
                                <option value="4">Abril</option>
                                <option value="5">Maio</option>
                                <option value="6">Junho</option>
                                <option value="7">Julho</option>
                                <option value="8">Agosto</option>
                                <option value="9">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold">Ano</label>
                            <input type="number" id="filtro-atend-ano" class="form-select" min="2020" max="2099" placeholder="Ex: 2024">
                        </div>
                        <div class="col-md-2">
                            <button id="btn-limpar-atend" class="btn btn-outline-secondary w-100">Limpar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="total-atendimentos" class="mb-3 fw-bold text-primary fs-5 d-none"></div>

            <div class="table-responsive shadow-sm rounded bg-white">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Profissional</th>
                            <th>Especialidade</th>
                            <th>Unidade</th>
                            <th class="text-center">Qtd</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody id="atendimentos-tbody">
                        <tr><td colspan="6" class="text-center text-muted py-4">Utilize os filtros acima para buscar atendimentos ou aguarde o carregamento...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="/assets/js/main.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
