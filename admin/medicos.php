<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid mb-5">
    <?php require_once __DIR__ . '/../includes/admin_nav.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Profissionais</h2>
            <button class="btn btn-primary" onclick="openModal()"><i class="bi bi-plus-circle"></i> Novo Profissional</button>
        </div>

        <div id="alert-container"></div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Registro</th>
                            <th>Especialidade</th>
                            <th>Jornada</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <tr><td colspan="7" class="text-center py-4">Carregando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Novo Profissional</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="dataForm">
                    <input type="hidden" id="itemId">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="nome" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo *</label>
                            <select class="form-select" id="tipo" required>
                                <option value="medico">Médico</option>
                                <option value="dentista">Dentista</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Especialidade</label>
                            <select class="form-select" id="especialidade_id">
                                <option value="">Nenhuma</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">CRM</label>
                            <input type="text" class="form-control" id="crm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CRO</label>
                            <input type="text" class="form-control" id="cro">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jornada (h)</label>
                            <input type="number" class="form-control" id="jornada_horas">
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

<script src="/assets/js/admin.js"></script>
<script>
let items = [];
let especialidades = [];
const modal = new bootstrap.Modal(document.getElementById('formModal'));

async function loadData() {
    try {
        [items, especialidades] = await Promise.all([
            AdminAPI.getAll('medicos'),
            AdminAPI.getAll('especialidades')
        ]);
        
        // Populate select
        const selEsp = document.getElementById('especialidade_id');
        selEsp.innerHTML = '<option value="">Nenhuma</option>';
        especialidades.forEach(e => {
            selEsp.innerHTML += `<option value="${e.id}">${e.nome}</option>`;
        });

        renderTable();
    } catch (err) {
        showAlert(err.message, 'danger');
    }
}

function renderTable() {
    const tbody = document.getElementById('table-body');
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Nenhum profissional encontrado.</td></tr>';
        return;
    }
    
    tbody.innerHTML = items.map(item => {
        const reg = item.tipo === 'dentista' ? `CRO: ${item.cro||'-'}` : `CRM: ${item.crm||'-'}`;
        const tipoBadge = item.tipo === 'dentista' ? 'bg-info' : 'bg-primary';
        
        return `
        <tr>
            <td>${item.id}</td>
            <td class="fw-bold">${item.nome}</td>
            <td><span class="badge ${tipoBadge}">${item.tipo}</span></td>
            <td>${reg}</td>
            <td>${item.especialidade_nome || '—'}</td>
            <td>${item.jornada_horas ? item.jornada_horas + 'h' : '—'}</td>
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
    document.getElementById('modalTitle').textContent = 'Novo Profissional';

    if (id) {
        const item = items.find(i => i.id === id);
        if (item) {
            document.getElementById('itemId').value = item.id;
            document.getElementById('nome').value = item.nome;
            document.getElementById('tipo').value = item.tipo;
            document.getElementById('crm').value = item.crm;
            document.getElementById('cro').value = item.cro;
            document.getElementById('especialidade_id').value = item.especialidade_id || '';
            document.getElementById('jornada_horas').value = item.jornada_horas || '';
            document.getElementById('modalTitle').textContent = 'Editar Profissional';
        }
    }
    modal.show();
}

function editItem(id) { openModal(id); }

async function saveItem() {
    const id = document.getElementById('itemId').value;
    const payload = {
        nome: document.getElementById('nome').value,
        tipo: document.getElementById('tipo').value,
        crm: document.getElementById('crm').value,
        cro: document.getElementById('cro').value,
        especialidade_id: document.getElementById('especialidade_id').value || null,
        jornada_horas: document.getElementById('jornada_horas').value || null
    };
    
    try {
        if (id) {
            await AdminAPI.update('medicos', id, payload);
            showAlert('Profissional atualizado com sucesso!');
        } else {
            await AdminAPI.create('medicos', payload);
            showAlert('Profissional criado com sucesso!');
        }
        modal.hide();
        loadData();
    } catch (err) {
        alert(err.message);
    }
}

async function deleteItem(id) {
    if (!confirm('Tem certeza que deseja excluir este profissional?')) return;
    try {
        await AdminAPI.remove('medicos', id);
        showAlert('Profissional excluído com sucesso!');
        loadData();
    } catch (err) {
        showAlert(err.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', loadData);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
