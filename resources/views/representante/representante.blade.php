@extends('layouts.app')

@section('title', 'Cadastro de Representantes')

@section('page-title', 'Gerenciar de Representantes')

@section('content')
    <!-- Formulário de Cadastro/Edição -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title" id="form-title">Novo Representante</h5>
            <form id="form-representante">
                <input type="hidden" id="representante_id">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" required>
                </div>
                <button type="submit" class="btn btn-primary" id="form-button">Cadastrar</button>
                <button type="button" class="btn btn-secondary" id="cancel-button" style="display: none;">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Filtros e Lista de Representantes -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Representantes Cadastrados</h5>
            <!-- Formulário de Filtros -->
            <form id="form-filtros" class="mb-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="search" placeholder="Buscar por nome">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-secondary" id="limpar-filtros">Limpar Filtros</button>
                    </div>
                </div>
            </form>
            <ul class="list-group" id="representantes-list"></ul>
            <div id="lista-vazia" class="text-muted mt-3" style="display: none;">Nenhum representante encontrado.</div>
            <!-- Controles de paginação -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div id="pagination-info"></div>
                <div>
                    <button class="btn btn-outline-primary me-2" id="prev-page" disabled>Anterior</button>
                    <button class="btn btn-outline-primary" id="next-page" disabled>Próximo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Gerenciar Cidades -->
    <div class="modal fade" id="modalGerenciarCidades" tabindex="-1" aria-labelledby="modalGerenciarCidadesLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalGerenciarCidadesLabel">Gerenciar Cidades</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cidades-checkboxes"></div>
                    <div id="cidade-error-modal" class="text-danger mt-2" style="display: none;">Nenhuma cidade disponível. Cadastre cidades primeiro.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="gerenciarCidades()">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentPage = 1;
        let lastPage = 1;
        let representanteIdAtual;

        // Função para listar representantes com filtros e paginação
        async function listarRepresentantes(page = 1, search = '') {
            try {
                let url = `/api/representantes?page=${page}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;

                const response = await fetch(url, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                const lista = document.getElementById('representantes-list');
                lista.innerHTML = '';
                const listaVazia = document.getElementById('lista-vazia');

                if (data.data.length === 0) {
                    listaVazia.style.display = 'block';
                } else {
                    listaVazia.style.display = 'none';
                    data.data.forEach(representante => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.innerHTML = `
                            ${representante.nome} (Cidades: ${representante.cidades && representante.cidades.length > 0 ? representante.cidades.map(c => c.nome).join(', ') : 'Nenhuma cidade associada'})
                            <div>
                                <button class="btn btn-primary btn-sm me-2" onclick="editarRepresentante(${representante.id}, '${representante.nome}')">Alterar Nome</button>
                                <button class="btn btn-danger btn-sm me-2" onclick="excluirRepresentante(${representante.id})">Excluir</button>
                                <button class="btn btn-success btn-sm" onclick="abrirModalGerenciarCidades(${representante.id})">Gerenciar Cidades</button>
                            </div>
                        `;
                        lista.appendChild(li);
                    });
                }

                currentPage = data.current_page;
                lastPage = data.last_page;
                document.getElementById('pagination-info').textContent = `Página ${data.current_page} de ${data.last_page} (${data.total} representantes)`;
                document.getElementById('prev-page').disabled = !data.prev_page_url;
                document.getElementById('next-page').disabled = !data.next_page_url;
            } catch (error) {
                console.error('Erro ao listar representantes:', error);
                document.getElementById('lista-vazia').textContent = 'Erro ao carregar representantes.';
                document.getElementById('lista-vazia').style.display = 'block';
            }
        }

        // Função para abrir o modal de gerenciamento de cidades
        async function abrirModalGerenciarCidades(id) {
            representanteIdAtual = id;
            try {
                // Carregar cidades disponíveis
                const response = await fetch('/api/cidades', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                const cidades = await response.json();
                const container = document.getElementById('cidades-checkboxes');
                container.innerHTML = '';

                if (cidades.length === 0) {
                    document.getElementById('cidade-error-modal').style.display = 'block';
                } else {
                    document.getElementById('cidade-error-modal').style.display = 'none';
                    cidades.forEach(cidade => {
                        const div = document.createElement('div');
                        div.className = 'form-check';
                        div.innerHTML = `
                            <input class="form-check-input" type="checkbox" value="${cidade.id}" id="cidade-${cidade.id}">
                            <label class="form-check-label" for="cidade-${cidade.id}">
                                ${cidade.nome}
                            </label>
                        `;
                        container.appendChild(div);
                    });
                }

                // Carregar cidades já associadas ao representante
                const respRep = await fetch(`/api/representantes/${id}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                const representante = await respRep.json();
                representante.cidades.forEach(cidade => {
                    const checkbox = document.getElementById(`cidade-${cidade.id}`);
                    if (checkbox) checkbox.checked = true;
                });

                // Abrir o modal
                const modal = new bootstrap.Modal(document.getElementById('modalGerenciarCidades'));
                modal.show();
            } catch (error) {
                console.error('Erro ao carregar cidades:', error);
                document.getElementById('cidade-error-modal').style.display = 'block';
            }
        }

        // Função para gerenciar (associar/desassociar) cidades
        async function gerenciarCidades() {
            const cidade_id = Array.from(document.querySelectorAll('#cidades-checkboxes .form-check-input:checked')).map(input => parseInt(input.value));
            try {
                const response = await fetch(`/api/

representantes/${representanteIdAtual}/cidades`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ cidade_id })
                });
                if (response.ok) {
                    alert('Cidades atualizadas com sucesso!');
                    listarRepresentantes(currentPage, document.getElementById('search').value);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalGerenciarCidades'));
                    modal.hide();
                } else {
                    const error = await response.json();
                    alert('Erro: ' + JSON.stringify(error));
                }
            } catch (error) {
                alert('Erro ao gerenciar cidades: ' + error.message);
            }
        }

        // Função para editar representante
        function editarRepresentante(id, nome) {
            document.getElementById('form-title').textContent = 'Editar Representante';
            document.getElementById('form-button').textContent = 'Salvar';
            document.getElementById('cancel-button').style.display = 'inline-block';
            document.getElementById('representante_id').value = id;
            document.getElementById('nome').value = nome;
        }

        // Função para limpar formulário
        function limparFormulario() {
            document.getElementById('form-title').textContent = 'Novo Representante';
            document.getElementById('form-button').textContent = 'Cadastrar';
            document.getElementById('cancel-button').style.display = 'none';
            document.getElementById('form-representante').reset();
            document.getElementById('representante_id').value = '';
        }

        // Função para cadastrar ou atualizar representante
        document.getElementById('form-representante').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('representante_id').value;
            const nome = document.getElementById('nome').value;

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/api/representantes/${id}` : '/api/representantes';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ nome })
                });

                if (response.ok) {
                    alert(id ? 'Representante atualizado com sucesso!' : 'Representante cadastrado com sucesso!');
                    limparFormulario();
                    listarRepresentantes(currentPage, document.getElementById('search').value);
                } else {
                    const error = await response.json();
                    alert('Erro: ' + JSON.stringify(error));
                }
            } catch (error) {
                alert('Erro: ' + error.message);
            }
        });

        // Função para excluir representante
        async function excluirRepresentante(id) {
            if (!confirm('Tem certeza que deseja excluir este representante?')) return;

            try {
                const response = await fetch(`/api/representantes/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                if (response.ok) {
                    alert('Representante excluído com sucesso!');
                    listarRepresentantes(currentPage, document.getElementById('search').value);
                } else {
                    const error = await response.json();
                    alert('Erro: ' + JSON.stringify(error));
                }
            } catch (error) {
                alert('Erro ao excluir representante: ' + error.message);
            }
        }

        // Evento para o botão Cancelar
        document.getElementById('cancel-button').addEventListener('click', limparFormulario);

        // Evento para busca
        let searchTimeout;
        document.getElementById('search').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                listarRepresentantes(1, document.getElementById('search').value);
            }, 500);
        });

        // Evento para limpar filtros
        document.getElementById('limpar-filtros').addEventListener('click', () => {
            document.getElementById('search').value = '';
            currentPage = 1;
            listarRepresentantes(1, '');
        });

        // Eventos para navegação de páginas
        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                listarRepresentantes(currentPage - 1, document.getElementById('search').value);
            }
        });

        document.getElementById('next-page').addEventListener('click', () => {
            if (currentPage < lastPage) {
                listarRepresentantes(currentPage + 1, document.getElementById('search').value);
            }
        });

        // Carregar representantes ao iniciar
        listarRepresentantes();
    </script>
@endsection