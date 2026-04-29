<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid mb-5">
    <?php require_once __DIR__ . '/../includes/admin_nav.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Escalas</h2>
            <button class="btn btn-primary" onclick="openModal()"><i class="bi bi-plus-circle"></i> Nova Escala</button>
        </div>

        <div id="alert-container"></div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Profissional</th>
                                <th>Especialidade</th>
                                <th>Unidade</th>
                                <th>Dia</th>
                                <th>Horário</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <tr><td colspan="6" class="text-center py-4">Carregando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nova Escala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="dataForm">
                    <input type="hidden" id="itemId">
                    
                    <div class="mb-3">
                        <label class="form-label">Profissional *</label>
                        <select class="form-select" id="medico_id" required></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Unidade de Saúde *</label>
                        <select class="form-select" id="unidade_id" required></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dia da Semana *</label>
                        <select class="form-select" id="dia_semana" required>
                            <option value="Segunda-feira">Segunda-feira</option>
                            <option value="Terça-feira">Terça-feira</option>
                            <option value="Quarta-feira">Quarta-feira</option>
                            <option value="Quinta-feira">Quinta-feira</option>
                            <option value="Sexta-feira">Sexta-feira</option>
                            <option value="Sábado">Sábado</option>
                            <option value="Domingo">Domingo</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Início *</label>
                            <input type="time" class="form-control" id="horario_inicio" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Fim *</label>
                            <input type="time" class="form-control" id="horario_fim" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveItem()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>/assets/js/admin.js"></script>
<script>
let items = [];
let medicos = [];
let unidades = [];
const modal = new bootstrap.Modal(document.getElementById('formModal'));

async function loadData() {
    try {
        [items, medicos, unidades] = await Promise.all([
            AdminAPI.getAll('escalas'),
            AdminAPI.getAll('medicos'),
            AdminAPI.getAll('unidades')
        ]);
        
        const selMed = document.getElementById('medico_id');
        selMed.innerHTML = '<option value="">Selecione...</option>';
        medicos.forEach(m => selMed.innerHTML += `<option value="${m.id}">${m.nome} (${m.especialidade_nome||'Clínico'})</option>`);

        const selUni = document.getElementById('unidade_id');
        selUni.innerHTML = '<option value="">Selecione...</option>';
        unidades.forEach(u => selUni.innerHTML += `<option value="${u.id}">${u.nome}</option>`);

        renderTable();
    } catch (err) {
        showAlert(err.message, 'danger');
    }
}

function renderTable() {
    const tbody = document.getElementById('table-body');
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Nenhuma escala encontrada.</td></tr>';
        return;
    }
    
    tbody.innerHTML = items.map(item => `
        <tr>
            <td class="fw-bold">${item.medico_nome}</td>
            <td>${item.especialidade_nome || '—'}</td>
            <td>${item.unidade_nome}</td>
            <td>${item.dia_semana}</td>
            <td>${item.horario_inicio} – ${item.horario_fim}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary me-1" onclick="editItem(${item.id})"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem(${item.id})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

function openModal(id = null) {
    document.getElementById('dataForm').reset();
    document.getElementById('itemId').value = '';
    document.getElementById('modalTitle').textContent = 'Nova Escala';

    if (id) {
        const item = items.find(i => i.id === id);
        if (item) {
            document.getElementById('itemId').value = item.id;
            document.getElementById('medico_id').value = item.medico_id;
            document.getElementById('unidade_id').value = item.unidade_id;
            document.getElementById('dia_semana').value = item.dia_semana;
            document.getElementById('horario_inicio').value = item.horario_inicio;
            document.getElementById('horario_fim').value = item.horario_fim;
            document.getElementById('modalTitle').textContent = 'Editar Escala';
        }
    }
    modal.show();
}

function editItem(id) { openModal(id); }

async function saveItem() {
    const id = document.getElementById('itemId').value;
    const payload = {
        medico_id: document.getElementById('medico_id').value,
        unidade_id: document.getElementById('unidade_id').value,
        dia_semana: document.getElementById('dia_semana').value,
        horario_inicio: document.getElementById('horario_inicio').value,
        horario_fim: document.getElementById('horario_fim').value
    };

    if(!payload.medico_id || !payload.unidade_id || !payload.horario_inicio || !payload.horario_fim) {
        alert("Preencha todos os campos obrigatórios");
        return;
    }
    
    try {
        if (id) {
            await AdminAPI.update('escalas', id, payload);
            showAlert('Escala atualizada com sucesso!');
        } else {
            await AdminAPI.create('escalas', payload);
            showAlert('Escala criada com sucesso!');
        }
        modal.hide();
        loadData();
    } catch (err) {
        alert(err.message);
    }
}

async function deleteItem(id) {
    if (!confirm('Tem certeza que deseja excluir esta escala?')) return;
    try {
        await AdminAPI.remove('escalas', id);
        showAlert('Escala excluída com sucesso!');
        loadData();
    } catch (err) {
        showAlert(err.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', loadData);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
