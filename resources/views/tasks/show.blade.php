@extends('layouts.app')
@section('title', 'Задача')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Задача: <span id="taskName">-</span></h1>
        <div>
            <a href="/projects/{{ $projectId }}" class="btn btn-outline-secondary me-2">Назад к проекту</a>
            <a href="/projects/{{ $projectId }}/task/{{ $taskId }}/edit" class="btn btn-warning" id="editBtn" style="display:none;">
                Редактировать
            </a>
            <button class="btn btn-outline-danger" id="deleteTaskBtn" style="display:none;" onclick="deleteTask()">
                Удалить задачу
            </button>
        </div>
    </div>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Информация о задаче</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8" id="taskId">-</dd>

                        <dt class="col-sm-4">Название</dt>
                        <dd class="col-sm-8" id="taskNameFull">-</dd>

                        <dt class="col-sm-4">Описание</dt>
                        <dd class="col-sm-8" id="taskDesc">-</dd>

                        <dt class="col-sm-4">Проект</dt>
                        <dd class="col-sm-8" id="taskProject">-</dd>

                        <dt class="col-sm-4">Статус</dt>
                        <dd class="col-sm-8">
                            <span class="badge fs-6" id="taskStatus">-</span>
                        </dd>
                    </dl>
                </div>

                <!-- Блок смены статуса — справа от данных -->
                <div class="col-md-6" id="statusSection" style="display:none;">
                    <h5>Изменить статус</h5>
                    <div class="btn-group flex-wrap" role="group">
                        <button class="btn btn-outline-info btn-sm me-1 mb-1 status-task-btn" data-status="open" data-original-text="Открыта">Открыта</button>
                        <button class="btn btn-outline-primary btn-sm me-1 mb-1 status-task-btn" data-status="in_progress" data-original-text="В работе">В работе</button>
                        <button class="btn btn-outline-danger btn-sm me-1 mb-1 status-task-btn" data-status="blocked" data-original-text="Заблокирована">Заблокирована</button>
                        <button class="btn btn-outline-success btn-sm me-1 mb-1 status-task-btn" data-status="done" data-original-text="Выполнена">Выполнена</button>
                        <button class="btn btn-outline-secondary btn-sm mb-1 status-task-btn" data-status="cancelled" data-original-text="Отменена">Отменена</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            // ────────────────────────────────────────────────
            // Получаем ID из Blade (обязательно!)
            // ────────────────────────────────────────────────
            const projectId = parseInt('{{ $projectId ?? 0 }}', 10);
            const taskId    = parseInt('{{ $taskId    ?? 0 }}', 10);

            console.log('projectId:', projectId);
            console.log('taskId:', taskId);

            if (isNaN(projectId) || isNaN(taskId)) {
                console.error('ID проекта или задачи не переданы!');
                document.getElementById('message').innerHTML = 'Ошибка: ID не определён';
                document.getElementById('message').className = 'alert alert-danger';
                document.getElementById('message').style.display = 'block';
            }

            // ────────────────────────────────────────────────
            // Вспомогательные функции
            // ────────────────────────────────────────────────
            function getStatusColor(status) {
                const map = {
                    'open': 'info',
                    'in_progress': 'primary',
                    'blocked': 'danger',
                    'done': 'success',
                    'cancelled': 'secondary'
                };
                return map[status] || 'secondary';
            }

            function getStatusText(status) {
                const map = {
                    'open': 'Открыта',
                    'in_progress': 'В работе',
                    'blocked': 'Заблокирована',
                    'done': 'Выполнена',
                    'cancelled': 'Отменена'
                };
                return map[status] || status;
            }

            function showMessage(type, text) {
                const div = document.getElementById('message');
                if (!div) return;
                div.className = `alert alert-${type} mb-4`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 7000);
            }

            // Сброс кнопки в исходное состояние
            function resetButton(btn) {
                if (!btn) return;
                clearTimeout(btn.dataset.timeoutId);

                btn.classList.remove('confirm-status', 'btn-danger');

                // Восстанавливаем правильный outline-класс
                const status = btn.dataset.status;
                const outlineClass = {
                    'open': 'btn-outline-info',
                    'in_progress': 'btn-outline-primary',
                    'blocked': 'btn-outline-danger',
                    'done': 'btn-outline-success',
                    'cancelled': 'btn-outline-secondary'
                }[status] || 'btn-outline-secondary';

                // Удаляем все возможные outline-классы
                ['btn-outline-info', 'btn-outline-primary', 'btn-outline-danger', 'btn-outline-success', 'btn-outline-secondary']
                    .forEach(cls => btn.classList.remove(cls));

                // Добавляем правильный
                btn.classList.add(outlineClass);

                // Текст
                btn.textContent = btn.dataset.originalText || btn.textContent;
            }

            // ────────────────────────────────────────────────
            // Загрузка задачи
            // ────────────────────────────────────────────────
            async function loadTask() {
                console.log('Загружаем задачу...');

                try {
                    const response = await apiRequest('GET', `/project/${projectId}/task/${taskId}`);
                    const task = response.task || response; // на случай разной структуры

                    console.log('Задача:', task);

                    document.getElementById('taskId').textContent = task.id || '-';
                    document.getElementById('taskName').textContent = task.name || 'Без названия';
                    document.getElementById('taskNameFull').textContent = task.name || '-';
                    document.getElementById('taskDesc').textContent = task.description || 'Нет описания';

                    const project = task.project || {};
                    document.getElementById('taskProject').textContent = project.project_name || '-';

                    const statusEl = document.getElementById('taskStatus');
                    statusEl.textContent = getStatusText(task.status);
                    statusEl.className = `badge bg-${getStatusColor(task.status)} fs-6`;

                    // Права доступа
                    const userRole = (localStorage.getItem('user_role') || 'user').toLowerCase();
                    const canManage = ['manager', 'admin'].includes(userRole);
                    const deleteBtn = document.getElementById('deleteTaskBtn');
                    if (deleteBtn) {
                        deleteBtn.style.display = canManage ? 'inline-block' : 'none';

                        // Сохраняем исходное состояние кнопки
                        if (!deleteBtn.dataset.originalText) {
                            deleteBtn.dataset.originalText = deleteBtn.textContent.trim();
                            deleteBtn.dataset.originalClass = deleteBtn.className;
                        }

                        deleteBtn.addEventListener('click', async function() {
                            if (!this.classList.contains('confirm-delete')) {
                                // Первое нажатие
                                this.classList.add('confirm-delete');
                                this.textContent = 'Уверены?';
                                this.classList.add('btn-danger');

                                this.dataset.timeoutId = setTimeout(() => resetDeleteButton(this), 4000);
                                return;
                            }

                            // Второе нажатие — удаляем
                            clearTimeout(this.dataset.timeoutId);

                            try {
                                await apiRequest('DELETE', `/project/${projectId}/task/${taskId}`);
                                showMessage('success', 'Задача удалена');

                                // Сбрасываем кнопку (на всякий случай)
                                resetDeleteButton(this);

                                // Редирект на проект
                                location.href = `/projects/${projectId}`;
                            } catch (err) {
                                showMessage('danger', err.message || 'Не удалось удалить задачу');
                                resetDeleteButton(this);
                            }
                        });
                    }
                    document.getElementById('editBtn').style.display = canManage ? 'inline-block' : 'none';
                    document.getElementById('statusSection').style.display = canManage ? 'block' : 'none';


                    async function deleteTask() {
                        if (!confirm('Удалить задачу? Это действие нельзя отменить.')) return;

                        try {
                            await apiRequest('DELETE', `/project/${projectId}/task/${taskId}`);
                            showMessage('success', 'Задача удалена');
                            location.href = `/projects/${projectId}`;
                        } catch (err) {
                            showMessage('danger', err.message || 'Не удалось удалить задачу');
                        }
                    }
                    // Смена статуса задачи (двойной клик)
                    document.querySelectorAll('.status-task-btn').forEach(btn => {
                        if (!btn.dataset.originalText) {
                            btn.dataset.originalText = btn.textContent.trim();
                            btn.dataset.originalClass = btn.className;
                        }

                        btn.addEventListener('click', async function() {
                            const status = this.dataset.status;

                            if (!this.classList.contains('confirm-status')) {
                                this.classList.add('confirm-status');
                                this.textContent = 'Подтвердить?';
                                this.classList.add('btn-danger');

                                const timeout = setTimeout(() => resetButton(this), 4000);
                                this.dataset.timeoutId = timeout;
                                return;
                            }

                            clearTimeout(this.dataset.timeoutId);

                            try {
                                await apiRequest('PATCH', `/project/${projectId}/task/${taskId}/status`, { status });
                                showMessage('success', `Статус изменён на «${getStatusText(status)}»`);

                                // Сбрасываем все кнопки
                                document.querySelectorAll('.status-task-btn').forEach(resetButton);

                                loadTask(); // обновляем

                            } catch (err) {
                                showMessage('danger', err.message || 'Не удалось изменить статус');
                                resetButton(this);
                            }
                        });
                    });

                } catch (err) {
                    console.error('Ошибка:', err);
                    showMessage('danger', err.message || 'Не удалось загрузить задачу');
                }
            }

            function resetDeleteButton(btn) {
                if (!btn) return;
                clearTimeout(btn.dataset.timeoutId);

                btn.classList.remove('confirm-delete', 'btn-danger');
                btn.textContent = btn.dataset.originalText || 'Удалить задачу';
                btn.className = btn.dataset.originalClass || 'btn btn-outline-danger';
            }

            // Запуск
            document.addEventListener('DOMContentLoaded', () => {
                console.log('Страница загружена');
                loadTask();
            });
        </script>
    @endsection
@endsection
