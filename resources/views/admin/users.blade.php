@extends('layouts.app')
@section('title', 'Пользователи — Админ')

@section('content')
    <h1 class="h3 mb-4">Пользователи</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Роль</label>
                    <select class="form-select" name="role_id" id="roleFilter">
                        <option value="">Все роли</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Статус</label>
                    <select class="form-select" name="is_banned">
                        <option value="">Все</option>
                        <option value="1">Забаненные</option>
                        <option value="0">Активные</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Поиск</label>
                    <input type="text" class="form-control" name="search" placeholder="Имя, фамилия или email">
                </div>
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-outline-secondary w-100">Фильтровать</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="usersTable">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="card-footer text-center" id="noUsers" style="display:none;">
            <p class="text-muted mb-0">Пользователей нет</p>
        </div>
        <div class="card-footer" id="pagination"></div>
    </div>

    <!-- Модалка статистики пользователя -->
    <div class="modal fade" id="userStatModal" tabindex="-1" aria-labelledby="userStatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userStatModalLabel">Статистика пользователя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userStatContent">
                    <p class="text-center">Загрузка...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            // Глобальные переменные
            let rolesList = [];
            let currentFilters = {};
            let currentPage = 1;

            // Сообщения (с деталями ошибок)
            function showMessage(type, text) {
                const div = document.getElementById('message');
                if (!div) return;
                div.className = `alert alert-${type} mb-4`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 7000);
            }

            // 1. Загрузка ролей
            async function loadRoles() {
                try {
                    const response = await apiRequest('GET', '/role');
                    rolesList = response.roles || response.data || response;
                    console.log('Роли загружены:', rolesList);

                    // Фильтр ролей
                    const roleFilter = document.getElementById('roleFilter');
                    roleFilter.innerHTML = '<option value="">Все роли</option>';
                    rolesList.forEach(role => {
                        const option = document.createElement('option');
                        option.value = role.id;
                        option.textContent = role.role;
                        roleFilter.appendChild(option);
                    });
                } catch (err) {
                    console.error('Ошибка загрузки ролей:', err);
                    showMessage('danger', err.message || 'Не удалось загрузить роли');
                }
            }

            // 2. Загрузка пользователей
            async function loadUsers(filters = {}, page = 1) {
                currentFilters = { ...filters };
                currentPage = page;

                const tbody = document.querySelector('#usersTable tbody');
                const noUsers = document.getElementById('noUsers');
                const pagination = document.getElementById('pagination');

                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Загрузка...</td></tr>';
                noUsers.style.display = 'none';
                pagination.innerHTML = '';

                let query = new URLSearchParams(filters).toString();
                if (page > 1) query += (query ? '&' : '') + `page=${page}`;
                query = query ? `?${query}` : '';

                try {
                    const response = await apiRequest('GET', `/users${query}`);
                    const users = response.data || [];
                    const meta = response.meta || {};

                    tbody.innerHTML = '';

                    if (users.length === 0) {
                        noUsers.style.display = 'block';
                        return;
                    }

                    users.forEach(user => {
                        const isBanned = user.is_banned;
                        const row = document.createElement('tr');
                        row.className = isBanned ? 'table-danger' : '';

                        // Находим название роли по role_id
                        const currentRole = rolesList.find(r => r.id === user.role_id) || { role: 'Неизвестно' };

                        row.innerHTML = `
                <td>${user.id}</td>
                <td>${user.first_name} ${user.surname} ${user.last_name || ''}</td>
                <td>${user.email}</td>
                <td>
                    <select class="form-select form-select-sm role-select" data-id="${user.id}" data-current-role-id="${user.role_id}">
                        ${rolesList.map(role => `
                            <option value="${role.id}" ${user.role_id === role.id ? 'selected' : ''}>
                                ${role.role}
                            </option>
                        `).join('')}
                    </select>
                </td>
                <td>
                    <span class="badge bg-${isBanned ? 'danger' : 'success'}">
                        ${isBanned ? 'Забанен' : 'Активен'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-info stat-btn me-1" data-id="${user.id}">
                        Статистика
                    </button>
                    <button class="btn btn-sm btn-outline-${isBanned ? 'success' : 'danger'} block-btn"
                            data-id="${user.id}"
                            data-banned="${isBanned}">
                        ${isBanned ? 'Разбанить' : 'Забанить'}
                    </button>
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
                                if (!isNaN(page)) loadUsers(currentFilters, page);
                            });
                        });
                    }

                    // Смена роли
                    document.querySelectorAll('.role-select').forEach(select => {
                        select.addEventListener('change', async function() {
                            const userId = this.dataset.id;
                            const roleId = parseInt(this.value, 10);

                            if (isNaN(roleId)) return;

                            try {
                                await apiRequest('PATCH', `/user/${userId}/role`, { role_id: roleId });
                                showMessage('success', 'Роль изменена');
                            } catch (err) {
                                let errorText = err.message || 'Не удалось изменить роль';

                                if (err.errors && typeof err.errors === 'object') {
                                    errorText += '<ul class="mb-0 ps-3 mt-2">';
                                    Object.entries(err.errors).forEach(([field, msgs]) => {
                                        msgs.forEach(msg => {
                                            errorText += `<li>${field}: ${msg}</li>`;
                                        });
                                    });
                                    errorText += '</ul>';
                                }

                                showMessage('danger', errorText);
                                this.value = this.dataset.currentRoleId; // откат
                            }
                        });
                    });

                    // Блокировка/разблокировка
                    document.querySelectorAll('.block-btn').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            const userId = this.dataset.id;
                            const isBanned = this.dataset.banned === 'true';
                            const endpoint = isBanned ? `/user/${userId}/unban` : `/user/${userId}/ban`;

                            try {
                                await apiRequest('PATCH', endpoint);
                                showMessage('success', `Пользователь ${isBanned ? 'разбанен' : 'забанен'}`);
                                loadUsers(currentFilters, currentPage); // обновляем текущую страницу
                            } catch (err) {
                                let errorText = err.message || 'Не удалось изменить статус';

                                if (err.errors && typeof err.errors === 'object') {
                                    errorText += '<ul class="mb-0 ps-3 mt-2">';
                                    Object.entries(err.errors).forEach(([field, msgs]) => {
                                        msgs.forEach(msg => {
                                            errorText += `<li>${field}: ${msg}</li>`;
                                        });
                                    });
                                    errorText += '</ul>';
                                }

                                showMessage('danger', errorText);
                            }
                        });
                    });

                    // Статистика пользователя (модалка)
                    document.querySelectorAll('.stat-btn').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            const userId = this.dataset.id;
                            const modal = new bootstrap.Modal(document.getElementById('userStatModal'));
                            const content = document.getElementById('userStatContent');

                            content.innerHTML = '<p class="text-center">Загрузка статистики...</p>';

                            try {
                                const response = await apiRequest('GET', `/user/${userId}/stat`);
                                const stat = response[0] || response;

                                content.innerHTML = `
                        <h6>${stat.user.first_name || ''} ${stat.user.surname || ''} (${stat.user.email})</h6>
                        <hr>
                        <p><strong>Записи времени:</strong></p>
                        <ul>
                            <li>Всего: ${stat.time_entries?.total || 0}</li>
                            <li>Активные: ${stat.time_entries?.active || 0}</li>
                            <li>Общее время (мин): ${stat.time_entries?.total_minutes || 0}</li>
                            <li>Сегодня (мин): ${stat.time_entries?.today_minutes || 0}</li>
                        </ul>
                        <p><strong>Проекты:</strong></p>
                        <ul>
                            <li>Управляет: ${stat.projects?.managed || 0}</li>
                            <li>Активные: ${stat.projects?.active || 0}</li>
                        </ul>
                        <p><strong>Задачи:</strong></p>
                        <ul>
                            <li>Назначено: ${stat.tasks?.assigned || 0}</li>
                            <li>Завершено: ${stat.tasks?.done || 0}</li>
                        </ul>
                    `;
                            } catch (err) {
                                content.innerHTML = `<p class="text-danger">Ошибка: ${err.message || 'Не удалось загрузить статистику'}</p>`;
                            }

                            modal.show();
                        });
                    });

                } catch (err) {
                    console.error('Ошибка загрузки пользователей:', err);
                    tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center py-4">Ошибка: ${err.message}</td></tr>`;
                }
            }

            // Фильтры
            document.getElementById('filterForm')?.addEventListener('submit', e => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const filters = Object.fromEntries(formData);
                loadUsers(filters, 1);
            });

            // Запуск
            document.addEventListener('DOMContentLoaded', async () => {
                await loadRoles(); // сначала роли
                loadUsers();       // потом пользователи
            });
        </script>
    @endsection
@endsection
