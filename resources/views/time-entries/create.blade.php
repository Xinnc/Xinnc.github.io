@extends('layouts.app')
@section('title', 'Новая запись времени')

@section('content')
    <h1 class="h3 mb-4">Новая запись времени</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="createTimeEntryForm">
                <!-- Проект -->
                <div class="mb-3">
                    <label class="form-label">Проект *</label>
                    <select class="form-select" name="project_id" id="projectSelect" required>
                        <option value="">Выберите проект...</option>
                    </select>
                </div>

                <!-- Задача (подгружается после выбора проекта) -->
                <div class="mb-3">
                    <label class="form-label">Задача (опционально)</label>
                    <select class="form-select" name="task_id" id="taskSelect">
                        <option value="">Выберите задачу...</option>
                    </select>
                </div>

                <!-- Программа -->
                <div class="mb-3">
                    <label class="form-label">Программа (опционально)</label>
                    <select class="form-select" name="program_id" id="programSelect">
                        <option value="">Выберите программу...</option>
                    </select>
                </div>

                <!-- Время начала и конца -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Время начала *</label>
                        <input type="datetime-local" class="form-control" name="start_time" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Время окончания (опционально)</label>
                        <input type="datetime-local" class="form-control" name="end_time">
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Создать запись</button>
                <a href="/time-entries" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            // Загрузка проектов
            async function loadProjects() {
                const select = document.getElementById('projectSelect');
                try {
                    const response = await apiRequest('GET', '/project');
                    const projects = response.data || [];

                    select.innerHTML = '<option value="">Выберите проект...</option>';

                    projects.forEach(p => {
                        const option = document.createElement('option');
                        option.value = p.id;
                        option.textContent = p.name;
                        select.appendChild(option);
                    });
                } catch (err) {
                    showMessage('danger', 'Не удалось загрузить проекты');
                }
            }

            // Загрузка программ
            async function loadPrograms() {
                const select = document.getElementById('programSelect');
                try {
                    const response = await apiRequest('GET', '/program');
                    const programs = response.programs || []; // предполагаем, что приходит data[]

                    select.innerHTML = '<option value="">Выберите программу...</option>';

                    programs.forEach(p => {
                        const option = document.createElement('option');
                        option.value = p.id;
                        option.textContent = p.name || `Программа ${p.id}`;
                        select.appendChild(option);
                    });
                } catch (err) {
                    showMessage('danger', 'Не удалось загрузить программы');
                }
            }

            // Загрузка задач по выбранному проекту
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

            // Обработчик изменения проекта → подгружаем задачи
            document.getElementById('projectSelect').addEventListener('change', function() {
                loadTasks(this.value);
            });

            // Создание записи
            document.getElementById('createTimeEntryForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('message');
                messageDiv.style.display = 'none';
                messageDiv.innerHTML = '';
                messageDiv.className = 'mb-4';

                try {
                    const response = await apiRequest('POST', '/time_entry', data);
                    console.log('Успех:', response);

                    messageDiv.className = 'alert alert-success mb-4';
                    messageDiv.innerHTML = 'Запись успешно создана!';
                    messageDiv.style.display = 'block';

                    setTimeout(() => {
                        location.href = '/time-entries';
                    }, 1200);

                } catch (err) {
                    console.error('Ошибка создания:', err);

                    messageDiv.className = 'alert alert-danger mb-4';

                    let html = '';

                    // 1. Основное сообщение от сервера (всегда есть)
                    if (err.message) {
                        html += `<p><strong>${err.message}</strong></p>`;
                    }

                    // 2. Детальные ошибки по полям (если есть объект errors)
                    if (err.errors && typeof err.errors === 'object') {
                        html += '<ul class="mb-0 ps-4 mt-2">';
                        Object.entries(err.errors).forEach(([field, messages]) => {
                            messages.forEach(msg => {
                                html += `<li>${msg}</li>`;
                            });
                        });
                        html += '</ul>';
                    }

                    // 3. Если ничего конкретного — общий текст
                    if (!html) {
                        html = 'Произошла ошибка при создании записи. Проверьте данные и попробуйте снова.';
                    }

                    messageDiv.innerHTML = html;
                    messageDiv.style.display = 'block';
                }
            });

            // Инициализация
            document.addEventListener('DOMContentLoaded', () => {
                loadProjects();
                loadPrograms();
            });
        </script>
    @endsection
@endsection
