@extends('layouts.app')
@section('title', 'Записи времени')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Записи времени</h1>
        <a href="/time-entries/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Новая запись
        </a>
    </div>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="timeEntriesTable">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Проект</th>
                    <th>Задача</th>
                    <th>Начало</th>
                    <th>Конец</th>
                    <th>Длительность</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="card-footer text-center" id="noEntries" style="display:none;">
            <p class="text-muted mb-0">Записей пока нет</p>
        </div>
        <div class="card-footer" id="pagination"></div>
    </div>

    @section('scripts')
        <script>
            // 1. Форматирование даты
            function formatDateTime(iso) {
                if (!iso) return '-';
                const date = new Date(iso);
                if (isNaN(date.getTime())) return '-';
                return date.toLocaleString('ru-RU', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
            }

            // 2. Форматирование длительности по секундам
            function formatDuration(seconds) {
                if (!seconds || seconds <= 0) return '-';
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = seconds % 60;
                return `${h}ч ${m}мин ${s}сек`;
            }

            // 3. Сообщения
            function showMessage(type, text) {
                const div = document.getElementById('message');
                if (!div) return;
                div.className = `alert alert-${type} mb-4`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 5000);
            }

            // 4. Загрузка списка
            async function loadTimeEntries(page = 1) {
                const tbody = document.querySelector('#timeEntriesTable tbody');
                const noEntries = document.getElementById('noEntries');
                const pagination = document.getElementById('pagination');

                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">Загрузка...</td></tr>';
                noEntries.style.display = 'none';
                pagination.innerHTML = '';

                try {
                    const response = await apiRequest('GET', `/time_entry?page=${page}`);
                    console.log('Записи:', response);

                    const entries = response.data || [];
                    const meta = response.meta || {};

                    tbody.innerHTML = '';

                    if (entries.length === 0) {
                        noEntries.style.display = 'block';
                        return;
                    }

                    entries.forEach(entry => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>${entry.id}</td>
                        <td>${entry.user || '-'}</td>
                        <td>${entry.project || '-'}</td>
                        <td>${entry.task || '-'}</td>
                        <td>${formatDateTime(entry.start_time)}</td>
                        <td>${formatDateTime(entry.end_time)}</td>
                        <td>${formatDuration(entry.duration_seconds)}</td>
                        <td>
                            <a href="/time-entries/${entry.id}" class="btn btn-sm btn-outline-info">Просмотр</a>
                            <a href="/time-entries/${entry.id}/edit" class="btn btn-sm btn-outline-warning">Редактировать</a>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${entry.id}">Удалить</button>
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
                                if (!isNaN(page)) loadTimeEntries(page);
                            });
                        });
                    }

                    // Удаление записи (двойное нажатие)
                    document.querySelectorAll('.delete-btn').forEach(btn => {
                        btn.addEventListener('click', async function() {
                            const idStr = this.getAttribute('data-id'); // ← используем getAttribute
                            const id = parseInt(idStr, 10);

                            console.log('Удаление ID:', idStr, '→', id);

                            if (isNaN(id) || id <= 0) {
                                showMessage('danger', 'Неверный ID записи');
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
                                await apiRequest('DELETE', `/time_entry/${id}`);
                                showMessage('success', 'Запись удалена');
                                this.closest('tr').remove();
                                if (tbody.querySelectorAll('tr').length === 0) {
                                    noEntries.style.display = 'block';
                                }
                            } catch (err) {
                                showMessage('danger', err.message || 'Не удалось удалить запись');
                            }

                            this.classList.remove('confirm-delete', 'btn-danger');
                            this.textContent = 'Удалить';
                        });
                    });

                } catch (err) {
                    console.error('Ошибка загрузки:', err);
                    tbody.innerHTML = `<tr><td colspan="8" class="text-danger text-center py-4">Ошибка: ${err.message || 'Неизвестная ошибка'}</td></tr>`;
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                loadTimeEntries();
            });
        </script>
    @endsection
@endsection
