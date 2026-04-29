<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid mb-5">
    <?php require_once __DIR__ . '/../includes/admin_nav.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Especialidades</h2>
            <button class="btn btn-primary" onclick="openModal()"><i class="bi bi-plus-circle"></i> Nova Especialidade</button>
        </div>

        <div id="alert-container"></div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <tr><td colspan="3" class="text-center py-4">Carregando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="formModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nova Especialidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="dataForm">
                    <input type="hidden" id="itemId">
                    <div class="mb-3">
                        <label class="form-label">Nome da Especialidade</label>
                        <input type="text" class="form-control" id="nome" required>
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
const modal = new bootstrap.Modal(document.getElementById('formModal'));

async function loadItems() {
    try {
        items = await AdminAPI.getAll('especialidades');
        renderTable();
    } catch (err) {
        showAlert(err.message, 'danger');
    }
}

function renderTable() {
    const tbody = document.getElementById('table-body');
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Nenhuma especialidade encontrada.</td></tr>';
        return;
    }
    
    tbody.innerHTML = items.map(item => `
        <tr>
            <td>${item.id}</td>
            <td class="fw-bold">${item.nome}</td>
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
    document.getElementById('modalTitle').textContent = 'Nova Especialidade';

    if (id) {
        const item = items.find(i => i.id === id);
        if (item) {
            document.getElementById('itemId').value = item.id;
            document.getElementById('nome').value = item.nome;
            document.getElementById('modalTitle').textContent = 'Editar Especialidade';
        }
    }
    modal.show();
}

function editItem(id) { openModal(id); }

async function saveItem() {
    const id = document.getElementById('itemId').value;
    const payload = { nome: document.getElementById('nome').value };
    
    try {
        if (id) {
            await AdminAPI.update('especialidades', id, payload);
            showAlert('Especialidade atualizada com sucesso!');
        } else {
            await AdminAPI.create('especialidades', payload);
            showAlert('Especialidade criada com sucesso!');
        }
        modal.hide();
        loadItems();
    } catch (err) {
        alert(err.message);
    }
}

async function deleteItem(id) {
    if (!confirm('Tem certeza que deseja excluir esta especialidade?')) return;
    try {
        await AdminAPI.remove('especialidades', id);
        showAlert('Especialidade excluída com sucesso!');
        loadItems();
    } catch (err) {
        showAlert(err.message, 'danger');
    }
}

document.addEventListener('DOMContentLoaded', loadItems);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
