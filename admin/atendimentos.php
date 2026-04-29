<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid mb-5">
    <?php require_once __DIR__ . '/../includes/admin_nav.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Atendimentos</h2>
            <button class="btn btn-primary" onclick="openModal()"><i class="bi bi-plus-circle"></i> Novo Atendimento</button>
        </div>

        <div id="alert-container"></div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Profissional</th>
                                <th>Unidade</th>
                                <th>Quantidade</th>
                                <th>Observações</th>
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
                <h5 class="modal-title" id="modalTitle">Novo Atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="dataForm">
                    <input type="hidden" id="itemId">
                    
                    <div class="mb-3">
                        <label class="form-label">Data do Atendimento *</label>
                        <input type="date" class="form-control" id="data" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profissional *</label>
                        <select class="form-select" id="medico_id" required></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Unidade de Saúde *</label>
                        <select class="form-select" id="unidade_id" required></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantidade *</label>
                        <input type="number" class="form-control" id="quantidade" value="1" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" rows="2"></textarea>
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

<script src="/assets/js/admin.js"></script>
<script>
let items = [];
let medicos = [];
let unidades = [];
const modal = new bootstrap.Modal(document.getElementById('formModal'));

async function loadData() {
    try {
        [items, medicos, unidades] = await Promise.all([
            AdminAPI.getAll('atendimentos'),
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
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Nenhum atendimento encontrado.</td></tr>';
        return;
    }
    
    tbody.innerHTML = items.map(item => {
        // format date YYYY-MM-DD
        const [y, m, d] = item.data.split('-');
        const dataForm = `${d}/${m}/${y}`;
        
        return `
        <tr>
            <td class="fw-bold">${dataForm}</td>
            <td>${item.medico_nome} <small class="text-muted d-block">${item.especialidade_nome||''}</small></td>
            <td>${item.unidade_nome}</td>
            <td class="text-center">${item.quantidade}</td>
            <td class="text-truncate" style="max-width: 200px;">${item.observacoes || '—'}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary me-1" onclick="editItem(${item.id})"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem(${item.id})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `}).join('');
}

function openModal(id = null) {
    document.getElementById('dataForm').reset();
    document.getElementById('itemId').value = '';
    document.getElementById('modalTitle').textContent = 'Novo Atendimento';

    if (id) {
        const item = items.find(i => i.id === id);
        if (item) {
            document.getElementById('itemId').value = item.id;
            document.getElementById('data').value = item.data;
            document.getElementById('medico_id').value = item.medico_id;
            document.getElementById('unidade_id').value = item.unidade_id;
            document.getElementById('quantidade').value = item.quantidade;
            document.getElementById('observacoes').value = item.observacoes;
            document.getElementById('modalTitle').textContent = 'Editar Atendimento';
        }
    } else {
        document.getElementById('data').value = new Date().toISOString().split('T')[0];
    }
    modal.show();
}

function editItem(id) { openModal(id); }

async function saveItem() {
    const id = document.getElementById('itemId').value;
    const payload = {
        data: document.getElementById('data').value,
        medico_id: document.getElementById('medico_id').value,
        unidade_id: document.getElementById('unidade_id').value,
        quantidade: document.getElementById('quantidade').value,
        observacoes: document.getElementById('observacoes').value
    };

    if(!payload.data || !payload.medico_id || !payload.unidade_id) {
        alert("Preencha todos os campos obrigatórios");
        return;
    }
    
    try {
        if (id) {
            await AdminAPI.update('atendimentos', id, payload);
            showAlert('Atendimento atualizado com sucesso!');
        } else {
            await AdminAPI.create('atendimentos', payload);
            showAlert('Atendimento criado com sucesso!');
        }
        modal.hide();
        loadData();
    } catch (err) {
        alert(err.message);
    }
}

async function deleteItem(id) {
    if (!confirm('Tem certeza que deseja excluir este atendimento?')) return;
    try {
        await AdminAPI.remove('atendimentos', id);
        showAlert('Atendimento excluído com sucesso!');
        loadData();
    } catch (err) {
        showAlert(err.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', loadData);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
