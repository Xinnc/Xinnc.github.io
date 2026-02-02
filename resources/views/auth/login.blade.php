@extends('layouts.app')
@section('title', 'Вход')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h3 class="card-title text-center mb-4">Вход в TimeManager</h3>

                    <!-- Блок для сообщений (ошибки / успех) -->
                    <div id="form-message" class="mb-3" style="display: none;"></div>

                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Войти</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="/register" class="text-muted">Нет аккаунта? Зарегистрироваться</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            document.getElementById('loginForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('form-message');
                messageDiv.style.display = 'none';
                messageDiv.innerHTML = '';

                try {
                    const response = await apiRequest('POST', '/login', data);
                    localStorage.setItem('token', response.token);
                    const profile = await apiRequest('GET', '/profile');
                    localStorage.setItem('user_role', profile.role || 'user');

                    // Успех — можно показать зелёное сообщение или сразу редирект
                    messageDiv.className = 'alert alert-success mb-3';
                    messageDiv.innerHTML = 'Вход выполнен успешно';
                    messageDiv.style.display = 'block';

                    setTimeout(() => location.href = '/', 700);
                } catch (err) {
                    messageDiv.className = 'alert alert-danger mb-3';
                    messageDiv.innerHTML = err.message || 'Ошибка. Попробуйте снова.';
                    messageDiv.style.display = 'block';
                }
            });
        </script>
    @endsection
@endsection
