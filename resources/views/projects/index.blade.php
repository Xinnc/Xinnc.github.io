@extends('layouts.app')
@section('title', 'Проекты')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Проекты</h1>
        <a href="/projects/create" class="btn btn-primary create-project-btn" style="display:none;">
            <i class="bi bi-plus-lg"></i> Новый проект
        </a>
    </div>

    <div id="message" class="mb-3" style="display:none;"></div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Статус</label>
                    <select class="form-select" name="status">
                        <option value="">Все</option>
                        <option value="active">Активные</option>
                        <option value="paused">На паузе</option>
                        <option value="completed">Завершённые</option>
                        <option value="archived">Архивные</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Поиск</label>
                    <input type="text" class="form-control" name="search" placeholder="Название проекта">
                </div>
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-outline-secondary w-100">Фильтровать</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="projectsTable">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Дедлайн</th>
                    <th>Менеджер</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="card-footer text-center" id="noProjects" style="display:none;">
            <p class="text-muted mb-0">Проектов пока нет</p>
        </div>

        <div class="card-footer" id="pagination"></div>
    </div>

    @section('scripts')
        <script>
            // 1. Вспомогательные функции — сначала их
            function getStatusColor(status) {
                const colors = {
                    'active': 'success',
                    'paused': 'warning',
                    'completed': 'primary',
                    'archived': 'secondary'
                };
                return colors[status] || 'secondary';
            }

            function getStatusText(status) {
                const texts = {
                    'active': 'Активный',
                    'paused': 'На паузе',
                    'completed': 'Завершён',
                    'archived': 'В архиве'
                };
                return texts[status] || status;
            }

            function showMessage(type, text) {
                const div = document.getElementById('message');
                div.className = `alert alert-${type} mb-3`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 5000);
            }

            // 2. Теперь основная функция загрузки
            async function loadProjects(filters = {}) {
                const tbody = document.querySelector('#projectsTable tbody');
                const noProjects = document.getElementById('noProjects');
                const pagination = document.getElementById('pagination');
                const createBtn = document.querySelector('.create-project-btn');

                tbody.innerHTML = '';
                noProjects.style.display = 'none';
                pagination.innerHTML = '';

                const userRole = (localStorage.getItem('user_role') || 'user').toLowerCase();
                const canManage = ['manager', 'admin'].includes(userRole);

                if (createBtn) {
                    createBtn.style.display = canManage ? 'inline-block' : 'none';
                }

                try {
                    let query = new URLSearchParams(filters).toString();
                    query = query ? `?${query}` : '';

                    const response = await apiRequest('GET', `/project${query}`);
                    console.log('Ответ от сервера:', response); // для отладки

                    const projects = response.data || [];
                    const meta = response.meta || {};
                    const links = response.links || {};

                    if (projects.length === 0) {
                        noProjects.style.display = 'block';
                    } else {
                        projects.forEach(project => {
                            const manager = project.manager_id || {};
                            const row = document.createElement('tr');
                            row.innerHTML = `
                    <td>${project.id}</td>
                    <td>${project.name}</td>
                    <td>${project.description || '-'}</td>
                    <td>${project.deadline || '-'}</td>
                    <td>${manager.name || '-'}<br><small>${manager.email || ''}</small></td>
                    <td>
                        <span class="badge bg-${getStatusColor(project.status)}">
                            ${getStatusText(project.status)}
                        </span>
                    </td>
                    <td>
                        <a href="/projects/${project.id}" class="btn btn-sm btn-outline-info">Просмотр</a>
                        ${canManage ? `
                            <a href="/projects/${project.id}/edit" class="btn btn-sm btn-outline-warning">Редактировать</a>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${project.id}">Удалить</button>
                        ` : ''}
                    </td>
                `;
                            tbody.appendChild(row);
                        });
                    }

                    // Пагинация (оставляем как есть или закомментируй, если пока не нужна)
                    // ... код пагинации ...

                    // Удаление (без confirm, с двойным кликом)
                    document.querySelectorAll('.delete-btn').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            const id = this.dataset.id;

                            if (!this.classList.contains('confirm-delete')) {
                                this.classList.add('confirm-delete');
                                this.textContent = 'Уверены?';
                                this.classList.replace('btn-outline-danger', 'btn-danger');
                                setTimeout(() => {
                                    if (this.classList.contains('confirm-delete')) {
                                        this.classList.remove('confirm-delete');
                                        this.textContent = 'Удалить';
                                        this.classList.replace('btn-danger', 'btn-outline-danger');
                                    }
                                }, 3000);
                                return;
                            }

                            try {
                                await apiRequest('DELETE', `/project/${id}`);
                                this.closest('tr').remove();
                                if (tbody.children.length === 0) noProjects.style.display = 'block';
                                showMessage('success', 'Проект удалён');
                            } catch (err) {
                                showMessage('danger', err.message || 'Не удалось удалить проект');
                            }
                        });
                    });

                } catch (err) {
                    console.error('Ошибка в loadProjects:', err);
                    showMessage('danger', err.message || 'Не удалось загрузить проекты');
                }
            }

            // Фильтры
            document.getElementById('filterForm')?.addEventListener('submit', e => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const filters = Object.fromEntries(formData);
                loadProjects(filters);
            });

            // Инициализация
            document.addEventListener('DOMContentLoaded', () => {
                loadProjects();
            });
        </script>
    @endsection
@endsection
