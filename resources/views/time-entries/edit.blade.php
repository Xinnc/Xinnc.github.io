@extends('layouts.app')
@section('title', 'Редактирование записи')

@section('content')
    <h1 class="h3 mb-4">Редактировать запись #{{ $id }}</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="editTimeEntryForm">
                <div class="mb-3">
                    <label class="form-label">Проект</label>
                    <select class="form-select" name="project_id" id="projectSelect" >
                        <option value="">Загрузка...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Задача</label>
                    <select class="form-select" name="task_id" id="taskSelect">
                        <option value="">Загрузка...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Программа</label>
                    <select class="form-select" name="program_id" id="programSelect">
                        <option value="">Загрузка...</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Время начала</label>
                        <input type="datetime-local" class="form-control" name="start_time" id="startTimeInput" >
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Время окончания</label>
                        <input type="datetime-local" class="form-control" name="end_time" id="endTimeInput">
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Сохранить</button>
                <a href="/time-entries/{{ $id }}" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            // 1. ID записи из Blade
            const entryId = parseInt('{{ $id ?? 0 }}', 10);

            if (isNaN(entryId) || entryId <= 0) {
                console.error('ID записи некорректен:', '{{ $id }}');
                document.getElementById('message').innerHTML = 'Ошибка: ID записи не определён';
                document.getElementById('message').className = 'alert alert-danger';
                document.getElementById('message').style.display = 'block';
            }

            // 2. Форматирование даты для input datetime-local
            function formatForInput(iso) {
                if (!iso) return '';
                return iso.slice(0, 16); // 2026-12-12T12:20 → 2026-12-12T12:20
            }

            // 3. Загрузка проектов с предвыбором
            async function loadProjects(selectedId = null) {
                const select = document.getElementById('projectSelect');
                select.innerHTML = '<option value="">Загрузка проектов...</option>';

                try {
                    const response = await apiRequest('GET', '/project');
                    const projects = response.data || [];

                    select.innerHTML = '<option value="">Выберите проект...</option>';

                    projects.forEach(p => {
                        const option = document.createElement('option');
                        option.value = p.id;
                        option.textContent = p.name || `Проект ${p.id}`;
                        if (p.id == selectedId) option.selected = true;
                        select.appendChild(option);
                    });
                } catch (err) {
                    select.innerHTML = '<option value="">Ошибка загрузки проектов</option>';
                    showMessage('danger', 'Не удалось загрузить проекты');
                }
            }

            // 4. Загрузка программ с предвыбором
            async function loadPrograms(selectedId = null) {
                const select = document.getElementById('programSelect');
                select.innerHTML = '<option value="">Загрузка программ...</option>';

                try {
                    const response = await apiRequest('GET', '/program');
                    const programs = response.programs || [];

                    select.innerHTML = '<option value="">Выберите программу...</option>';

                    programs.forEach(p => {
                        const option = document.createElement('option');
                        option.value = p.id;
                        option.textContent = p.name || `Программа ${p.id}`;
                        if (p.id === selectedId) option.selected = true;
                        select.appendChild(option);
                    });
                } catch (err) {
                    select.innerHTML = '<option value="">Ошибка загрузки программ</option>';
                    showMessage('danger', 'Не удалось загрузить программы');
                }
            }

            // 5. Загрузка задач по проекту с предвыбором
            async function loadTasks(projectId) {
                const select = document.getElementById('taskSelect');
                select.innerHTML = '<option value="">Выберите задачу...</option>';

                if (!projectId) return;

                try {
                    const response = await apiRequest('GET', `/project/${projectId}/task`);
                    const tasks = response.data || [];

                    tasks.forEach(t => {
                        const option = document.createElement('option');
                        option.value = t.id;
                        option.textContent = t.name;
                        select.appendChild(option);
                    });
                } catch (err) {
                    showMessage('danger', 'Не удалось загрузить задачи проекта');
                }
            }

            // 6. Загрузка текущей записи и предзаполнение
            async function loadEntryForEdit() {
                try {
                    const entry = await apiRequest('GET', `/time_entry/${entryId}`);
                    console.log('Запись для редактирования:', entry);

                    // Предзаполняем поля
                    document.getElementById('startTimeInput').value = entry.start_time ? formatForInput(entry.start_time) : '';
                    document.getElementById('endTimeInput').value = entry.end_time ? formatForInput(entry.end_time) : '';

                    // Загружаем списки с предвыбором
                    await loadProjects(entry.project_id);
                    await loadPrograms(entry.program_id);

                    // Задачи — после выбора проекта (если project_id есть)
                    if (entry.project_id) {
                        await loadTasks(entry.project_id, entry.task_id);
                    }

                } catch (err) {
                    console.error('Ошибка загрузки записи:', err);
                    showMessage('danger', err.message || 'Не удалось загрузить запись');
                }
            }

            // 7. При изменении проекта — подгружаем задачи
            document.getElementById('projectSelect').addEventListener('change', function() {
                loadTasks(this.value);
            });

            // 8. Сохранение изменений
            document.getElementById('editTimeEntryForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('message');
                messageDiv.style.display = 'none';
                messageDiv.innerHTML = '';
                messageDiv.className = 'mb-4 alert';

                try {
                    await apiRequest('PATCH', `/time_entry/${entryId}`, data);

                    messageDiv.className = 'alert alert-success mb-4';
                    messageDiv.innerHTML = 'Запись успешно обновлена!';
                    messageDiv.style.display = 'block';

                    setTimeout(() => location.href = `/time-entries/${entryId}`, 1200);

                } catch (err) {
                    messageDiv.className = 'alert alert-danger mb-4';

                    let html = '';

                    // Основное сообщение от сервера (почти всегда есть)
                    if (err.message) {
                        html += `<p><strong>${err.message}</strong></p>`;
                    }

                    // Детальные ошибки по полям (стандарт Laravel 422)
                    if (err.errors && typeof err.errors === 'object') {
                        html += '<ul class="mb-0 ps-4 mt-2">';
                        Object.entries(err.errors).forEach(([field, messages]) => {
                            // messages — массив строк
                            messages.forEach(msg => {
                                html += `<li><strong>${field}:</strong> ${msg}</li>`;
                            });
                        });
                        html += '</ul>';
                    }

                    // Если ничего конкретного не нашли
                    if (!html) {
                        html = '<p>Не удалось сохранить изменения. Проверьте данные и попробуйте снова.</p>';
                    }

                    messageDiv.innerHTML = html;
                    messageDiv.style.display = 'block';
                }
            });

            // 9. Запуск при загрузке страницы
            document.addEventListener('DOMContentLoaded', () => {
                console.log('Страница редактирования загружена, запускаем загрузку');
                loadEntryForEdit();
            });
        </script>
    @endsection
@endsection
