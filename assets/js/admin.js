// assets/js/admin.js

const AdminAPI = {
    async request(url, method = 'GET', body = null) {
        const options = { method, headers: {} };
        if (body) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }
        const response = await fetch(url, options);
        if (response.status === 401) {
            window.location.href = '/login.php';
            throw new Error('Não autorizado');
        }
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || 'Erro na requisição');
        }
        return data;
    },

    async getAll(endpoint) { return this.request(`/api/${endpoint}.php`); },
    async create(endpoint, payload) { return this.request(`/api/${endpoint}.php`, 'POST', payload); },
    async update(endpoint, id, payload) { return this.request(`/api/${endpoint}.php?id=${id}`, 'PUT', payload); },
    async remove(endpoint, id) { return this.request(`/api/${endpoint}.php?id=${id}`, 'DELETE'); }
};

function showAlert(message, type = 'success', containerId = 'alert-container') {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    setTimeout(() => {
        const alertNode = container.querySelector('.alert');
        if (alertNode) {
            const alert = bootstrap.Alert.getOrCreateInstance(alertNode);
            alert.close();
        }
    }, 5000);
}
