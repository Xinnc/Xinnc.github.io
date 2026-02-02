@extends('layouts.app')
@section('title', 'Программы')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Программы</h1>
        <a href="/programs/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Новая программа
        </a>
    </div>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="programsTable">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Статус</th>
                    <th>Создано</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="card-footer text-center" id="noPrograms" style="display:none;">
            <p class="text-muted mb-0">Программ пока нет</p>
        </div>
        <div class="card-footer" id="pagination"></div>
    </div>

    @section('scripts')
        <script>
            // Форматирование даты
            function formatDate(iso) {
                if (!iso) return '-';
                const date = new Date(iso);
                if (isNaN(date.getTime())) return '-';
                return date.toLocaleString('ru-RU', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Сообщения
            function showMessage(type, text) {
                const div = document.getElementById('message');
                div.className = `alert alert-${type} mb-4`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 5000);
            }

            // Загрузка программ
            async function loadPrograms(page = 1) {
                const tbody = document.querySelector('#programsTable tbody');
                const noPrograms = document.getElementById('noPrograms');
                const pagination = document.getElementById('pagination');

                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Загрузка...</td></tr>';
                noPrograms.style.display = 'none';
                pagination.innerHTML = '';

                try {
                    const response = await apiRequest('GET', `/program?page=${page}`);
                    const programs = response.programs || [];
                    const meta = response.meta || {};

                    tbody.innerHTML = '';

                    if (programs.length === 0) {
                        noPrograms.style.display = 'block';
                        return;
                    }

                    programs.forEach(program => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                <td>${program.id}</td>
                <td>${program.name || '-'}</td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input status-toggle" type="checkbox"
                               id="toggle-${program.id}"
                               ${program.is_active ? 'checked' : ''}
                               data-id="${program.id}">
                        <label class="form-check-label" for="toggle-${program.id}">
                            ${program.is_active ? 'Активна' : 'Неактивна'}
                        </label>
                    </div>
                </td>
                <td>${formatDate(program.created_at)}</td>
                <td>
                    <a href="/programs/${program.id}" class="btn btn-sm btn-outline-info">Просмотр</a>
                    <a href="/programs/${program.id}/edit" class="btn btn-sm btn-outline-warning">Редактировать</a>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${program.id}">Удалить</button>
                </td>
            `;
                        tbody.appendChild(row);
                    });

                    // Пагинация (если есть)
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
                                if (!isNaN(page)) loadPrograms(page);
                            });
                        });
                    }

                    // Обработчик переключения статуса (мгновенно)
                    document.querySelectorAll('.status-toggle').forEach(toggle => {
                        toggle.addEventListener('change', async function() {
                            const id = this.dataset.id;
                            const isActive = this.checked;
                            const label = this.nextElementSibling;

                            // Визуально показываем, что идёт запрос
                            label.textContent = 'Сохранение...';
                            this.disabled = true;

                            try {
                                await apiRequest('PATCH', `/program/${id}/status`, { is_active: isActive ? 1 : 0 });
                                showMessage('success', `Статус изменён на ${isActive ? 'Активна' : 'Неактивна'}`);
                                label.textContent = isActive ? 'Активна' : 'Неактивна';
                            } catch (err) {
                                showMessage('danger', err.message || 'Не удалось изменить статус');
                                this.checked = !isActive; // откатываем переключатель
                                label.textContent = !isActive ? 'Активна' : 'Неактивна';
                            } finally {
                                this.disabled = false;
                            }
                        });
                    });

                    // Удаление (двойной клик) — оставляем как было
                    document.querySelectorAll('.delete-btn').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            const idStr = this.getAttribute('data-id');
                            const id = parseInt(idStr, 10);

                            if (isNaN(id) || id <= 0) {
                                showMessage('danger', 'Неверный ID программы');
                                return;
                            }

                            if (!this.classList.contains('confirm-delete')) {
                                this.classList.add('confirm-delete');
                                this.textContent = 'Уверены?';
                                this.classList.add('btn-danger');

                                setTimeout(() => {
                                    if (this.classList.contains('confirm-delete')) {
                                        this.classList.remove('confirm-delete', 'btn-danger');
                                        this.textContent = 'Удалить';
                                    }
                                }, 4000);
                                return;
                            }

                            try {
                                await apiRequest('DELETE', `/program/${id}`);
                                showMessage('success', 'Программа удалена');
                                this.closest('tr').remove();
                                if (tbody.querySelectorAll('tr').length === 0) {
                                    noPrograms.style.display = 'block';
                                }
                            } catch (err) {
                                showMessage('danger', err.message || 'Не удалось удалить программу');
                            }

                            this.classList.remove('confirm-delete', 'btn-danger');
                            this.textContent = 'Удалить';
                        });
                    });

                } catch (err) {
                    console.error('Ошибка загрузки программ:', err);
                    tbody.innerHTML = `<tr><td colspan="5" class="text-danger text-center py-4">Ошибка: ${err.message || 'Неизвестная ошибка'}</td></tr>`;
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                loadPrograms();
            });
        </script>
    @endsection
@endsection
