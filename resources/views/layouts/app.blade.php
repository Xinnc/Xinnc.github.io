<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TimeManager')</title>

    <!-- Bootstrap 5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-..." crossorigin="anonymous">
</head>
<body class="bg-light">

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-xxl">
            <a class="navbar-brand" href="/">TimeManager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto" id="main-menu"></ul>

                <ul class="navbar-nav ms-auto" id="auth-menu"></ul>
            </div>
        </div>
    </nav>
</header>

<main class="container mt-4">
    @yield('content')
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-..."
        crossorigin="anonymous"></script>

<script>
    const API_BASE = '/api';

    function getToken() {
        return localStorage.getItem('token');
    }

    async function apiRequest(method, endpoint, body = null) {
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };

        const token = getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const config = { method, headers };
        if (body) config.body = JSON.stringify(body);

        const response = await fetch(`${API_BASE}${endpoint}`, config);

        let json;
        try {
            json = await response.json();
        } catch {
            json = {};
        }

        if (response.ok) {
            // Для login/register ждём token
            if ((endpoint === '/login' || endpoint === '/register') && json.token) {
                return json;
            }
            // Для всех остальных — просто возвращаем данные
            return json;
        }

        const error = new Error(json.message || `Ошибка сервера ${response.status}`);
        error.errors = json.errors || null;
        error.status = response.status;
        throw error;
    }

    async function handleLogout() {
        try {
            await apiRequest('GET', '/logout');
        } catch (e) {
            console.warn('Logout failed:', e);
        }
        localStorage.removeItem('token');
        localStorage.removeItem('user_role'); // чистим роль
        location.href = '/login';
    }

    // ────────────────────────────────────────────────
    // Рендер navbar с учётом роли
    // ────────────────────────────────────────────────
    function renderNavbar() {
        const token = getToken();
        const isLoggedIn = !!token;
        const userRole = (localStorage.getItem('user_role') || 'user').toLowerCase();
        const isManagerOrAdmin = ['manager', 'admin'].includes(userRole);

        const mainMenu = document.getElementById('main-menu');
        const authMenu = document.getElementById('auth-menu');

        mainMenu.innerHTML = '';
        authMenu.innerHTML = '';

        // Всегда показываем "Главная"
        mainMenu.innerHTML += `
            <li class="nav-item">
                <a class="nav-link ${location.pathname === '/' ? 'active' : ''}" href="/">Главная</a>
            </li>
        `;

        if (isLoggedIn) {
            mainMenu.innerHTML += `
                <li class="nav-item">
                    <a class="nav-link ${location.pathname.startsWith('/projects') ? 'active' : ''}" href="/projects">Проекты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ${location.pathname === '/tasks' ? 'active' : ''}" href="/tasks">Все задачи</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ${location.pathname.startsWith('/time-entries') ? 'active' : ''}" href="/time-entries">Записи времени</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ${location.pathname.startsWith('/programs') ? 'active' : ''}" href="/programs">Программы</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ${location.pathname.startsWith('/profile') ? 'active' : ''}" href="/profile">Профиль</a>
                </li>
            `;
            if (userRole === 'admin') {
                mainMenu.innerHTML += `
                    <li class="nav-item">
                        <a class="nav-link ${location.pathname.startsWith('/admin') ? 'active' : ''}" href="/admin">Админ</a>
                    </li>
                `;
            }


            authMenu.innerHTML = `
                <li class="nav-item">
                    <button class="btn btn-outline-light" onclick="handleLogout()">Выйти</button>
                </li>
            `;
        } else {
            authMenu.innerHTML = `
                <li class="nav-item">
                    <a class="nav-link ${location.pathname === '/login' ? 'active' : ''}" href="/login">Вход</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ${location.pathname === '/register' ? 'active' : ''}" href="/register">Регистрация</a>
                </li>
            `;
        }

    }

    // Защита страниц
    if (!getToken() && !['/login', '/register'].includes(location.pathname)) {
        location.href = '/login';
    }

    // ────────────────────────────────────────────────
    // Загрузка роли после логина (если ещё не загружена)
    // ────────────────────────────────────────────────
    async function loadUserRoleIfNeeded() {
        if (getToken() && !localStorage.getItem('user_role')) {
            try {
                const profile = await apiRequest('GET', '/profile');
                const role = profile.role_name || profile.role || 'user';
                localStorage.setItem('user_role', role);
                renderNavbar(); // перерисовываем navbar после получения роли
            } catch (e) {
                console.warn('Не удалось загрузить роль пользователя:', e);
            }
        }
    }

    function formatDateTime(isoString) {
        if (!isoString) return '-';

        try {
            const date = new Date(isoString);
            if (isNaN(date.getTime())) return '-';

            return new Intl.DateTimeFormat('ru-RU', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).format(date);
            // Вывод: 12.12.2026 12:20
        } catch {
            return '-';
        }
    }

    function formatDuration(start, end) {
        if (!start || !end) return '-';
        const diffMs = new Date(end) - new Date(start);
        if (diffMs < 0) return '-';

        const hours = Math.floor(diffMs / 3600000);
        const minutes = Math.floor((diffMs % 3600000) / 60000);
        const seconds = Math.floor((diffMs % 60000) / 1000);

        return `${hours > 0 ? hours + 'ч ' : ''}${minutes}мин ${seconds}сек`;
    }

    // Инициализация
    document.addEventListener('DOMContentLoaded', () => {
        renderNavbar();
        loadUserRoleIfNeeded();
    });

    window.addEventListener('popstate', renderNavbar);
</script>
@yield('scripts')
</body>
</html>
