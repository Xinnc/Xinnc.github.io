@extends('layouts.app')
@section('title', 'Просмотр проекта')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Проект: <span id="projectName">-</span></h1>
        <div>
            <a href="/projects" class="btn btn-outline-secondary me-2">Назад к списку</a>
            <a href="/projects/{{ $id }}/edit" class="btn btn-warning me-2" id="editBtn" style="display:none;">
                Редактировать
            </a>
        </div>
    </div>

    <div id="message" class="mb-4" style="display:none;"></div>

    <!-- Основная информация о проекте -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Информация о проекте</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8" id="projectId">-</dd>

                        <dt class="col-sm-4">Название</dt>
                        <dd class="col-sm-8" id="projectNameFull">-</dd>

                        <dt class="col-sm-4">Описание</dt>
                        <dd class="col-sm-8" id="projectDesc">-</dd>

                        <dt class="col-sm-4">Дедлайн</dt>
                        <dd class="col-sm-8" id="projectDeadline">-</dd>

                        <dt class="col-sm-4">Менеджер</dt>
                        <dd class="col-sm-8" id="projectManager">-</dd>

                        <dt class="col-sm-4">Статус</dt>
                        <dd class="col-sm-8">
                            <span class="badge fs-6" id="projectStatus">-</span>
                        </dd>
                    </dl>
                </div>

                <div class="col-md-6">
                    <h5>Изменить статус</h5>
                    <div id="statusButtons" style="display:none;">
                        <button class="btn btn-outline-success btn-sm me-2 mb-2 status-btn" data-status="active">
                            Активный
                        </button>
                        <button class="btn btn-outline-warning btn-sm me-2 mb-2 status-btn" data-status="paused">На
                            паузе
                        </button>
                        <button class="btn btn-outline-primary btn-sm me-2 mb-2 status-btn" data-status="completed">
                            Завершён
                        </button>
                        <button class="btn btn-outline-secondary btn-sm mb-2 status-btn" data-status="archived">Архив
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Задачи проекта -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Задачи проекта</h5>
            <a href="/projects/{{ $id }}/task/create" class="btn btn-sm btn-primary" id="createTaskBtn"
               style="display:none;">
                <i class="bi bi-plus-lg"></i> Новая задача
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="tasksTable">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
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
        <div class="card-footer" id="tasksPagination"></div>
    </div>

    @section('scripts')
        <script>
            // ────────────────────────────────────────────────
            // Вспомогательные функции — должны быть ПЕРВЫМИ
            // ────────────────────────────────────────────────
            function getStatusColor(status) {
                const colors = {
                    'active': 'success',
                    'paused': 'warning',
                    'completed': 'primary',
                    'archived': 'secondary',
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
                    'active': 'Активный',
                    'paused': 'На паузе',
                    'completed': 'Завершён',
                    'archived': 'В архиве',
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
                if (!div) return;
                div.className = `alert alert-${type} mb-4`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 7000);
            }

            // ────────────────────────────────────────────────
            // Основные функции загрузки
            // ────────────────────────────────────────────────
            const projectId = parseInt('{{ $id }}', 10);

            async function loadProject() {
                try {
                    const project = await apiRequest('GET', `/project/${projectId}`);

                    // Заполняем поля
                    document.getElementById('projectId').textContent = project.id || '-';
                    document.getElementById('projectName').textContent = project.name || 'Без названия';
                    document.getElementById('projectNameFull').textContent = project.name || '-';
                    document.getElementById('projectDesc').textContent = project.description || 'Нет описания';
                    document.getElementById('projectDeadline').textContent = project.deadline || '-';

                    const statusEl = document.getElementById('projectStatus');
                    if (statusEl) {
                        statusEl.textContent = getStatusText(project.status);
                        statusEl.className = `badge bg-${getStatusColor(project.status)} fs-6`;
                    }

                    const manager = project.manager_id || {};
                    document.getElementById('projectManager').textContent =
                        manager.name ? `${manager.name} (${manager.email || '-'})` : '-';

                    // Права
                    const userRole = (localStorage.getItem('user_role') || 'user').toLowerCase();
                    const canManage = ['manager', 'admin'].includes(userRole);

                    const editBtn = document.getElementById('editBtn');
                    if (editBtn) editBtn.style.display = canManage ? 'inline-block' : 'none';

                    const statusButtons = document.getElementById('statusButtons');
                    if (statusButtons) statusButtons.style.display = canManage ? 'block' : 'none';

                    const createTaskBtn = document.getElementById('createTaskBtn');
                    if (createTaskBtn) createTaskBtn.style.display = canManage ? 'inline-block' : 'none';

                    document.querySelectorAll('.status-btn').forEach(btn => {
                        // Сохраняем исходные данные кнопки один раз
                        if (!btn.dataset.originalText) {
                            btn.dataset.originalText = btn.textContent.trim();
                            btn.dataset.originalClass = btn.className;
                        }

                        btn.addEventListener('click', async function(e) {
                            e.preventDefault(); // на всякий случай

                            const status = this.dataset.status;

                            if (!this.classList.contains('confirm-status')) {
                                // Первое нажатие
                                this.classList.add('confirm-status');
                                this.textContent = 'Подтвердить?';
                                this.classList.remove('btn-outline-*'); // убираем все outline классы
                                this.classList.add('btn-danger');

                                // Автосброс через 4 секунды
                                const timeout = setTimeout(() => resetButton(this), 4000);
                                this.dataset.timeoutId = timeout;

                                return;
                            }

                            // Второе нажатие — меняем статус
                            clearTimeout(this.dataset.timeoutId); // отменяем автосброс

                            try {
                                await apiRequest('PATCH', `/project/${projectId}/status`, { status });
                                showMessage('success', `Статус изменён на «${getStatusText(status)}»`);

                                // Важно: сначала сбрасываем кнопку, потом перезагружаем
                                resetButton(this);

                                // Перезагружаем данные проекта
                                loadProject();

                            } catch (err) {
                                showMessage('danger', err.message || 'Не удалось изменить статус');
                                resetButton(this); // сбрасываем при ошибке
                            }
                        });
                    });

// Функция сброса кнопки
                    function resetButton(btn) {
                        if (!btn) return;
                        clearTimeout(btn.dataset.timeoutId);
                        btn.classList.remove('confirm-status', 'btn-danger');
                        btn.textContent = btn.dataset.originalText || 'Статус';
                        btn.className = btn.dataset.originalClass; // полностью восстанавливаем класс
                    }
                } catch (err) {
                    showMessage('danger', err.message || 'Не удалось загрузить проект');
                }
            }

            async function loadTasks(page = 1) {
                const tbody = document.querySelector('#tasksTable tbody');
                const noTasks = document.getElementById('noTasks');
                const pagination = document.getElementById('tasksPagination');

                if (!tbody) return;

                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Загрузка задач...</td></tr>';
                if (noTasks) noTasks.style.display = 'none';
                if (pagination) pagination.innerHTML = '';

                try {
                    const response = await apiRequest('GET', `/project/${projectId}/task?page=${page}`);

                    const tasks = response.data || [];
                    const meta = response.meta || {};

                    tbody.innerHTML = '';

                    if (tasks.length === 0) {
                        if (noTasks) noTasks.style.display = 'block';
                        return;
                    }

                    tasks.forEach(task => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                <td>${task.id}</td>
                <td>${task.name || '-'}</td>
                <td>${task.description || '-'}</td>
                <td>
                    <span class="badge bg-${getStatusColor(task.status)}">
                        ${getStatusText(task.status)}
                    </span>
                </td>
                <td>
                    <a href="/projects/${projectId}/task/${task.id}" class="btn btn-sm btn-outline-info">Просмотр</a>
                </td>
            `;
                        tbody.appendChild(row);
                    });

                    // Пагинация задач
                    if (meta.last_page > 1 && pagination) {
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
                                if (!isNaN(page) && page > 0 && page <= meta.last_page) {
                                    loadTasks(page);
                                }
                            });
                        });
                    }

                } catch (err) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-danger text-center py-4">Ошибка: ${err.message || 'Не удалось загрузить задачи'}</td></tr>`;
                }
            }

            // ────────────────────────────────────────────────
            // Запуск при загрузке страницы
            // ────────────────────────────────────────────────
            document.addEventListener('DOMContentLoaded', () => {
                loadProject();
                loadTasks();
            });
        </script>
    @endsection
@endsection
