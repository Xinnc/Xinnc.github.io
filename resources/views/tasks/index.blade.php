@extends('layouts.app')
@section('title', 'Все задачи')

@section('content')
    <h1 class="h3 mb-4">Все задачи</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Статус</label>
                    <select class="form-select" name="status">
                        <option value="">Все</option>
                        <option value="open">Открыта</option>
                        <option value="in_progress">В работе</option>
                        <option value="blocked">Заблокирована</option>
                        <option value="done">Выполнена</option>
                        <option value="cancelled">Отменена</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Поиск</label>
                    <input type="text" class="form-control" name="search" placeholder="Название задачи">
                </div>
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-outline-secondary w-100">Фильтровать</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица задач -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="tasksTable">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Проект</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="card-footer text-center" id="noTasks" style="display:none;">
            <p class="text-muted mb-0">Задач пока нет</p>
        </div>
        <div class="card-footer" id="pagination"></div>
    </div>

    @section('scripts')
        <script>
            // Вспомогательные функции (копируем из других страниц)
            function getStatusColor(status) {
                const colors = {
                    'open': 'info',
                    'in_progress': 'primary',
                    'blocked': 'danger',
                    'done': 'success',
                    'cancelled': 'secondary'
                };
                return colors[status] || 'secondary';
            }

            function getStatusText(status) {
                const texts = {
                    'open': 'Открыта',
                    'in_progress': 'В работе',
                    'blocked': 'Заблокирована',
                    'done': 'Выполнена',
                    'cancelled': 'Отменена'
                };
                return texts[status] || status;
            }

            function showMessage(type, text) {
                const div = document.getElementById('message');
                div.className = `alert alert-${type} mb-4`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 5000);
            }

            async function loadTasks(filters = {}, page = 1) {
                const tbody = document.querySelector('#tasksTable tbody');
                const noTasks = document.getElementById('noTasks');
                const pagination = document.getElementById('pagination');

                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Загрузка...</td></tr>';
                noTasks.style.display = 'none';
                pagination.innerHTML = '';

                try {
                    let query = new URLSearchParams(filters).toString();
                    if (page > 1) query += (query ? '&' : '') + `page=${page}`;

                    const response = await apiRequest('GET', `/tasks?${query}`);
                    console.log('Все задачи:', response);

                    const tasks = response.data || [];
                    const meta = response.meta || {};

                    tbody.innerHTML = '';

                    if (tasks.length === 0) {
                        noTasks.style.display = 'block';
                        return;
                    }

                    tasks.forEach(task => {
                        const project = task.project || {};
                        const row = document.createElement('tr');
                        row.innerHTML = `
                <td>${task.id}</td>
                <td>${task.name || '-'}</td>
                <td>${task.description || '-'}</td>
                <td>${project.project_name || '-'}</td>
                <td><span class="badge bg-${getStatusColor(task.status)}">${getStatusText(task.status)}</span></td>
                <td>
                    <a href="/projects/${project.project_id}/task/${task.id}" class="btn btn-sm btn-outline-info">Просмотр</a>
                </td>
            `;
                        tbody.appendChild(row);
                    });

                    // Пагинация
                    if (meta.last_page > 1) {
                        let html = '<nav><ul class="pagination justify-content-center mb-0">';
                        html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${meta.current_page - 1}">Предыдущая</a>
            </li>`;

                        for (let i = 1; i <= meta.last_page; i++) {
                            html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
                        }

                        html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${meta.current_page + 1}">Следующая</a>
            </li>`;
                        html += '</ul></nav>';
                        pagination.innerHTML = html;

                        pagination.querySelectorAll('.page-link').forEach(link => {
                            link.addEventListener('click', e => {
                                e.preventDefault();
                                const page = parseInt(link.dataset.page);
                                if (!isNaN(page)) loadTasks(filters, page);
                            });
                        });
                    }

                } catch (err) {
                    console.error('Ошибка загрузки задач:', err);
                    tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center py-4">Ошибка: ${err.message}</td></tr>`;
                }
            }

            document.addEventListener('DOMContentLoaded', () => loadTasks());

            document.getElementById('filterForm')?.addEventListener('submit', e => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const filters = Object.fromEntries(formData);
                loadTasks(filters);
            });
        </script>
    @endsection
@endsection
