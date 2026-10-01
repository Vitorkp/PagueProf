/* ============================================================
   PAGUEPROF - SISTEMA COMPLETO
   ============================================================ */

// ============================================================
// BANCO DE DADOS SIMULADO
// ============================================================
const DB = {
    alunos: [
        { id: 1, nome: 'João Silva', email: 'joao@email.com', materia: 'Violão', status: 'Pago', valor: 120, data: '10/05/2026', avatar: 'JS', cor: 'avatar-blue' },
        { id: 2, nome: 'Maria Souza', email: 'maria@email.com', materia: 'Desenho', status: 'Pendente', valor: 90, data: '08/05/2026', avatar: 'MS', cor: 'avatar-purple' },
        { id: 3, nome: 'Pedro Lima', email: 'pedro@email.com', materia: 'Inglês', status: 'Pago', valor: 150, data: '12/05/2026', avatar: 'PL', cor: 'avatar-green' },
        { id: 4, nome: 'Ana Costa', email: 'ana@email.com', materia: 'Piano', status: 'Atrasado', valor: 200, data: '05/05/2026', avatar: 'AC', cor: 'avatar-orange' },
        { id: 5, nome: 'Lucas Rocha', email: 'lucas@email.com', materia: 'Guitarra', status: 'Pago', valor: 130, data: '14/05/2026', avatar: 'LR', cor: 'avatar-pink' }
    ],
    pagamentos: [
        { id: 1, aluno: 'João Silva', valor: 120, data: '10/05/2026', metodo: 'PIX', status: 'Pago' },
        { id: 2, aluno: 'Maria Souza', valor: 90, data: '08/05/2026', metodo: 'Cartão', status: 'Pendente' },
        { id: 3, aluno: 'Pedro Lima', valor: 150, data: '12/05/2026', metodo: 'PIX', status: 'Pago' },
        { id: 4, aluno: 'Ana Costa', valor: 200, data: '05/05/2026', metodo: 'Boleto', status: 'Atrasado' },
        { id: 5, aluno: 'Lucas Rocha', valor: 130, data: '14/05/2026', metodo: 'PIX', status: 'Pago' }
    ],
    aulas: [
        { id: 1, aluno: 'João Silva', materia: 'Violão', data: '20/05/2026', hora: '14:00', status: 'Agendada' },
        { id: 2, aluno: 'Maria Souza', materia: 'Desenho', data: '21/05/2026', hora: '10:00', status: 'Agendada' },
        { id: 3, aluno: 'Pedro Lima', materia: 'Inglês', data: '22/05/2026', hora: '16:00', status: 'Agendada' },
        { id: 4, aluno: 'Ana Costa', materia: 'Piano', data: '19/05/2026', hora: '09:00', status: 'Concluída' },
        { id: 5, aluno: 'Lucas Rocha', materia: 'Guitarra', data: '23/05/2026', hora: '15:00', status: 'Agendada' }
    ],
    configuracoes: {
        nome: 'Prof. Silva',
        email: 'prof.silva@email.com',
        lembrete: 'email',
        antecedencia: '1',
        pix: 'prof.silva@email.com',
        valorPadrao: 'R$ 120,00',
        cidade: 'SAO PAULO',
        senha: '123456'
    },
    cobrancas: []
};

const AVATAR_COLORS = ['avatar-blue', 'avatar-purple', 'avatar-green', 'avatar-orange', 'avatar-pink', 'avatar-cyan'];

let paginaAtual = 'dashboard';
let cobrancasAtuais = [];
let cobrancaIndexAtual = 0;
let emailRecuperacao = '';

// ============================================================
// TEMPLATES DAS PÁGINAS
// ============================================================
const Pages = {

    dashboard: () => `
        <div class="page">
            <div class="page-header">
                <h1>Olá, ${DB.configuracoes.nome}! 👋</h1>
                <p>Aqui está um resumo dos seus alunos e pagamentos.</p>
            </div>

            <section class="summary-grid">
                <div class="summary-card card-blue">
                    <div class="card-icon"><span class="material-symbols-rounded">group</span></div>
                    <div class="card-content">
                        <span class="card-label">Total de Alunos</span>
                        <span class="card-value">${DB.alunos.length}</span>
                    </div>
                </div>
                <div class="summary-card card-green">
                    <div class="card-icon"><span class="material-symbols-rounded">check_circle</span></div>
                    <div class="card-content">
                        <span class="card-label">Pagos</span>
                        <span class="card-value">${DB.alunos.filter(a => a.status === 'Pago').length}</span>
                    </div>
                </div>
                <div class="summary-card card-yellow">
                    <div class="card-icon"><span class="material-symbols-rounded">schedule</span></div>
                    <div class="card-content">
                        <span class="card-label">Pendentes</span>
                        <span class="card-value">${DB.alunos.filter(a => a.status === 'Pendente').length}</span>
                    </div>
                </div>
                <div class="summary-card card-red">
                    <div class="card-icon"><span class="material-symbols-rounded">error</span></div>
                    <div class="card-content">
                        <span class="card-label">Atrasados</span>
                        <span class="card-value">${DB.alunos.filter(a => a.status === 'Atrasado').length}</span>
                    </div>
                </div>
            </section>

            <section class="table-section">
                <div class="section-header">
                    <h2>Alunos Recentes</h2>
                    <button class="link-view-all" onclick="navigateTo('alunos')">
                        Ver todos <span class="material-symbols-rounded">arrow_forward</span>
                    </button>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Matéria</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${DB.alunos.length === 0 ? `
                                <tr><td colspan="4" style="text-align:center; padding: 2rem; color: var(--text-muted);">
                                    Nenhum aluno cadastrado ainda.
                                </td></tr>
                            ` : DB.alunos.slice(0, 4).map(a => `
                                <tr>
                                    <td>
                                        <div class="table-user">
                                            <div class="mini-avatar ${a.cor}">${a.avatar}</div>
                                            <div>
                                                <span class="table-name">${a.nome}</span>
                                                <span class="table-email">${a.email}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${a.materia}</td>
                                    <td class="table-value">R$ ${a.valor},00</td>
                                    <td>${renderBadge(a.status)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    `,

    alunos: () => `
        <div class="page">
            <div class="page-header">
                <h1>Alunos</h1>
                <p>Gerencie todos os seus alunos cadastrados.</p>
            </div>

            <section class="table-section">
                <div class="section-header">
                    <h2>Todos os Alunos (${DB.alunos.length})</h2>
                    <button class="link-view-all" onclick="openModal()">
                        <span class="material-symbols-rounded">add</span> Adicionar
                    </button>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Matéria</th>
                                <th>Última Aula</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            ${DB.alunos.length === 0 ? `
                                <tr><td colspan="6" style="text-align:center; padding: 2rem; color: var(--text-muted);">
                                    Nenhum aluno cadastrado ainda.
                                </td></tr>
                            ` : DB.alunos.map(a => `
                                <tr>
                                    <td>
                                        <div class="table-user">
                                            <div class="mini-avatar ${a.cor}">${a.avatar}</div>
                                            <div>
                                                <span class="table-name">${a.nome}</span>
                                                <span class="table-email">${a.email}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${a.materia}</td>
                                    <td>${a.data}</td>
                                    <td class="table-value">R$ ${a.valor},00</td>
                                    <td>${renderBadge(a.status)}</td>
                                    <td>
                                        <button class="icon-btn-sm" onclick="removerAluno(${a.id})" title="Remover">
                                            <span class="material-symbols-rounded">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    `,

    pagamentos: () => {
        return `
        <div class="page">
            <div class="page-header">
                <h1>Pagamentos</h1>
                <p>Acompanhe o histórico e gere cobranças PIX para seus alunos.</p>
            </div>

            <section class="summary-grid">
                <div class="summary-card card-green">
                    <div class="card-icon"><span class="material-symbols-rounded">payments</span></div>
                    <div class="card-content">
                        <span class="card-label">Total Recebido</span>
                        <span class="card-value">R$ ${DB.pagamentos.filter(p => p.status === 'Pago').reduce((s, p) => s + p.valor, 0)}</span>
                    </div>
                </div>
                <div class="summary-card card-yellow">
                    <div class="card-icon"><span class="material-symbols-rounded">pending</span></div>
                    <div class="card-content">
                        <span class="card-label">A Receber</span>
                        <span class="card-value">R$ ${DB.pagamentos.filter(p => p.status === 'Pendente').reduce((s, p) => s + p.valor, 0)}</span>
                    </div>
                </div>
                <div class="summary-card card-red">
                    <div class="card-icon"><span class="material-symbols-rounded">warning</span></div>
                    <div class="card-content">
                        <span class="card-label">Em Atraso</span>
                        <span class="card-value">R$ ${DB.pagamentos.filter(p => p.status === 'Atrasado').reduce((s, p) => s + p.valor, 0)}</span>
                    </div>
                </div>
            </section>

            <section class="table-section">
                <div class="section-header">
                    <h2>Histórico de Pagamentos</h2>
                    <button class="link-view-all" onclick="abrirModalCobranca()">
                        <span class="material-symbols-rounded">qr_code</span> Gerar Cobranças
                    </button>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Método</th>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${DB.pagamentos.length === 0 ? `
                                <tr><td colspan="5" style="text-align:center; padding: 2rem; color: var(--text-muted);">
                                    Nenhum pagamento registrado.
                                </td></tr>
                            ` : DB.pagamentos.map(p => `
                                <tr>
                                    <td><span class="table-name">${p.aluno}</span></td>
                                    <td>${p.metodo}</td>
                                    <td>${p.data}</td>
                                    <td class="table-value">R$ ${p.valor},00</td>
                                    <td>${renderBadge(p.status)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </section>

            ${DB.cobrancas.length > 0 ? `
            <section class="table-section">
                <div class="section-header">
                    <h2>Cobranças Agendadas</h2>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${DB.cobrancas.map(c => `
                                <tr>
                                    <td><span class="table-name">${c.alunoNome}</span></td>
                                    <td class="table-value">R$ ${c.valor},00</td>
                                    <td>${c.vencimento}${c.hora ? ' ' + c.hora : ''}</td>
                                    <td>${renderBadge(c.status)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </section>
            ` : ''}
        </div>
        `;
    },

    aulas: () => `
        <div class="page">
            <div class="page-header">
                <h1>Aulas</h1>
                <p>Veja sua agenda de aulas agendadas e concluídas.</p>
            </div>

            <section class="table-section">
                <div class="section-header">
                    <h2>Agenda de Aulas</h2>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Matéria</th>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${DB.aulas.length === 0 ? `
                                <tr><td colspan="5" style="text-align:center; padding: 2rem; color: var(--text-muted);">
                                    Nenhuma aula agendada.
                                </td></tr>
                            ` : DB.aulas.map(a => `
                                <tr>
                                    <td><span class="table-name">${a.aluno}</span></td>
                                    <td>${a.materia}</td>
                                    <td>${a.data}</td>
                                    <td>${a.hora}</td>
                                    <td><span class="status-badge status-scheduled">${a.status}</span></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    `,

    configuracoes: () => {
        const valorLimpo = (DB.configuracoes.valorPadrao || 'R$ 120,00')
            .replace(/[^\d,]/g, '')
            .replace(',', '.');

        return `
        <div class="page">
            <div class="page-header">
                <h1>Configurações</h1>
                <p>Ajuste as preferências da sua conta.</p>
            </div>

            <div class="settings-grid">
                <div class="settings-card">
                    <h3><span class="material-symbols-rounded">person</span> Perfil</h3>
                    <form id="formPerfil">
                        <div class="form-group">
                            <label for="cfgNome">Nome completo</label>
                            <input type="text" id="cfgNome" value="${DB.configuracoes.nome}" required />
                            <span class="form-error" id="errNome">Campo obrigatório</span>
                        </div>
                        <div class="form-group">
                            <label for="cfgEmail">E-mail</label>
                            <input type="email" id="cfgEmail" value="${DB.configuracoes.email}" required />
                            <span class="form-error" id="errEmail">E-mail inválido</span>
                        </div>
                        <button type="submit" class="btn-save">
                            <span class="material-symbols-rounded" style="font-size:16px;">save</span>
                            Salvar Alterações
                        </button>
                    </form>
                </div>

                <div class="settings-card">
                    <h3><span class="material-symbols-rounded">notifications</span> Notificações</h3>
                    <form id="formNotificacoes">
                        <div class="form-group">
                            <label for="cfgLembrete">Receber lembretes de pagamento</label>
                            <select id="cfgLembrete">
                                <option ${DB.configuracoes.lembrete === 'email' ? 'selected' : ''} value="email">Sim, por e-mail</option>
                                <option ${DB.configuracoes.lembrete === 'whatsapp' ? 'selected' : ''} value="whatsapp">Sim, por WhatsApp</option>
                                <option ${DB.configuracoes.lembrete === 'nao' ? 'selected' : ''} value="nao">Não receber</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cfgAntecedencia">Antecedência do lembrete</label>
                            <select id="cfgAntecedencia">
                                <option ${DB.configuracoes.antecedencia === '1' ? 'selected' : ''} value="1">1 dia antes</option>
                                <option ${DB.configuracoes.antecedencia === '3' ? 'selected' : ''} value="3">3 dias antes</option>
                                <option ${DB.configuracoes.antecedencia === '7' ? 'selected' : ''} value="7">1 semana antes</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-save">
                            <span class="material-symbols-rounded" style="font-size:16px;">save</span>
                            Salvar Preferências
                        </button>
                    </form>
                </div>

                <div class="settings-card">
                    <h3><span class="material-symbols-rounded">payments</span> Pagamentos</h3>
                    <form id="formPagamentos">
                        <div class="form-group">
                            <label for="cfgPix">Chave PIX *</label>
                            <input type="text" id="cfgPix" value="${DB.configuracoes.pix}" required placeholder="CPF, e-mail, telefone ou chave aleatória" />
                            <span class="form-error" id="errPix">Campo obrigatório</span>
                            <span class="form-hint">Usada para gerar os QR Codes de cobrança.</span>
                        </div>
                        <div class="form-group">
                            <label for="cfgValorPadrao">Valor padrão da hora/aula *</label>
                            <input type="number" id="cfgValorPadrao" value="${valorLimpo}" min="0" step="0.01" required placeholder="120.00" />
                            <span class="form-error" id="errValorPadrao">Campo obrigatório</span>
                            <span class="form-hint">Usado quando o aluno não tiver um valor específico.</span>
                        </div>
                        <button type="submit" class="btn-save">
                            <span class="material-symbols-rounded" style="font-size:16px;">save</span>
                            Salvar Dados
                        </button>
                    </form>
                </div>
            </div>
        </div>
        `;
    }
};

// ============================================================
// FUNÇÕES AUXILIARES
// ============================================================
function renderBadge(status) {
    const map = {
        'Pago': 'status-paid',
        'Pendente': 'status-pending',
        'Atrasado': 'status-late',
        'Agendada': 'status-scheduled',
        'Concluída': 'status-paid'
    };
    return `<span class="status-badge ${map[status] || 'status-pending'}">${status}</span>`;
}

function getIniciais(nome) {
    const partes = nome.trim().split(' ').filter(Boolean);
    if (partes.length === 0) return '??';
    if (partes.length === 1) return partes[0].substring(0, 2).toUpperCase();
    return (partes[0][0] + partes[partes.length - 1][0]).toUpperCase();
}

function getCorAleatoria() {
    return AVATAR_COLORS[Math.floor(Math.random() * AVATAR_COLORS.length)];
}

function getDataHoje() {
    const d = new Date();
    const dia = String(d.getDate()).padStart(2, '0');
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const ano = d.getFullYear();
    return `${dia}/${mes}/${ano}`;
}

function getDataHojeISO() {
    const d = new Date();
    const dia = String(d.getDate()).padStart(2, '0');
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    return `${d.getFullYear()}-${mes}-${dia}`;
}

function parseValor(str) {
    if (typeof str === 'number') return str;
    if (!str) return 0;
    return Number(String(str).replace(/[^\d,]/g, '').replace(',', '.')) || 0;
}

// ============================================================
// GERADOR DE PAYLOAD PIX
// ============================================================
function normalizarTexto(txt) {
    return (txt || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^A-Za-z0-9 ]/g, '')
        .toUpperCase()
        .trim();
}

function emvField(id, value) {
    const len = String(value.length).padStart(2, '0');
    return `${id}${len}${value}`;
}

function crc16(payload) {
    let crc = 0xFFFF;
    for (let i = 0; i < payload.length; i++) {
        crc ^= payload.charCodeAt(i) << 8;
        for (let j = 0; j < 8; j++) {
            if ((crc & 0x8000) !== 0) {
                crc = ((crc << 1) ^ 0x1021) & 0xFFFF;
            } else {
                crc = (crc << 1) & 0xFFFF;
            }
        }
    }
    return crc.toString(16).toUpperCase().padStart(4, '0');
}

function gerarPayloadPix({ chave, nome, cidade, valor, txid }) {
    const f00 = emvField('00', '01');
    const gui = emvField('00', 'br.gov.bcb.pix');
    const key = emvField('01', chave);
    const f26 = emvField('26', gui + key);
    const f52 = emvField('52', '0000');
    const f53 = emvField('53', '986');
    const f54 = emvField('54', Number(valor).toFixed(2));
    const f58 = emvField('58', 'BR');
    const nomeNorm = normalizarTexto(nome).substring(0, 25);
    const f59 = emvField('59', nomeNorm || 'RECEBEDOR');
    const cidadeNorm = normalizarTexto(cidade).substring(0, 15);
    const f60 = emvField('60', cidadeNorm || 'CIDADE');
    const txidNorm = (txid || '***').replace(/[^A-Za-z0-9]/g, '').substring(0, 25) || '***';
    const txidField = emvField('05', txidNorm);
    const f62 = emvField('62', txidField);

    const payloadSemCRC = f00 + f26 + f52 + f53 + f54 + f58 + f59 + f60 + f62 + '6304';
    const crc = crc16(payloadSemCRC);

    return payloadSemCRC + crc;
}

// ============================================================
// COBRANÇAS
// ============================================================
function abrirModalCobranca() {
    const modal = document.getElementById('modalCobranca');
    const lista = document.getElementById('listaCobranca');
    const dataInput = document.getElementById('cobrancaData');
    const horaInput = document.getElementById('cobrancaHora');

    dataInput.value = getDataHojeISO();
    horaInput.value = '';

    const valorPadraoNum = parseValor(DB.configuracoes.valorPadrao);
    const pendentes = DB.alunos.filter(a => a.status === 'Pendente' || a.status === 'Atrasado');

    if (pendentes.length === 0) {
        lista.innerHTML = `<div class="vazio">
            <span class="material-symbols-rounded" style="font-size:32px; display:block; margin-bottom:0.5rem; color:#10B981;">check_circle</span>
            Todos os alunos estão com pagamento em dia! 🎉
        </div>`;
    } else {
        lista.innerHTML = pendentes.map(a => {
            const valorDoAluno = a.valor && a.valor > 0 ? a.valor : valorPadraoNum;
            return `
                <label class="item-cobranca">
                    <input type="checkbox" value="${a.id}" checked />
                    <div class="info-aluno">
                        <span class="nome-aluno">${a.nome}</span>
                        <span class="detalhe-aluno">${a.materia} • ${a.status}</span>
                    </div>
                    <div class="valor-input-wrapper" onclick="event.stopPropagation(); event.preventDefault();">
                        <span class="prefix">R$</span>
                        <input type="number" class="input-valor" data-aluno-id="${a.id}" value="${valorDoAluno}" min="0" step="0.01" onclick="event.stopPropagation();" />
                    </div>
                </label>
            `;
        }).join('');
    }

    document.getElementById('cobrancaEtapa1').classList.remove('hidden');
    document.getElementById('cobrancaEtapa2').classList.add('hidden');

    modal.classList.add('active');
}

function fecharModalCobranca() {
    document.getElementById('modalCobranca').classList.remove('active');
    cobrancasAtuais = [];
    cobrancaIndexAtual = 0;
}

function gerarCobrancas() {
    const dataVenc = document.getElementById('cobrancaData').value;
    const horaVenc = document.getElementById('cobrancaHora').value;

    if (!dataVenc) {
        mostrarToast('Selecione uma data de vencimento.', 'error');
        return;
    }

    const checkboxes = document.querySelectorAll('#listaCobranca input[type="checkbox"]:checked');
    if (checkboxes.length === 0) {
        mostrarToast('Selecione pelo menos um aluno.', 'error');
        return;
    }

    const idsSelecionados = Array.from(checkboxes).map(cb => Number(cb.value));
    const alunosSelecionados = DB.alunos.filter(a => idsSelecionados.includes(a.id));

    const [ano, mes, dia] = dataVenc.split('-');
    const dataFormatada = `${dia}/${mes}/${ano}`;

    cobrancasAtuais = alunosSelecionados.map(a => {
        const inputValor = document.querySelector(`.input-valor[data-aluno-id="${a.id}"]`);
        const valorEditado = inputValor ? Number(inputValor.value) || 0 : (a.valor || parseValor(DB.configuracoes.valorPadrao));

        const txid = `PP${a.id}${Date.now().toString().slice(-6)}`;
        const payload = gerarPayloadPix({
            chave: DB.configuracoes.pix,
            nome: DB.configuracoes.nome,
            cidade: DB.configuracoes.cidade || 'SAO PAULO',
            valor: valorEditado,
            txid: txid
        });

        return {
            alunoId: a.id,
            alunoNome: a.nome,
            materia: a.materia,
            valor: valorEditado,
            vencimento: dataFormatada,
            vencimentoISO: dataVenc,
            hora: horaVenc,
            payload: payload,
            txid: txid,
            status: 'Pendente'
        };
    });

    DB.cobrancas.push(...cobrancasAtuais);
    salvarCobrancasNoStorage();

    cobrancaIndexAtual = 0;
    document.getElementById('cobrancaEtapa1').classList.add('hidden');
    document.getElementById('cobrancaEtapa2').classList.remove('hidden');
    exibirCobranca(cobrancaIndexAtual);
}

function exibirCobranca(index) {
    const c = cobrancasAtuais[index];
    if (!c) return;

    document.getElementById('qrcodeAlunoNome').textContent = c.alunoNome;
    document.getElementById('qrcodeValor').textContent = `R$ ${Number(c.valor).toFixed(2).replace('.', ',')}`;
    document.getElementById('qrcodeCounter').textContent = `${index + 1} / ${cobrancasAtuais.length}`;
    document.getElementById('pixCodeInput').value = c.payload;

    const container = document.getElementById('qrcodeCanvas');
    container.innerHTML = '';
    if (window.QRCode) {
        new QRCode(container, {
            text: c.payload,
            width: 240,
            height: 240,
            colorDark: '#111827',
            colorLight: '#FFFFFF',
            correctLevel: QRCode.CorrectLevel.M
        });
    } else {
        container.innerHTML = '<p style="color:red; font-size:0.8rem;">Erro ao carregar QR Code.</p>';
    }

    document.getElementById('qrcodeAnterior').disabled = index === 0;
    document.getElementById('qrcodeProximo').disabled = index === cobrancasAtuais.length - 1;
    document.getElementById('qrcodeAnterior').style.opacity = index === 0 ? '0.5' : '1';
    document.getElementById('qrcodeProximo').style.opacity = index === cobrancasAtuais.length - 1 ? '0.5' : '1';
}

function cobrancaAnterior() {
    if (cobrancaIndexAtual > 0) {
        cobrancaIndexAtual--;
        exibirCobranca(cobrancaIndexAtual);
    }
}

function cobrancaProximo() {
    if (cobrancaIndexAtual < cobrancasAtuais.length - 1) {
        cobrancaIndexAtual++;
        exibirCobranca(cobrancaIndexAtual);
    }
}

function voltarCobranca() {
    document.getElementById('cobrancaEtapa1').classList.remove('hidden');
    document.getElementById('cobrancaEtapa2').classList.add('hidden');
}

function copiarPix() {
    const input = document.getElementById('pixCodeInput');
    input.select();
    input.setSelectionRange(0, 99999);

    try {
        navigator.clipboard.writeText(input.value).then(() => {
            mostrarToast('Código PIX copiado!', 'success');
        }).catch(() => {
            document.execCommand('copy');
            mostrarToast('Código PIX copiado!', 'success');
        });
    } catch (e) {
        document.execCommand('copy');
        mostrarToast('Código PIX copiado!', 'success');
    }
}

// ============================================================
// VERIFICAÇÃO AUTOMÁTICA
// ============================================================
function verificarCobrancasVencidas() {
    const hoje = getDataHojeISO();
    let alterou = false;

    DB.cobrancas.forEach(c => {
        if (c.status === 'Pago') return;
        if (c.vencimentoISO <= hoje) {
            c.status = 'Pago';
            const aluno = DB.alunos.find(a => a.id === c.alunoId);
            if (aluno) aluno.status = 'Pago';

            const pagExistente = DB.pagamentos.find(p => p.aluno === c.alunoNome && p.data === c.vencimento);
            if (pagExistente) {
                pagExistente.status = 'Pago';
            } else {
                DB.pagamentos.unshift({
                    id: Date.now() + Math.random(),
                    aluno: c.alunoNome,
                    valor: c.valor,
                    data: c.vencimento,
                    metodo: 'PIX',
                    status: 'Pago'
                });
            }
            alterou = true;
        }
    });

    if (alterou) {
        salvarCobrancasNoStorage();
        mostrarToast('Cobranças vencidas foram marcadas como PAGAS automaticamente! ✅', 'success');
    }
}

// ============================================================
// PERSISTÊNCIA
// ============================================================
function salvarCobrancasNoStorage() {
    try {
        localStorage.setItem('pagueprof_cobrancas', JSON.stringify(DB.cobrancas));
    } catch (e) { console.warn('Erro ao salvar cobranças:', e); }
}

function carregarCobrancasDoStorage() {
    const salvo = localStorage.getItem('pagueprof_cobrancas');
    if (salvo) {
        try { DB.cobrancas = JSON.parse(salvo); }
        catch (e) { console.warn('Erro ao carregar cobranças:', e); }
    }
}

// ============================================================
// VALIDAÇÃO DE SENHA
// ============================================================
function validarSenhaForte(senha) {
    return {
        length: senha.length >= 6,
        upper: /[A-Z]/.test(senha),
        lower: /[a-z]/.test(senha),
        special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(senha)
    };
}

function atualizarRequisitosSenha(senha, containerId) {
    const reqs = validarSenhaForte(senha);
    const container = document.getElementById(containerId);
    if (!container) return;

    container.querySelectorAll('.req-item').forEach(item => {
        const req = item.dataset.req;
        const icon = item.querySelector('.material-symbols-rounded');

        if (reqs[req]) {
            item.classList.add('valid');
            if (icon) icon.textContent = 'check_circle';
        } else {
            item.classList.remove('valid');
            if (icon) icon.textContent = 'cancel';
        }
    });
}

// ============================================================
// TOGGLE SENHA
// ============================================================
function toggleSenha(inputId, botao) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const icon = botao.querySelector('.material-symbols-rounded');

    if (input.type === 'password') {
        input.type = 'text';
        if (icon) icon.textContent = 'visibility_off';
        botao.title = 'Ocultar senha';
    } else {
        input.type = 'password';
        if (icon) icon.textContent = 'visibility';
        botao.title = 'Mostrar senha';
    }
}

// ============================================================
// AUTENTICAÇÃO
// ============================================================
const STORAGE_USER = 'pagueprof_user';
const STORAGE_SESSION = 'pagueprof_session';

function carregarUsuario() {
    const salvo = localStorage.getItem(STORAGE_USER);
    if (salvo) {
        try {
            const user = JSON.parse(salvo);
            DB.configuracoes.nome = user.nome;
            DB.configuracoes.email = user.email;
            DB.configuracoes.senha = user.senha;
        } catch (e) { console.warn('Erro ao carregar usuário:', e); }
    }
}

function salvarUsuario() {
    localStorage.setItem(STORAGE_USER, JSON.stringify({
        nome: DB.configuracoes.nome,
        email: DB.configuracoes.email,
        senha: DB.configuracoes.senha
    }));
}

function temSessaoAtiva() { return localStorage.getItem(STORAGE_SESSION) === 'ativa'; }
function criarSessao() { localStorage.setItem(STORAGE_SESSION, 'ativa'); }
function encerrarSessao() { localStorage.removeItem(STORAGE_SESSION); }

function mostrarLogin() {
    document.getElementById('authScreen').classList.remove('hidden');
    document.getElementById('appLayout').classList.add('hidden');
}

function mostrarApp() {
    document.getElementById('authScreen').classList.add('hidden');
    document.getElementById('appLayout').classList.remove('hidden');

    const sidebarName = document.getElementById('sidebarName');
    const sidebarAvatar = document.getElementById('sidebarAvatar');
    if (sidebarName) sidebarName.textContent = DB.configuracoes.nome;
    if (sidebarAvatar) sidebarAvatar.textContent = getIniciais(DB.configuracoes.nome);

    verificarCobrancasVencidas();
    navigateTo('dashboard');
}

function trocarAba(aba) {
    const tabLogin = document.getElementById('tabLogin');
    const tabRegister = document.getElementById('tabRegister');
    const formLogin = document.getElementById('formLogin');
    const formRegister = document.getElementById('formRegister');

    if (aba === 'login') {
        tabLogin.classList.add('active');
        tabRegister.classList.remove('active');
        formLogin.classList.remove('hidden');
        formRegister.classList.add('hidden');
    } else {
        tabRegister.classList.add('active');
        tabLogin.classList.remove('active');
        formRegister.classList.remove('hidden');
        formLogin.classList.add('hidden');
    }
}

function fazerLogin(e) {
    e.preventDefault();
    const botao = e.target.querySelector('.auth-btn');
    const email = document.getElementById('loginEmail').value.trim();
    const senha = document.getElementById('loginSenha').value;

    let valido = true;
    limparErroAuth('loginEmail', 'errLoginEmail');
    limparErroAuth('loginSenha', 'errLoginSenha');

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        mostrarErroAuth('loginEmail', 'errLoginEmail', 'E-mail inválido');
        valido = false;
    }
    if (!senha) {
        mostrarErroAuth('loginSenha', 'errLoginSenha', 'Senha obrigatória');
        valido = false;
    }

    if (!valido) return;

    if (email.toLowerCase() === DB.configuracoes.email.toLowerCase() && senha === DB.configuracoes.senha) {
        setLoadingAuth(botao, true);
        setTimeout(() => {
            criarSessao();
            setLoadingAuth(botao, false);
            mostrarApp();
            mostrarToast(`Bem-vindo, ${DB.configuracoes.nome}!`, 'success');
        }, 700);
    } else {
        mostrarErroAuth('loginSenha', 'errLoginSenha', 'E-mail ou senha incorretos');
        mostrarToast('Credenciais inválidas.', 'error');
    }
}

function fazerCadastro(e) {
    e.preventDefault();
    const botao = e.target.querySelector('.auth-btn');
    const nome = document.getElementById('regNome').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const senha = document.getElementById('regSenha').value;

    let valido = true;
    limparErroAuth('regNome', 'errRegNome');
    limparErroAuth('regEmail', 'errRegEmail');
    limparErroAuth('regSenha', 'errRegSenha');

    if (!nome) { mostrarErroAuth('regNome', 'errRegNome', 'Campo obrigatório'); valido = false; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        mostrarErroAuth('regEmail', 'errRegEmail', 'E-mail inválido'); valido = false;
    }

    const reqs = validarSenhaForte(senha);
    if (!(reqs.length && reqs.upper && reqs.lower && reqs.special)) {
        mostrarErroAuth('regSenha', 'errRegSenha', 'A senha não atende aos requisitos');
        valido = false;
    }

    if (!valido) {
        mostrarToast('Corrija os campos destacados.', 'error');
        return;
    }

    setLoadingAuth(botao, true);
    setTimeout(() => {
        DB.configuracoes.nome = nome;
        DB.configuracoes.email = email;
        DB.configuracoes.senha = senha;

        salvarUsuario();
        criarSessao();

        setLoadingAuth(botao, false);
        mostrarApp();
        mostrarToast(`Conta criada com sucesso! Bem-vindo, ${nome}!`, 'success');
    }, 700);
}

function fazerLogout() {
    if (confirm('Deseja realmente sair da sua conta?')) {
        encerrarSessao();
        mostrarLogin();
        document.getElementById('formLogin').reset();
        document.getElementById('formRegister').reset();
        trocarAba('login');
        mostrarToast('Você saiu da sua conta.', 'success');
    }
}

function mostrarErroAuth(campoId, erroId, mensagem) {
    const campo = document.getElementById(campoId);
    const erro = document.getElementById(erroId);
    if (campo) campo.classList.add('error');
    if (erro) { erro.textContent = mensagem; erro.classList.add('show'); }
}

function limparErroAuth(campoId, erroId) {
    const campo = document.getElementById(campoId);
    const erro = document.getElementById(erroId);
    if (campo) campo.classList.remove('error');
    if (erro) erro.classList.remove('show');
}

function setLoadingAuth(botao, ativo) {
    if (!botao) return;
    if (ativo) {
        botao.classList.add('loading');
        botao.dataset.originalText = botao.innerHTML;
        botao.innerHTML = 'Aguarde...';
    } else {
        botao.classList.remove('loading');
        botao.innerHTML = botao.dataset.originalText || botao.innerHTML;
    }
}

// ============================================================
// ESQUECI MINHA SENHA — FUNÇÕES GLOBAIS
// ============================================================
window.abrirModalEsqueci = function() {
    console.log('✅ abrirModalEsqueci() chamada');

    const modal = document.getElementById('modalEsqueciSenha');
    if (!modal) {
        console.error('❌ Modal #modalEsqueciSenha não encontrado!');
        alert('Erro: modal de recuperação não encontrado.');
        return;
    }

    const formEmail = document.getElementById('formEsqueciEmail');
    const formSenha = document.getElementById('formEsqueciSenha');
    if (formEmail) formEmail.reset();
    if (formSenha) formSenha.reset();

    document.getElementById('esqueciEtapa1').classList.remove('hidden');
    document.getElementById('esqueciEtapa2').classList.add('hidden');

    limparErroAuth('esqueciEmail', 'errEsqueciEmail');
    limparErroAuth('esqueciNovaSenha', 'errEsqueciNovaSenha');
    limparErroAuth('esqueciConfirmarSenha', 'errEsqueciConfirmarSenha');

    const reqsContainer = document.getElementById('esqueciPasswordReqs');
    if (reqsContainer) {
        reqsContainer.querySelectorAll('.req-item').forEach(item => {
            item.classList.remove('valid');
            const icon = item.querySelector('.material-symbols-rounded');
            if (icon) icon.textContent = 'cancel';
        });
    }

    emailRecuperacao = '';
    modal.classList.add('active');
};

window.fecharModalEsqueci = function() {
    const modal = document.getElementById('modalEsqueciSenha');
    if (modal) modal.classList.remove('active');
    emailRecuperacao = '';
};

window.voltarEsqueciEtapa1 = function() {
    document.getElementById('esqueciEtapa1').classList.remove('hidden');
    document.getElementById('esqueciEtapa2').classList.add('hidden');
};

function esqueciEtapa1Submit(e) {
    e.preventDefault();
    const email = document.getElementById('esqueciEmail').value.trim();

    limparErroAuth('esqueciEmail', 'errEsqueciEmail');

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        mostrarErroAuth('esqueciEmail', 'errEsqueciEmail', 'E-mail inválido');
        return;
    }

    if (email.toLowerCase() !== DB.configuracoes.email.toLowerCase()) {
        mostrarErroAuth('esqueciEmail', 'errEsqueciEmail', 'E-mail não encontrado');
        mostrarToast('Nenhuma conta encontrada com este e-mail.', 'error');
        return;
    }

    emailRecuperacao = email;
    document.getElementById('esqueciEtapa1').classList.add('hidden');
    document.getElementById('esqueciEtapa2').classList.remove('hidden');
    setTimeout(() => document.getElementById('esqueciNovaSenha').focus(), 100);
}

function esqueciEtapa2Submit(e) {
    e.preventDefault();
    const botao = e.target.querySelector('.btn-primary');
    const novaSenha = document.getElementById('esqueciNovaSenha').value;
    const confirmar = document.getElementById('esqueciConfirmarSenha').value;

    let valido = true;
    limparErroAuth('esqueciNovaSenha', 'errEsqueciNovaSenha');
    limparErroAuth('esqueciConfirmarSenha', 'errEsqueciConfirmarSenha');

    const reqs = validarSenhaForte(novaSenha);
    if (!(reqs.length && reqs.upper && reqs.lower && reqs.special)) {
        mostrarErroAuth('esqueciNovaSenha', 'errEsqueciNovaSenha', 'A senha não atende aos requisitos');
        valido = false;
    }

    if (novaSenha !== confirmar) {
        mostrarErroAuth('esqueciConfirmarSenha', 'errEsqueciConfirmarSenha', 'As senhas não coincidem');
        valido = false;
    }

    if (!valido) {
        mostrarToast('Verifique os campos de senha.', 'error');
        return;
    }

    setLoadingAuth(botao, true);

    setTimeout(() => {
        DB.configuracoes.senha = novaSenha;
        salvarNoStorage();
        salvarUsuario();

        setLoadingAuth(botao, false);
        fecharModalEsqueci();

        document.getElementById('loginEmail').value = emailRecuperacao;
        document.getElementById('loginSenha').value = '';
        document.getElementById('loginSenha').focus();

        mostrarToast('Senha redefinida com sucesso! Faça login.', 'success');
    }, 800);
}

// ============================================================
// NAVEGAÇÃO
// ============================================================
function navigateTo(pageName) {
    paginaAtual = pageName;
    const content = document.getElementById('appContent');
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        link.classList.remove('active', 'outlined');
        if (link.dataset.page === pageName) {
            if (pageName === 'configuracoes') link.classList.add('outlined');
            else link.classList.add('active');
        }
    });

    content.innerHTML = Pages[pageName] ? Pages[pageName]() : '<h1>Página não encontrada</h1>';

    if (pageName === 'configuracoes') registrarEventosConfiguracoes();

    const labelMap = {
        dashboard: 'Novo Aluno',
        alunos: 'Novo Aluno',
        pagamentos: 'Nova Cobrança',
        aulas: 'Nova Aula',
        configuracoes: 'Salvar'
    };
    document.getElementById('btnNewLabel').textContent = labelMap[pageName] || 'Novo';

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================================
// MODAL DE NOVO ALUNO
// ============================================================
function openModal() {
    document.getElementById('modalOverlay').classList.add('active');
    setTimeout(() => document.getElementById('nome').focus(), 100);
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('active');
    document.getElementById('formAluno').reset();
}

function cadastrarAluno(e) {
    e.preventDefault();
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const materia = document.getElementById('materia').value.trim();
    const valor = Number(document.getElementById('valor').value);
    const status = document.getElementById('status').value;

    if (!nome || !email || !materia || !valor) return;

    DB.alunos.push({
        id: Date.now(), nome, email, materia, valor, status,
        data: getDataHoje(), avatar: getIniciais(nome), cor: getCorAleatoria()
    });

    DB.pagamentos.unshift({
        id: Date.now() + 1, aluno: nome, valor,
        data: getDataHoje(), metodo: 'PIX', status
    });

    closeModal();
    navigateTo(paginaAtual);
    mostrarToast(`Aluno "${nome}" cadastrado com sucesso!`, 'success');
}

function removerAluno(id) {
    const aluno = DB.alunos.find(a => a.id === id);
    if (!aluno) return;
    if (confirm(`Deseja realmente remover o aluno "${aluno.nome}"?`)) {
        DB.alunos = DB.alunos.filter(a => a.id !== id);
        DB.pagamentos = DB.pagamentos.filter(p => p.aluno !== aluno.nome);
        DB.cobrancas = DB.cobrancas.filter(c => c.alunoId !== id);
        salvarCobrancasNoStorage();
        navigateTo(paginaAtual);
        mostrarToast(`Aluno "${aluno.nome}" removido.`, 'success');
    }
}

// ============================================================
// CONFIGURAÇÕES
// ============================================================
function salvarNoStorage() {
    try { localStorage.setItem('pagueprof_config', JSON.stringify(DB.configuracoes)); }
    catch (e) { console.warn('Erro:', e); }
}

function carregarDoStorage() {
    const salvo = localStorage.getItem('pagueprof_config');
    if (salvo) {
        try { DB.configuracoes = { ...DB.configuracoes, ...JSON.parse(salvo) }; }
        catch (e) { console.warn('Erro:', e); }
    }
}

function mostrarErro(campoId, erroId, mensagem) {
    const campo = document.getElementById(campoId);
    const erro = document.getElementById(erroId);
    if (campo) campo.classList.add('error');
    if (erro) { erro.textContent = mensagem; erro.classList.add('show'); }
}

function limparErro(campoId, erroId) {
    const campo = document.getElementById(campoId);
    const erro = document.getElementById(erroId);
    if (campo) campo.classList.remove('error');
    if (erro) erro.classList.remove('show');
}

function setLoading(botao, ativo) {
    if (!botao) return;
    if (ativo) {
        botao.classList.add('loading');
        botao.dataset.originalText = botao.innerHTML;
        botao.innerHTML = 'Salvando...';
    } else {
        botao.classList.remove('loading');
        botao.innerHTML = botao.dataset.originalText || botao.innerHTML;
    }
}

function salvarPerfil(e) {
    e.preventDefault();
    const botao = e.target.querySelector('.btn-save');
    const nome = document.getElementById('cfgNome').value.trim();
    const email = document.getElementById('cfgEmail').value.trim();

    let valido = true;
    limparErro('cfgNome', 'errNome');
    limparErro('cfgEmail', 'errEmail');

    if (!nome) { mostrarErro('cfgNome', 'errNome', 'Campo obrigatório'); valido = false; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        mostrarErro('cfgEmail', 'errEmail', 'E-mail inválido'); valido = false;
    }

    if (!valido) { mostrarToast('Corrija os campos destacados.', 'error'); return; }

    setLoading(botao, true);
    setTimeout(() => {
        DB.configuracoes.nome = nome;
        DB.configuracoes.email = email;
        salvarNoStorage();
        salvarUsuario();

        const userNome = document.getElementById('sidebarName');
        const userAvatar = document.getElementById('sidebarAvatar');
        if (userNome) userNome.textContent = nome;
        if (userAvatar) userAvatar.textContent = getIniciais(nome);

        setLoading(botao, false);
        mostrarToast('Perfil atualizado com sucesso!', 'success');
    }, 600);
}

function salvarNotificacoes(e) {
    e.preventDefault();
    const botao = e.target.querySelector('.btn-save');
    const lembrete = document.getElementById('cfgLembrete').value;
    const antecedencia = document.getElementById('cfgAntecedencia').value;

    setLoading(botao, true);
    setTimeout(() => {
        DB.configuracoes.lembrete = lembrete;
        DB.configuracoes.antecedencia = antecedencia;
        salvarNoStorage();
        setLoading(botao, false);
        mostrarToast('Preferências salvas com sucesso!', 'success');
    }, 600);
}

function salvarPagamentos(e) {
    e.preventDefault();
    const botao = e.target.querySelector('.btn-save');
    const pix = document.getElementById('cfgPix').value.trim();
    const valorPadrao = document.getElementById('cfgValorPadrao').value.trim();

    let valido = true;
    limparErro('cfgPix', 'errPix');
    limparErro('cfgValorPadrao', 'errValorPadrao');

    if (!pix) { mostrarErro('cfgPix', 'errPix', 'Campo obrigatório'); valido = false; }
    if (!valorPadrao || Number(valorPadrao) <= 0) {
        mostrarErro('cfgValorPadrao', 'errValorPadrao', 'Informe um valor válido'); valido = false;
    }

    if (!valido) { mostrarToast('Corrija os campos destacados.', 'error'); return; }

    setLoading(botao, true);
    setTimeout(() => {
        DB.configuracoes.pix = pix;
        DB.configuracoes.valorPadrao = `R$ ${Number(valorPadrao).toFixed(2).replace('.', ',')}`;
        DB.configuracoes.cidade = DB.configuracoes.cidade || 'SAO PAULO';
        salvarNoStorage();
        setLoading(botao, false);
        mostrarToast('Dados de pagamento salvos!', 'success');
    }, 600);
}

function registrarEventosConfiguracoes() {
    const formPerfil = document.getElementById('formPerfil');
    const formNotif = document.getElementById('formNotificacoes');
    const formPag = document.getElementById('formPagamentos');

    if (formPerfil) formPerfil.addEventListener('submit', salvarPerfil);
    if (formNotif) formNotif.addEventListener('submit', salvarNotificacoes);
    if (formPag) formPag.addEventListener('submit', salvarPagamentos);
}

// ============================================================
// TOAST
// ============================================================
let toastTimer;
function mostrarToast(mensagem, tipo = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;

    const icon = toast.querySelector('.material-symbols-rounded');
    document.getElementById('toastMessage').textContent = mensagem;

    toast.classList.remove('success', 'error');
    toast.classList.add(tipo);

    if (icon) icon.textContent = tipo === 'error' ? 'error' : 'check_circle';

    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 3000);
}

// ============================================================
// INICIALIZAÇÃO
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 PagueProf inicializado');

    carregarUsuario();
    carregarDoStorage();
    carregarCobrancasDoStorage();

    document.getElementById('tabLogin').addEventListener('click', () => trocarAba('login'));
    document.getElementById('tabRegister').addEventListener('click', () => trocarAba('register'));

    document.getElementById('formLogin').addEventListener('submit', fazerLogin);
    document.getElementById('formRegister').addEventListener('submit', fazerCadastro);

    // Formulários do esqueci senha
    document.getElementById('formEsqueciEmail').addEventListener('submit', esqueciEtapa1Submit);
    document.getElementById('formEsqueciSenha').addEventListener('submit', esqueciEtapa2Submit);

    // Checklist de senha no cadastro
    const regSenhaInput = document.getElementById('regSenha');
    if (regSenhaInput) {
        regSenhaInput.addEventListener('input', (e) => {
            atualizarRequisitosSenha(e.target.value, 'passwordReqs');
            if (e.target.value.length > 0) limparErroAuth('regSenha', 'errRegSenha');
        });
    }

    // Checklist de senha no esqueci
    const esqueciNovaSenha = document.getElementById('esqueciNovaSenha');
    if (esqueciNovaSenha) {
        esqueciNovaSenha.addEventListener('input', (e) => {
            atualizarRequisitosSenha(e.target.value, 'esqueciPasswordReqs');
            if (e.target.value.length > 0) limparErroAuth('esqueciNovaSenha', 'errEsqueciNovaSenha');
        });
    }

    // Logo
    const logoHome = document.getElementById('logoHome');
    if (logoHome) {
        logoHome.addEventListener('click', (e) => {
            e.preventDefault();
            navigateTo('dashboard');
        });
    }

    // Logout
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) logoutBtn.addEventListener('click', fazerLogout);

    // Navegação
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            navigateTo(link.dataset.page);
        });
    });

    // Botão "+ Novo"
    const btnNew = document.getElementById('btnNew');
    if (btnNew) {
        btnNew.addEventListener('click', () => {
            if (paginaAtual === 'configuracoes') mostrarToast('Use os botões de salvar em cada seção.', 'success');
            else if (paginaAtual === 'pagamentos') abrirModalCobranca();
            else if (paginaAtual === 'aulas') mostrarToast('Funcionalidade de nova aula em breve.', 'success');
            else openModal();
        });
    }

    // Modal Novo Aluno
    const modalClose = document.getElementById('modalClose');
    const modalCancel = document.getElementById('modalCancel');
    const modalOverlay = document.getElementById('modalOverlay');

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalCancel) modalCancel.addEventListener('click', closeModal);
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target.id === 'modalOverlay') closeModal();
        });
    }

    // Modal Cobrança
    const cobrancaClose = document.getElementById('cobrancaClose');
    const cobrancaCancel = document.getElementById('cobrancaCancel');
    const modalCobranca = document.getElementById('modalCobranca');
    const cobrancaGerar = document.getElementById('cobrancaGerar');
    const cobrancaVoltar = document.getElementById('cobrancaVoltar');
    const qrcodeAnterior = document.getElementById('qrcodeAnterior');
    const qrcodeProximo = document.getElementById('qrcodeProximo');
    const btnCopiarPix = document.getElementById('btnCopiarPix');

    if (cobrancaClose) cobrancaClose.addEventListener('click', fecharModalCobranca);
    if (cobrancaCancel) cobrancaCancel.addEventListener('click', fecharModalCobranca);
    if (modalCobranca) {
        modalCobranca.addEventListener('click', (e) => {
            if (e.target.id === 'modalCobranca') fecharModalCobranca();
        });
    }
    if (cobrancaGerar) cobrancaGerar.addEventListener('click', gerarCobrancas);
    if (cobrancaVoltar) cobrancaVoltar.addEventListener('click', voltarCobranca);
    if (qrcodeAnterior) qrcodeAnterior.addEventListener('click', cobrancaAnterior);
    if (qrcodeProximo) qrcodeProximo.addEventListener('click', cobrancaProximo);
    if (btnCopiarPix) btnCopiarPix.addEventListener('click', copiarPix);

    // ESC fecha modais
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
            fecharModalCobranca();
            fecharModalEsqueci();
        }
    });

    const formAluno = document.getElementById('formAluno');
    if (formAluno) formAluno.addEventListener('submit', cadastrarAluno);

    if (temSessaoAtiva()) {
        mostrarApp();
    } else {
        mostrarLogin();
    }
});