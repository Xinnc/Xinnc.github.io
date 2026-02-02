@extends('layouts.app')
@section('title', 'Админ-панель')

@section('content')
    <h1 class="h3 mb-4">Админ-панель</h1>

    <div id="globalMessage" class="mb-4" style="display:none;"></div>

    <!-- Статистика системы -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Статистика системы</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="alert alert-info h-100">
                        <h6>Пользователи</h6>
                        <p>Всего: <strong id="usersTotal">-</strong></p>
                        <p>Активные: <strong id="usersActive">-</strong></p>
                        <p>Забаненные: <strong id="usersBanned">-</strong></p>
                        <p>Админы: <strong id="usersAdmins">-</strong></p>
                        <p>Менеджеры: <strong id="usersManagers">-</strong></p>
                        <p>Новых сегодня: <strong id="usersNewToday">-</strong></p>
                        <a href="/admin/users" class="btn btn-sm btn-light mt-2">Перейти к списку</a>
                    </div>
                </div>

                <!-- Остальные карточки аналогично -->
                <div class="col-md-3">
                    <div class="alert alert-success h-100">
                        <h6>Записи времени</h6>
                        <p>Всего: <strong id="timeEntriesTotal">-</strong></p>
                        <p>Активные: <strong id="timeEntriesActive">-</strong></p>
                        <p>Общее время (мин): <strong id="timeEntriesTotalMinutes">-</strong></p>
                        <p>Сегодня (мин): <strong id="timeEntriesTodayMinutes">-</strong></p>
                        <a href="/time-entries" class="btn btn-sm btn-light mt-2">Перейти к записям</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="alert alert-primary h-100">
                        <h6>Программы</h6>
                        <p>Всего: <strong id="programsTotal">-</strong></p>
                        <p>Активные: <strong id="programsActive">-</strong></p>
                        <p>Неактивные: <strong id="programsInactive">-</strong></p>
                        <a href="/programs" class="btn btn-sm btn-light mt-2">Перейти к программам</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="alert alert-warning h-100">
                        <h6>Проекты</h6>
                        <p>Всего: <strong id="projectsTotal">-</strong></p>
                        <p>Активные: <strong id="projectsActive">-</strong></p>
                        <p>На паузе: <strong id="projectsPaused">-</strong></p>
                        <p>Завершённые: <strong id="projectsCompleted">-</strong></p>
                        <a href="/projects" class="btn btn-sm btn-light mt-2">Перейти к проектам</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="alert alert-secondary h-100">
                        <h6>Задачи</h6>
                        <p>Всего: <strong id="tasksTotal">-</strong></p>
                        <p>Открытые: <strong id="tasksOpen">-</strong></p>
                        <p>В работе: <strong id="tasksInProgress">-</strong></p>
                        <p>Завершённые: <strong id="tasksDone">-</strong></p>
                        <p>Заблокированные: <strong id="tasksBlocked">-</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Управление ролями -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Управление ролями</h5>
        </div>
        <div class="card-body">
            <div id="roleMessage" class="mb-3" style="display:none;"></div>

            <!-- Добавление роли -->
            <div class="mb-4">
                <h6>Добавить роль</h6>
                <form id="addRoleForm" class="input-group">
                    <input type="text" class="form-control" name="role" placeholder="Название роли" required>
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </form>
            </div>

            <!-- Список ролей -->
            <h6>Существующие роли</h6>
            <table class="table table-hover" id="rolesTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody id="rolesBody">
                <tr><td colspan="3" class="text-center py-3">Загрузка ролей...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Force stop time entry -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Принудительная остановка таймера</h5>
        </div>
        <div class="card-body">
            <div id="timeEntryMessage" class="mb-3" style="display:none;"></div>

            <form id="forceStopForm" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Выберите пользователя</label>
                    <select class="form-select" id="userSelect" name="user_id" required>
                        <option value="">Загрузка пользователей...</option>
                    </select>
                </div>
                <div class="col-md-6 align-self-end">
                    <button type="submit" class="btn btn-danger">Остановить таймер</button>
                </div>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            // Универсальное сообщение
            function showMessage(elementId, type, text) {
                const div = document.getElementById(elementId);
                if (!div) return;
                div.className = `alert alert-${type} mb-3`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 7000);
            }

            // Статистика системы
            async function loadSystemStats() {
                try {
                    const response = await apiRequest('GET', '/system/stat');
                    const stats = response[0] || {};

                    document.getElementById('usersTotal').textContent = stats.users?.total || '-';
                    document.getElementById('usersActive').textContent = stats.users?.active || '-';
                    document.getElementById('usersBanned').textContent = stats.users?.banned || '-';
                    document.getElementById('usersAdmins').textContent = stats.users?.admins || '-';
                    document.getElementById('usersManagers').textContent = stats.users?.managers || '-';
                    document.getElementById('usersNewToday').textContent = stats.users?.new_today || '-';

                    document.getElementById('timeEntriesTotal').textContent = stats.time_entries?.total || '-';
                    document.getElementById('timeEntriesActive').textContent = stats.time_entries?.active || '-';
                    document.getElementById('timeEntriesTotalMinutes').textContent = stats.time_entries?.total_minutes || '-';
                    document.getElementById('timeEntriesTodayMinutes').textContent = stats.time_entries?.today_minutes || '-';

                    document.getElementById('programsTotal').textContent = stats.programs?.total || '-';
                    document.getElementById('programsActive').textContent = stats.programs?.active || '-';
                    document.getElementById('programsInactive').textContent = stats.programs?.inactive || '-';

                    document.getElementById('projectsTotal').textContent = stats.projects?.total || '-';
                    document.getElementById('projectsActive').textContent = stats.projects?.active || '-';
                    document.getElementById('projectsPaused').textContent = stats.projects?.paused || '-';
                    document.getElementById('projectsCompleted').textContent = stats.projects?.completed || '-';

                    document.getElementById('tasksTotal').textContent = stats.tasks?.total || '-';
                    document.getElementById('tasksOpen').textContent = stats.tasks?.open || '-';
                    document.getElementById('tasksInProgress').textContent = stats.tasks?.in_progress || '-';
                    document.getElementById('tasksDone').textContent = stats.tasks?.done || '-';
                    document.getElementById('tasksBlocked').textContent = stats.tasks?.blocked || '-';
                } catch (err) {
                    showMessage('globalMessage', 'danger', err.message || 'Не удалось загрузить статистику');
                }
            }

            // Роли
            async function loadRolesTable() {
                const tbody = document.querySelector('#rolesTable tbody');
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3">Загрузка...</td></tr>';

                try {
                    const response = await apiRequest('GET', '/role');
                    const roles = response.roles || response.data || response;

                    tbody.innerHTML = '';

                    roles.forEach(role => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                <td>${role.id}</td>
                <td>${role.role}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger delete-role-btn" data-id="${role.id}" data-original-text="Удалить">
                        Удалить
                    </button>
                </td>
            `;
                        tbody.appendChild(row);
                    });

                    // Двойной клик на удаление роли
                    document.querySelectorAll('.delete-role-btn').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            const id = this.dataset.id;

                            if (!this.classList.contains('confirm-delete')) {
                                this.classList.add('confirm-delete');
                                this.textContent = 'Уверены?';
                                this.classList.add('btn-danger');

                                setTimeout(() => {
                                    if (this.classList.contains('confirm-delete')) {
                                        this.classList.remove('confirm-delete', 'btn-danger');
                                        this.textContent = this.dataset.originalText || 'Удалить';
                                    }
                                }, 4000);
                                return;
                            }

                            try {
                                await apiRequest('DELETE', `/role/${id}`);
                                showMessage('roleMessage', 'success', 'Роль удалена');
                                loadRolesTable();
                            } catch (err) {
                                showMessage('roleMessage', 'danger', err.message || 'Не удалось удалить роль');
                            }

                            this.classList.remove('confirm-delete', 'btn-danger');
                            this.textContent = this.dataset.originalText || 'Удалить';
                        });
                    });
                } catch (err) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-danger text-center py-3">Ошибка загрузки ролей</td></tr>';
                    showMessage('roleMessage', 'danger', err.message || 'Не удалось загрузить роли');
                }
            }

            // Добавление роли
            document.getElementById('addRoleForm')?.addEventListener('submit', async e => {
                e.preventDefault();

                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData);

                try {
                    await apiRequest('POST', '/role', data);
                    showMessage('roleMessage', 'success', 'Роль добавлена');
                    e.target.reset();
                    loadRolesTable();
                } catch (err) {
                    showMessage('roleMessage', 'danger', err.message || 'Не удалось добавить роль');
                }
            });

            // Force stop time entry
            document.getElementById('forceStopForm')?.addEventListener('submit', async e => {
                e.preventDefault();

                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData);

                try {
                    await apiRequest('PATCH', `/user/${data.user_id}/stop/time_entry`);
                    showMessage('timeEntryMessage', 'success', 'Таймер пользователя принудительно остановлен');
                    e.target.reset();
                } catch (err) {
                    showMessage('timeEntryMessage', 'danger', err.message || 'Не удалось остановить таймер');
                }
            });

            // Загрузка пользователей для селекта force stop
            async function loadUsersForSelect() {
                const select = document.getElementById('userSelect');
                select.innerHTML = '<option value="" disabled selected>Загрузка...</option>';

                try {
                    const response = await apiRequest('GET', '/users');
                    const users = response.data || [];

                    select.innerHTML = '<option value="">Выберите пользователя...</option>';

                    users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = `${user.first_name} ${user.surname} (${user.email})`;
                        select.appendChild(option);
                    });
                } catch (err) {
                    select.innerHTML = '<option value="">Ошибка загрузки</option>';
                    showMessage('timeEntryMessage', 'danger', 'Не удалось загрузить пользователей');
                }
            }

            // Запуск
            document.addEventListener('DOMContentLoaded', async () => {
                await loadSystemStats();
                await loadRolesTable();
                await loadUsersForSelect();
            });
        </script>
    @endsection
@endsection
