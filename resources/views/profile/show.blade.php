@extends('layouts.app')
@section('title', 'Профиль')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Профиль</h1>
        <div>
            <a href="/profile/edit" class="btn btn-warning me-2">Редактировать профиль</a>
            <a href="/profile/password" class="btn btn-outline-primary">Сменить пароль</a>
        </div>
    </div>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="row">
        <!-- Основная информация -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Информация о пользователе</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8" id="userId">-</dd>

                        <dt class="col-sm-4">Имя</dt>
                        <dd class="col-sm-8" id="firstName">-</dd>

                        <dt class="col-sm-4">Фамилия</dt>
                        <dd class="col-sm-8" id="surname">-</dd>

                        <dt class="col-sm-4">Отчество</dt>
                        <dd class="col-sm-8" id="lastName">-</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8" id="email">-</dd>

                        <dt class="col-sm-4">Роль</dt>
                        <dd class="col-sm-8" id="role">-</dd>

                        <dt class="col-sm-4">Дата регистрации</dt>
                        <dd class="col-sm-8" id="createdAt">-</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Управление таймером -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Управление таймером</h5>
                </div>
                <div class="card-body">
                    <div id="timerInfo" class="mb-3">
                        <p id="timerStatus">Загрузка...</p>
                        <p id="timerDetails"></p>
                    </div>

                    <!-- Форма выбора перед запуском -->
                    <div id="startTimerSection" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Проект *</label>
                            <select class="form-select" id="projectSelect" required>
                                <option value="">Выберите проект...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Задача (опционально)</label>
                            <select class="form-select" id="taskSelect">
                                <option value="">Выберите задачу...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Программа (опционально)</label>
                            <select class="form-select" id="programSelect">
                                <option value="">Выберите программу...</option>
                            </select>
                        </div>

                        <button class="btn btn-success btn-lg" id="confirmStartBtn">
                            <i class="bi bi-play-fill"></i> Запустить таймер
                        </button>
                    </div>

                    <!-- Кнопка остановки -->
                    <button class="btn btn-danger btn-lg" id="stopTimerBtn" style="display:none;">
                        <i class="bi bi-stop-fill"></i> Остановить таймер
                    </button>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            // Форматирование
            function formatDateTime(iso) {
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

            function formatDuration(seconds) {
                if (!seconds || seconds <= 0) return '-';
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = seconds % 60;
                return `${h}ч ${m}мин ${s}сек`;
            }

            function showMessage(type, text) {
                const div = document.getElementById('message');
                if (!div) return;
                div.className = `alert alert-${type} mb-4`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 7000);
            }

            // Загрузка профиля
            async function loadProfile() {
                console.log('Загружаем профиль...');

                try {
                    // 1. Профиль пользователя
                    const profileData = await apiRequest('GET', '/profile');
                    console.log('Профиль пользователя:', profileData);

                    const user = profileData.user || profileData;
                    document.getElementById('userId').textContent = user.id || '-';
                    document.getElementById('firstName').textContent = user.first_name || '-';
                    document.getElementById('surname').textContent = user.surname || '-';
                    document.getElementById('lastName').textContent = user.last_name || '-';
                    document.getElementById('email').textContent = user.email || '-';
                    document.getElementById('role').textContent = user.role_name || user.role || 'Пользователь';
                    document.getElementById('createdAt').textContent = formatDateTime(user.created_at);

                    // 2. Проверка активного таймера через отдельный маршрут
                    let activeEntry = null;
                    try {
                        const timerResponse = await apiRequest('GET', '/time_entry/get_started');
                        console.log('Ответ таймера:', timerResponse);

                        // Твоя структура: запись приходит в корне или в поле timeEntry
                        activeEntry = timerResponse.timeEntry || timerResponse || null;

                    } catch (timerErr) {
                        console.warn('Нет активной записи или ошибка таймера:', timerErr);
                    }

                    const timerInfo = document.getElementById('timerInfo');
                    const startSection = document.getElementById('startTimerSection');
                    const stopBtn = document.getElementById('stopTimerBtn');

                    if (activeEntry && activeEntry.id && !activeEntry.end_time) {
                        // Есть активная запись (end_time = null)
                        timerInfo.innerHTML = `
                <strong>Активная запись:</strong><br>
                Проект: ${activeEntry.project || '-'}<br>
                Задача: ${activeEntry.task || '-'}<br>
                Программа: ${activeEntry.program || '-'}<br>
                Начало: ${formatDateTime(activeEntry.start_time)}<br>
                Длительность: ${formatDuration(activeEntry.duration_seconds || 0)}
            `;
                        startSection.style.display = 'none';
                        stopBtn.style.display = 'inline-block';
                    } else {
                        timerInfo.innerHTML = '<p class="text-muted">Таймер не запущен</p>';
                        startSection.style.display = 'block';
                        stopBtn.style.display = 'none';

                        // Подгружаем списки для запуска
                        console.log('Таймер не активен → загружаем списки');
                        await loadProjectsForTimer();
                        await loadProgramsForTimer();
                    }

                } catch (err) {
                    console.error('Общая ошибка загрузки профиля:', err);
                    showMessage('danger', err.message || 'Не удалось загрузить профиль');
                }
            }
            // Загрузка проектов
            async function loadProjectsForTimer() {
                const select = document.getElementById('projectSelect');
                if (!select) return;

                select.innerHTML = '<option value="" disabled selected>Загрузка проектов...</option>';

                try {
                    const response = await apiRequest('GET', '/project');
                    const projects = response.data || response.programs || [];

                    select.innerHTML = '<option value="">Выберите проект...</option>';

                    projects.forEach(p => {
                        const option = document.createElement('option');
                        option.value = p.id;
                        option.textContent = p.name || `Проект ${p.id}`;
                        select.appendChild(option);
                    });
                } catch (err) {
                    select.innerHTML = '<option value="">Ошибка загрузки проектов</option>';
                    showMessage('danger', 'Не удалось загрузить проекты');
                }
            }

            // Загрузка программ
            async function loadProgramsForTimer() {
                const select = document.getElementById('programSelect');
                if (!select) return;

                select.innerHTML = '<option value="" disabled selected>Загрузка программ...</option>';

                try {
                    const response = await apiRequest('GET', '/program');
                    const programs = response.programs || response.data || [];

                    select.innerHTML = '<option value="">Выберите программу...</option>';

                    programs.forEach(p => {
                        const option = document.createElement('option');
                        option.value = p.id;
                        option.textContent = p.name || `Программа ${p.id}`;
                        select.appendChild(option);
                    });
                } catch (err) {
                    select.innerHTML = '<option value="">Ошибка загрузки программ</option>';
                    showMessage('danger', 'Не удалось загрузить программы');
                }
            }

            // Загрузка задач при смене проекта
            document.getElementById('projectSelect')?.addEventListener('change', function() {
                loadTasksForTimer(this.value);
            });

            async function loadTasksForTimer(projectId) {
                const select = document.getElementById('taskSelect');
                if (!select) return;

                select.innerHTML = '<option value="" disabled selected>Загрузка задач...</option>';
                select.disabled = true;

                if (!projectId) {
                    select.innerHTML = '<option value="">Сначала выберите проект</option>';
                    select.disabled = false;
                    return;
                }

                try {
                    const response = await apiRequest('GET', `/project/${projectId}/task`);
                    const tasks = response.data || [];

                    select.innerHTML = '<option value="">Выберите задачу... (опционально)</option>';

                    if (tasks.length === 0) {
                        select.innerHTML += '<option value="" disabled>В проекте нет задач</option>';
                    } else {
                        tasks.forEach(t => {
                            const option = document.createElement('option');
                            option.value = t.id;
                            option.textContent = t.name || `Задача #${t.id}`;
                            select.appendChild(option);
                        });
                    }
                } catch (err) {
                    console.error('Ошибка задач:', err);
                    select.innerHTML = '<option value="">Ошибка загрузки задач</option>';
                    showMessage('danger', 'Не удалось загрузить задачи');
                } finally {
                    select.disabled = false;
                }
            }

            // Запуск таймера
            document.getElementById('confirmStartBtn')?.addEventListener('click', async function() {
                const projectSelect = document.getElementById('projectSelect');
                const taskSelect = document.getElementById('taskSelect');
                const programSelect = document.getElementById('programSelect');

                const projectId = projectSelect.value.trim();
                if (!projectId) {
                    showMessage('danger', 'Выберите проект!');
                    return;
                }

                const data = {
                    project_id: projectId,
                    task_id: taskSelect.value.trim() || "",
                    program_id: programSelect.value.trim() || ""
                };

                console.log('Отправка на запуск таймера:', data);

                try {
                    await apiRequest('POST', '/time_entry/start', data);
                    showMessage('success', 'Таймер запущен!');
                    loadProfile();
                } catch (err) {
                    showMessage('danger', err.message || 'Не удалось запустить таймер');
                }
            });

            // Остановка таймера
            document.getElementById('stopTimerBtn')?.addEventListener('click', async function() {
                try {
                    await apiRequest('PATCH', '/time_entry/stop');
                    showMessage('success', 'Таймер остановлен');
                    loadProfile();
                } catch (err) {
                    showMessage('danger', err.message || 'Не удалось остановить таймер');
                }
            });

            document.addEventListener('DOMContentLoaded', () => {
                loadProfile();
            });
        </script>
    @endsection
@endsection
