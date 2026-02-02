@extends('layouts.app')
@section('title', 'Запись времени')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Запись времени #{{ $id }}</h1>
        <div>
            <a href="/time-entries" class="btn btn-outline-secondary me-2">Назад к списку</a>
            <a href="/time-entries/{{ $id }}/edit" class="btn btn-warning me-2" id="editBtn" style="display:none;">
                Редактировать
            </a>
            <button class="btn btn-outline-danger" id="deleteBtn" style="display:none;">
                Удалить
            </button>
        </div>
    </div>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Детали записи</h5>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">ID</dt>
                <dd class="col-sm-8" id="entryId">-</dd>

                <dt class="col-sm-4">Пользователь</dt>
                <dd class="col-sm-8" id="user">-</dd>

                <dt class="col-sm-4">Проект</dt>
                <dd class="col-sm-8" id="project">-</dd>

                <dt class="col-sm-4">Задача</dt>
                <dd class="col-sm-8" id="task">-</dd>

                <dt class="col-sm-4">Программа</dt>
                <dd class="col-sm-8" id="program">-</dd>

                <dt class="col-sm-4">Начало</dt>
                <dd class="col-sm-8" id="startTime">-</dd>

                <dt class="col-sm-4">Окончание</dt>
                <dd class="col-sm-8" id="endTime">-</dd>

                <dt class="col-sm-4">Длительность</dt>
                <dd class="col-sm-8" id="duration">-</dd>

                <dt class="col-sm-4">Ручная запись</dt>
                <dd class="col-sm-8" id="isManual">-</dd>
            </dl>
        </div>
    </div>

    @section('scripts')
        <script>
            // 1. Получаем ID из Blade
            const entryId = parseInt('{{ $id ?? 0 }}', 10);

            if (isNaN(entryId) || entryId <= 0) {
                console.error('ID записи некорректен:', '{{ $id }}');
                document.getElementById('message').innerHTML = 'Ошибка: ID записи не определён';
                document.getElementById('message').className = 'alert alert-danger';
                document.getElementById('message').style.display = 'block';
            }

            // 2. Форматирование
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
                setTimeout(() => div.style.display = 'none', 7000);
            }

            // 4. Сброс кнопки удаления
            function resetDeleteButton(btn) {
                if (!btn) return;
                clearTimeout(btn.dataset.timeoutId);
                btn.classList.remove('confirm-delete', 'btn-danger');
                btn.textContent = btn.dataset.originalText || 'Удалить';
                btn.className = btn.dataset.originalClass || 'btn btn-outline-danger';
            }

            // 5. Загрузка записи
            async function loadEntry() {
                console.log('Загружаем запись #', entryId);

                try {
                    const entry = await apiRequest('GET', `/time_entry/${entryId}`);
                    console.log('Запись:', entry);

                    const data = entry.timeEntry || entry; // на случай разной структуры

                    document.getElementById('entryId').textContent = data.id || '-';
                    document.getElementById('user').textContent = data.user || '-';
                    document.getElementById('project').textContent = data.project || '-';
                    document.getElementById('task').textContent = data.task || '-';
                    document.getElementById('program').textContent = data.program || '-';
                    document.getElementById('startTime').textContent = formatDateTime(data.start_time);
                    document.getElementById('endTime').textContent = formatDateTime(data.end_time);
                    document.getElementById('duration').textContent = formatDuration(data.duration_seconds);
                    document.getElementById('isManual').textContent = data.is_manual ? 'Да' : 'Нет';

                    const userRole = (localStorage.getItem('user_role') || 'user').toLowerCase();
                    const canManage = ['manager', 'admin'].includes(userRole);

                    document.getElementById('editBtn').style.display = canManage ? 'inline-block' : 'none';
                    document.getElementById('deleteBtn').style.display = canManage ? 'inline-block' : 'none';

                } catch (err) {
                    console.error('Ошибка загрузки:', err);
                    showMessage('danger', err.message || 'Не удалось загрузить запись');
                }
            }

            // 6. Удаление (двойной клик)
            document.getElementById('deleteBtn')?.addEventListener('click', async function() {
                const btn = this;

                if (!btn.classList.contains('confirm-delete')) {
                    btn.classList.add('confirm-delete');
                    btn.textContent = 'Уверены?';
                    btn.classList.add('btn-danger');

                    btn.dataset.timeoutId = setTimeout(() => resetDeleteButton(btn), 4000);
                    return;
                }

                clearTimeout(btn.dataset.timeoutId);

                try {
                    await apiRequest('DELETE', `/time_entry/${entryId}`);
                    showMessage('success', 'Запись удалена');
                    location.href = '/time-entries';
                } catch (err) {
                    showMessage('danger', err.message || 'Не удалось удалить запись');
                }

                resetDeleteButton(btn);
            });

            document.addEventListener('DOMContentLoaded', () => {
                loadEntry();
            });
        </script>
    @endsection
@endsection
