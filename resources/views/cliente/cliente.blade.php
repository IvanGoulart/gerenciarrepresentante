@extends('layouts.app')

@section('title', 'Cadastro de Clientes')

@section('page-title', 'Gestão de Clientes')

@section('content')
    <style>
        /* Fixed column widths to prevent layout breaking */
        .clientes-table th, .clientes-table td {
            vertical-align: middle;
        }
        .clientes-table th:nth-child(1), .clientes-table td:nth-child(1) { /* Nome */
            width: 30%;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .clientes-table th:nth-child(2), .clientes-table td:nth-child(2) { /* Email */
            width: 30%;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .clientes-table th:nth-child(3), .clientes-table td:nth-child(3) { /* Cidade */
            width: 30%;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .clientes-table th:nth-child(4), .clientes-table td:nth-child(4) { /* Ações */
            width: 10%;
            min-width: 150px; /* Increased to fit buttons side by side */
            text-align: center;
        }
        /* Ensure buttons are side by side */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 8px; /* Space between buttons */
            flex-wrap: nowrap;
        }
        .action-buttons .btn {
            flex: 0 0 auto; /* Prevent buttons from stretching */
        }
    </style>

    <!-- Formulário de Cadastro/Edição -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title" id="form-title">Novo Cliente</h5>
            <form id="form-cliente">
                <input type="hidden" id="cliente_id">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="cidade_id" class="form-label">Cidade</label>
                    <select class="form-select" id="cidade_id" required>
                        <option value="">Selecione uma cidade</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" id="form-button">Cadastrar</button>
                <button type="button" class="btn btn-secondary" id="cancel-button" style="display: none;">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Filtros e Lista de Clientes -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Clientes Cadastrados</h5>
            <!-- Formulário de Filtros -->
            <form id="form-filtros" class="mb-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="search" placeholder="Buscar por nome, email ou cidade">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="filtro-cidade">
                            <option value="">Todas as cidades</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-secondary" id="limpar-filtros">Limpar Filtros</button>
                    </div>
                </div>
            </form>
            <!-- Tabela de Clientes -->
            <div class="table-responsive">
                <table class="table table-striped table-hover clientes-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Cidade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="clientes-list"></tbody>
                </table>
            </div>
            <div id="lista-vazia" class="text-muted mt-3" style="display: none;">Nenhum cliente encontrado.</div>
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

        // Função para carregar cidades nos selects (cadastro e filtro)
        async function carregarCidades() {
            try {
                const response = await fetch('/api/cidades', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                const cidades = await response.json();

                // Select do formulário de cadastro
                const selectCadastro = document.getElementById('cidade_id');
                selectCadastro.innerHTML = '<option value="">Selecione uma cidade</option>';
                cidades.data.forEach(cidade => {
                    const option = document.createElement('option');
                    option.value = cidade.id;
                    option.textContent = `${cidade.nome} - ${cidade.estado ? cidade.estado.nome + ' (' + cidade.estado.uf + ')' : 'N/A'}`;
                    selectCadastro.appendChild(option);
                });

                // Select do filtro
                const selectFiltro = document.getElementById('filtro-cidade');
                selectFiltro.innerHTML = '<option value="">Todas as cidades</option>';
                cidades.data.forEach(cidade => {
                    const option = document.createElement('option');
                    option.value = cidade.id;
                    option.textContent = `${cidade.nome} - ${cidade.estado ? cidade.estado.nome + ' (' + cidade.estado.uf + ')' : 'N/A'}`;
                    selectFiltro.appendChild(option);
                });
            } catch (error) {
                console.error('Erro ao carregar cidades:', error);
                alert('Erro ao carregar cidades. Tente novamente.');
            }
        }

        // Função para listar clientes com filtros e paginação
        async function listarClientes(page = 1, search = '', cidade_id = '') {
            try {
                let url = `/api/clientes?page=${page}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                if (cidade_id) url += `&cidade_id=${cidade_id}`;

                const response = await fetch(url, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                const lista = document.getElementById('clientes-list');
                lista.innerHTML = '';
                const listaVazia = document.getElementById('lista-vazia');

                if (data.data.length === 0) {
                    listaVazia.style.display = 'block';
                } else {
                    listaVazia.style.display = 'none';
                    data.data.forEach(cliente => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${cliente.nome}</td>
                            <td>${cliente.email}</td>
                            <td>${cliente.cidade ? cliente.cidade.nome + ' - ' + (cliente.cidade.estado ? cliente.cidade.estado.nome + ' (' + cliente.cidade.estado.uf + ')' : 'N/A') : 'N/A'}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-primary btn-sm" onclick="editarCliente(${cliente.id}, '${cliente.nome}', '${cliente.email}', ${cliente.cidade_id || 'null'})">Alterar</button>
                                    <button class="btn btn-danger btn-sm" onclick="excluirCliente(${cliente.id})">Excluir</button>
                                </div>
                            </td>
                        `;
                        lista.appendChild(tr);
                    });
                }

                currentPage = data.current_page;
                lastPage = data.last_page;
                document.getElementById('pagination-info').textContent = `Página ${data.current_page} de ${data.last_page} (${data.total} clientes)`;
                document.getElementById('prev-page').disabled = !data.prev_page_url;
                document.getElementById('next-page').disabled = !data.next_page_url;
            } catch (error) {
                console.error('Erro ao listar clientes:', error);
                document.getElementById('lista-vazia').textContent = 'Erro ao carregar clientes.';
                document.getElementById('lista-vazia').style.display = 'block';
            }
        }

        // Função para editar cliente
        function editarCliente(id, nome, email, cidade_id) {
            document.getElementById('form-title').textContent = 'Editar Cliente';
            document.getElementById('form-button').textContent = 'Salvar';
            document.getElementById('cancel-button').style.display = 'inline-block';
            document.getElementById('cliente_id').value = id;
            document.getElementById('nome').value = nome;
            document.getElementById('email').value = email;
            document.getElementById('cidade_id').value = cidade_id || '';
        }

        // Função para limpar formulário
        function limparFormulario() {
            document.getElementById('form-title').textContent = 'Novo Cliente';
            document.getElementById('form-button').textContent = 'Cadastrar';
            document.getElementById('cancel-button').style.display = 'none';
            document.getElementById('form-cliente').reset();
            document.getElementById('cliente_id').value = '';
            document.getElementById('cidade_id').value = '';
        }

        // Função para cadastrar ou atualizar cliente
        document.getElementById('form-cliente').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('cliente_id').value;
            const nome = document.getElementById('nome').value;
            const email = document.getElementById('email').value;
            const cidade_id = document.getElementById('cidade_id').value;

            const method = id ? 'PUT' : 'POST';
            const url = id ? `/api/clientes/${id}` : '/api/clientes';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ nome, email, cidade_id })
                });

                if (response.ok) {
                    alert(id ? 'Cliente atualizado com sucesso!' : 'Cliente cadastrado com sucesso!');
                    limparFormulario();
                    listarClientes(currentPage, document.getElementById('search').value, document.getElementById('filtro-cidade').value);
                } else {
                    const error = await response.json();
                    alert('Erro: ' + JSON.stringify(error));
                }
            } catch (error) {
                alert('Erro: ' + error.message);
            }
        });

        // Função para excluir cliente
        async function excluirCliente(id) {
            if (!confirm('Tem certeza que deseja excluir este cliente?')) return;

            try {
                const response = await fetch(`/api/clientes/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                if (response.ok) {
                    alert('Cliente excluído com sucesso!');
                    listarClientes(currentPage, document.getElementById('search').value, document.getElementById('filtro-cidade').value);
                } else {
                    const error = await response.json();
                    alert('Erro: ' + JSON.stringify(error));
                }
            } catch (error) {
                alert('Erro ao excluir cliente: ' + error.message);
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
                listarClientes(1, document.getElementById('search').value, document.getElementById('filtro-cidade').value);
            }, 500);
        });

        // Evento para filtro por cidade
        document.getElementById('filtro-cidade').addEventListener('change', () => {
            currentPage = 1;
            listarClientes(1, document.getElementById('search').value, document.getElementById('filtro-cidade').value);
        });

        // Evento para limpar filtros
        document.getElementById('limpar-filtros').addEventListener('click', () => {
            document.getElementById('search').value = '';
            document.getElementById('filtro-cidade').value = '';
            currentPage = 1;
            listarClientes(1, '', '');
        });

        // Eventos para navegação de páginas
        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                listarClientes(currentPage - 1, document.getElementById('search').value, document.getElementById('filtro-cidade').value);
            }
        });

        document.getElementById('next-page').addEventListener('click', () => {
            if (currentPage < lastPage) {
                listarClientes(currentPage + 1, document.getElementById('search').value, document.getElementById('filtro-cidade').value);
            }
        });

        // Carregar cidades e clientes ao iniciar
        carregarCidades();
        listarClientes();
    </script>
@endsection