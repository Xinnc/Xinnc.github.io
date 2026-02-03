@extends('layouts.app')
@section('title', 'Регистрация')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h3 class="card-title text-center mb-4">Регистрация</h3>

                    <!-- Блок для сообщений -->
                    <div id="form-message" class="mb-3" style="display: none;"></div>

                    <form id="registerForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">Имя</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="surname" class="form-label">Фамилия</label>
                                <input type="text" class="form-control" id="surname" name="surname" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Отчество</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" >
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Зарегистрироваться</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="/login" class="text-muted">Уже есть аккаунт? Войти</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            document.getElementById('registerForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('form-message');
                messageDiv.style.display = 'none';
                messageDiv.innerHTML = '';

                try {
                    const response = await apiRequest('POST', '/register', data);
                    localStorage.setItem('token', response.token);

                    messageDiv.className = 'alert alert-success mb-3';
                    messageDiv.innerHTML = 'Регистрация прошла успешно';
                    messageDiv.style.display = 'block';

                    setTimeout(() => location.href = '/', 700);
                } catch (err) {
                    messageDiv.className = 'alert alert-danger mb-3';

                    let html = '';

                    // Основное сообщение (всегда показываем, если есть)
                    if (err.message) {
                        html += `<p><strong>${err.message}</strong></p>`;
                    }

                    // Если есть детальные ошибки по полям — выводим их списком
                    if (err.errors && typeof err.errors === 'object') {
                        html += '<ul class="mb-0 ps-4">';
                        Object.entries(err.errors).forEach(([field, messages]) => {
                            messages.forEach(msg => {
                                html += `<li>${msg}</li>`;
                            });
                        });
                        html += '</ul>';
                    }

                    // Если вообще ничего нет — дефолтный текст
                    if (!html) {
                        html = 'Произошла ошибка. Попробуйте позже.';
                    }

                    messageDiv.innerHTML = html;
                    messageDiv.style.display = 'block';
                }
            });
        </script>
    @endsection
@endsection
