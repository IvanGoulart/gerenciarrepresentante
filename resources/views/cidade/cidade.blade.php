@extends('layouts.app')

@section('title', 'Cadastro de Cidades')

@section('page-title', 'Gerência de Cidades')

@section('content')
    <!-- Formulário de Cadastro/Edição -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title" id="form-title">Nova Cidade</h5>
            <form id="form-cidade">
                <input type="hidden" id="cidade_id">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" required>
                </div>
                <div class="mb-3">
                    <label for="estado_id" class="form-label">Estado</label>
                    <select class="form-select" id="estado_id" required>
                        <option value="">Selecione um estado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" id="form-button">Cadastrar</button>
                <button type="button" class="btn btn-secondary" id="cancel-button" style="display: none;">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Filtros e Lista de Cidades -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Cidades Cadastradas</h5>
            <!-- Formulário de Filtros -->
            <form id="form-filtros" class="mb-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="search" placeholder="Buscar por nome, UF ou estado">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-secondary" id="limpar-filtros">Limpar Filtros</button>
                    </div>
                </div>
            </form>
            <ul class="list-group" id="cidades-list"></ul>
            <div id="lista-vazia" class="text-muted mt-3" style="display: none;">Nenhuma cidade encontrada.</div>
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
@endsection

@section('scripts')
    <script>
        let currentPage = 1;
        let lastPage = 1;

        // Função para carregar estados no select
        async function carregarEstados() {
            try {
                const response = await fetch('/api/estados', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                const estados = await response.json();

                const select = document.getElementById('estado_id');
                select.innerHTML = '<option value="">Selecione um estado</option>';
                estados.forEach(estado => {
                    const option = document.createElement('option');
                    option.value = estado.id;
                    option.textContent = `${estado.nome} (${estado.uf})`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Erro ao carregar estados:', error);
                alert('Erro ao carregar estados. Tente novamente.');
            }
        }

        // Função para listar cidades com filtros e paginação
        async function listarCidades(page = 1, search = '') {
            try {
                let url = `/api/cidades?page=${page}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;

                const response = await fetch(url, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                const lista = document.getElementById('cidades-list');
                lista.innerHTML = '';
                const listaVazia = document.getElementById('lista-vazia');

                if (data.data.length === 0) {
                    listaVazia.style.display = 'block';
                } else {
                    listaVazia.style.display = 'none';
                    data.data.forEach(cidade => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';

                        li.innerHTML = `
                            ${cidade.nome} - ${cidade.estado ? cidade.estado.nome + ' (' + cidade.estado.uf + ')' : '(Sem estado)'}
                            <div>
                                <button class="btn btn-primary btn-sm me-2" onclick="editarCidade(${cidade.id}, '${cidade.nome}', '${cidade.estado_id || ''}', ${cidade.estado_id || 'null'})">Alterar</button>
                                <button class="btn btn-danger btn-sm" onclick="excluirCidade(${cidade.id})">Excluir</button>
                            </div>
                        `;
                        lista.appendChild(li);
                    });
                }

                currentPage = data.current_page;
                lastPage = data.last_page;
                document.getElementById('pagination-info').textContent = `Página ${data.current_page} de ${data.last_page} (${data.total} cidades)`;
                document.getElementById('prev-page').disabled = !data.prev_page_url;
                document.getElementById('next-page').disabled = !data.next_page_url;
            } catch (error) {
                console.error('Erro ao listar cidades:', error);
                document.getElementById('lista-vazia').textContent = 'Erro ao carregar cidades.';
                document.getElementById('lista-vazia').style.display = 'block';
            }
        }

        // Função para editar cidade
        function editarCidade(id, nome, estado_id, estado_id) {
            document.getElementById('form-title').textContent = 'Editar Cidade';
            document.getElementById('form-button').textContent = 'Salvar';
            document.getElementById('cancel-button').style.display = 'inline-block';
            document.getElementById('cidade_id').value = id;
            document.getElementById('nome').value = nome;
            document.getElementById('estado_id').value = estado_id || '';
        }

        // Função para limpar formulário
        function limparFormulario() {
            document.getElementById('form-title').textContent = 'Nova Cidade';
            document.getElementById('form-button').textContent = 'Cadastrar';
            document.getElementById('cancel-button').style.display = 'none';
            document.getElementById('form-cidade').reset();
            document.getElementById('cidade_id').value = '';
            document.getElementById('estado_id').value = '';
        }

        // Função para cadastrar ou atualizar cidade
        document.getElementById('form-cidade').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('cidade_id').value;
            const nome = document.getElementById('nome').value;
            const estado_id = document.getElementById('estado_id').value;

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/api/cidades/${id}` : '/api/cidades';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ nome, estado_id })
                });

                if (response.ok) {
                    alert(id ? 'Cidade atualizada com sucesso!' : 'Cidade cadastrada com sucesso!');
                    limparFormulario();
                    listarCidades(currentPage, document.getElementById('search').value);
                } else {
                    const error = await response.json();
                    alert('Erro: ' + JSON.stringify(error));
                }
            } catch (error) {
                alert('Erro: ' + error.message);
            }
        });

        // Função para excluir cidade
        async function excluirCidade(id) {
            if (!confirm('Tem certeza que deseja excluir esta cidade?')) return;

            try {
                const response = await fetch(`/api/cidades/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                if (response.ok) {
                    alert('Cidade excluída com sucesso!');
                    listarCidades(currentPage, document.getElementById('search').value);
                } else {
                    const error = await response.json();
                    alert('Erro: ' + JSON.stringify(error));
                }
            } catch (error) {
                alert('Erro ao excluir cidade: ' + error.message);
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
                listarCidades(1, document.getElementById('search').value);
            }, 500);
        });

        // Evento para limpar filtros
        document.getElementById('limpar-filtros').addEventListener('click', () => {
            document.getElementById('search').value = '';
            currentPage = 1;
            listarCidades(1, '');
        });

        // Eventos para navegação de páginas
        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                listarCidades(currentPage - 1, document.getElementById('search').value);
            }
        });

        document.getElementById('next-page').addEventListener('click', () => {
            if (currentPage < lastPage) {
                listarCidades(currentPage + 1, document.getElementById('search').value);
            }
        });

        // Carregar estados e cidades ao iniciar
        carregarEstados();
        listarCidades();
    </script>
@endsection