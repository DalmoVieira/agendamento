// assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    let escalasRaw = [];
    let unidadesRaw = [];

    const DOM = {
        escalasContainer: document.getElementById('escalas-container'),
        unidadeInfo: document.getElementById('unidade-info'),
        fTipo: document.getElementById('filtro-tipo'),
        fUnidade: document.getElementById('filtro-unidade'),
        fEspecialidade: document.getElementById('filtro-especialidade'),
        fMedico: document.getElementById('filtro-medico'),

        fAtendUnidade: document.getElementById('filtro-atend-unidade'),
        fAtendMes: document.getElementById('filtro-atend-mes'),
        fAtendAno: document.getElementById('filtro-atend-ano'),
        btnLimparAtend: document.getElementById('btn-limpar-atend'),
        atendBody: document.getElementById('atendimentos-tbody'),
        totalAtend: document.getElementById('total-atendimentos')
    };

    // Load initial data
    Promise.all([
        fetch(window.baseUrl + '/api/escalas.php').then(r => r.json()),
        fetch(window.baseUrl + '/api/unidades.php').then(r => r.json())
    ]).then(([escalas, unidades]) => {
        escalasRaw = escalas;
        unidadesRaw = unidades;

        populateSelect(DOM.fUnidade, unidades, 'id', 'nome');
        populateSelect(DOM.fAtendUnidade, unidades, 'id', 'nome');

        updateEscalasFilters();
        renderEscalas();
        fetchAtendimentos(); // initial load
    }).catch(err => {
        DOM.escalasContainer.innerHTML = `<div class="alert alert-danger">Erro ao carregar dados: ${err.message}</div>`;
    });

    function populateSelect(selectEl, data, valKey, textKey) {
        // Keep first option
        const first = selectEl.options[0];
        selectEl.innerHTML = '';
        selectEl.appendChild(first);
        
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[valKey];
            opt.textContent = item[textKey];
            selectEl.appendChild(opt);
        });
    }

    // Escalas Logic
    function updateEscalasFilters() {
        const tipo = DOM.fTipo.value;
        const unidade = DOM.fUnidade.value;
        const especialidade = DOM.fEspecialidade.value;

        // Base para especialidades e médicos baseados na unidade selecionada
        let base = unidade ? escalasRaw.filter(e => String(e.unidade_id) === unidade) : escalasRaw;
        if (tipo) base = base.filter(e => e.medico_tipo === tipo);

        // Especialidades map
        const espMap = new Map();
        base.forEach(e => {
            if (e.especialidade_id) espMap.set(String(e.especialidade_id), e.especialidade_nome);
        });
        const espList = Array.from(espMap, ([id, nome]) => ({id, nome})).sort((a,b) => a.nome.localeCompare(b.nome));
        
        const oldEsp = DOM.fEspecialidade.value;
        populateSelect(DOM.fEspecialidade, espList, 'id', 'nome');
        if(espList.some(x => x.id === oldEsp)) DOM.fEspecialidade.value = oldEsp;

        // Medicos map
        if (DOM.fEspecialidade.value) {
            base = base.filter(e => String(e.especialidade_id) === DOM.fEspecialidade.value);
        }
        const medMap = new Map();
        base.forEach(e => {
            medMap.set(String(e.medico_id), e.medico_nome);
        });
        const medList = Array.from(medMap, ([id, nome]) => ({id, nome})).sort((a,b) => a.nome.localeCompare(b.nome));
        
        const oldMed = DOM.fMedico.value;
        populateSelect(DOM.fMedico, medList, 'id', 'nome');
        if(medList.some(x => x.id === oldMed)) DOM.fMedico.value = oldMed;
    }

    function renderEscalas() {
        const tipo = DOM.fTipo.value;
        const unidade = DOM.fUnidade.value;
        const especialidade = DOM.fEspecialidade.value;
        const medico = DOM.fMedico.value;

        let filtered = escalasRaw;
        if (tipo) filtered = filtered.filter(e => e.medico_tipo === tipo);
        if (unidade) filtered = filtered.filter(e => String(e.unidade_id) === unidade);
        if (especialidade) filtered = filtered.filter(e => String(e.especialidade_id) === especialidade);
        if (medico) filtered = filtered.filter(e => String(e.medico_id) === medico);

        // Group by day
        const days = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'];
        const byDay = {};
        days.forEach(d => {
            const items = filtered.filter(e => e.dia_semana === d);
            if (items.length > 0) {
                byDay[d] = items.sort((a,b) => a.horario_inicio.localeCompare(b.horario_inicio));
            }
        });

        // Update Unidade Info
        if (unidade) {
            const u = unidadesRaw.find(x => String(x.id) === unidade);
            if (u) {
                DOM.unidadeInfo.innerHTML = `<strong>${u.nome}</strong> ${u.endereco ? '— '+u.endereco : ''} ${u.telefone ? '| Tel: '+u.telefone : ''}`;
                DOM.unidadeInfo.classList.remove('d-none');
            }
        } else {
            DOM.unidadeInfo.classList.add('d-none');
        }

        if (Object.keys(byDay).length === 0) {
            DOM.escalasContainer.innerHTML = '<div class="alert alert-warning text-center">Nenhuma escala encontrada para os filtros selecionados.</div>';
            return;
        }

        let html = '';
        for (const [day, items] of Object.entries(byDay)) {
            html += `<h4 class="mt-4 mb-3 text-primary border-bottom pb-2">${day}</h4>`;
            html += `<div class="row g-3">`;
            items.forEach(item => {
                const tipoBadge = item.medico_tipo === 'dentista' ? 'badge-dentista' : 'badge-medico';
                const tipoText = item.medico_tipo === 'dentista' ? 'Dentista' : 'Médico';
                const docCardClass = item.medico_tipo === 'dentista' ? 'doctor-card-dentista' : 'doctor-card-medico';
                const reg = item.medico_tipo === 'dentista' ? (item.cro ? `CRO: ${item.cro}` : '') : (item.crm ? `CRM: ${item.crm}` : '');
                
                html += `
                <div class="col-md-6 col-lg-4">
                    <div class="doctor-card ${docCardClass} d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="mb-0 fw-bold">${item.medico_nome}</h5>
                            <span class="${tipoBadge}">${tipoText}</span>
                        </div>
                        ${reg ? `<small class="text-muted mb-1">${reg}</small>` : ''}
                        <div class="text-primary small mb-2 fw-semibold">${item.especialidade_nome || '—'}</div>
                        ${!unidade ? `<div class="small mb-1"><i class="bi bi-hospital"></i> ${item.unidade_nome}</div>` : ''}
                        <div class="mt-auto pt-2 border-top">
                            <div class="d-flex justify-content-between">
                                <span><i class="bi bi-clock"></i> ${item.horario_inicio} – ${item.horario_fim}</span>
                                ${item.jornada_horas ? `<small class="text-muted">${item.jornada_horas}h/sem</small>` : ''}
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            html += `</div>`;
        }
        DOM.escalasContainer.innerHTML = html;
    }

    [DOM.fTipo, DOM.fUnidade, DOM.fEspecialidade, DOM.fMedico].forEach(el => {
        el.addEventListener('change', () => {
            // Se mudar tipo ou unidade, reseta esp e medico
            if(el === DOM.fTipo || el === DOM.fUnidade) {
                DOM.fEspecialidade.value = '';
                DOM.fMedico.value = '';
            }
            if(el === DOM.fEspecialidade) {
                DOM.fMedico.value = '';
            }
            updateEscalasFilters();
            renderEscalas();
        });
    });

    // Atendimentos Logic
    async function fetchAtendimentos() {
        const params = new URLSearchParams();
        if (DOM.fAtendUnidade.value) params.append('unidade_id', DOM.fAtendUnidade.value);
        if (DOM.fAtendMes.value) params.append('mes', DOM.fAtendMes.value);
        if (DOM.fAtendAno.value) params.append('ano', DOM.fAtendAno.value);

        DOM.atendBody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Carregando...</td></tr>';
        
        try {
            const res = await fetch(window.baseUrl + `/api/atendimentos.php?${params.toString()}`);
            const data = await res.json();
            
            if (data.length === 0) {
                DOM.atendBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Nenhum atendimento encontrado para os filtros selecionados.</td></tr>';
                DOM.totalAtend.classList.add('d-none');
                return;
            }

            let html = '';
            let total = 0;
            data.forEach(a => {
                total += parseInt(a.quantidade);
                const tipoBadge = a.medico_tipo === 'dentista' ? 'badge-dentista' : 'badge-medico';
                const tipoText = a.medico_tipo === 'dentista' ? 'Dentista' : 'Médico';
                
                // Formata data YYYY-MM-DD para DD/MM/YYYY
                const [y, m, d] = a.data.split('-');
                const dataFormatada = `${d}/${m}/${y}`;

                html += `
                <tr>
                    <td>${dataFormatada}</td>
                    <td><span class="${tipoBadge} me-1">${tipoText}</span> ${a.medico_nome}</td>
                    <td>${a.especialidade_nome || '—'}</td>
                    <td>${a.unidade_nome}</td>
                    <td class="text-center fw-bold">${a.quantidade}</td>
                    <td class="text-muted small">${a.observacoes || '—'}</td>
                </tr>`;
            });

            DOM.atendBody.innerHTML = html;
            DOM.totalAtend.innerHTML = `Total de Atendimentos: ${total}`;
            DOM.totalAtend.classList.remove('d-none');

        } catch (err) {
            DOM.atendBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Erro ao buscar: ${err.message}</td></tr>`;
        }
    }

    [DOM.fAtendUnidade, DOM.fAtendMes, DOM.fAtendAno].forEach(el => {
        el.addEventListener('change', fetchAtendimentos);
    });

    DOM.btnLimparAtend.addEventListener('click', () => {
        DOM.fAtendUnidade.value = '';
        DOM.fAtendMes.value = '';
        DOM.fAtendAno.value = '';
        fetchAtendimentos();
    });

    // Refresh when tab changes to atendimentos if needed
    const atendimentosTab = document.getElementById('atendimentos-tab');
    if(atendimentosTab) {
        atendimentosTab.addEventListener('shown.bs.tab', function () {
            // Already fetched or can re-fetch
        });
    }
});
