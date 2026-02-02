@extends('layouts.app')
@section('title', 'Смена пароля')

@section('content')
    <h1 class="h3 mb-4">Смена пароля</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="changePasswordForm">
                <div class="mb-3">
                    <label class="form-label">Текущий пароль *</label>
                    <input type="password" class="form-control" name="old_password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Новый пароль *</label>
                    <input type="password" class="form-control" name="password" required minlength="8">
                </div>

                <div class="mb-4">
                    <label class="form-label">Повторите новый пароль *</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-success">Сменить пароль</button>
                <a href="/profile" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('message');
                messageDiv.style.display = 'none';
                messageDiv.innerHTML = '';
                messageDiv.className = 'mb-4';

                try {
                    await apiRequest('PATCH', '/profile/password', data);

                    messageDiv.className = 'alert alert-success mb-4';
                    messageDiv.innerHTML = 'Пароль успешно изменён!';
                    messageDiv.style.display = 'block';

                    setTimeout(() => location.href = '/profile', 2000);
                } catch (err) {
                    messageDiv.className = 'alert alert-danger mb-4';

                    let html = err.message ? `<p><strong>${err.message}</strong></p>` : '';

                    if (err.errors && typeof err.errors === 'object') {
                        html += '<ul class="mb-0 ps-4 mt-2">';
                        Object.entries(err.errors).forEach(([field, messages]) => {
                            messages.forEach(msg => {
                                html += `<li><strong>${field}:</strong> ${msg}</li>`;
                            });
                        });
                        html += '</ul>';
                    }

                    if (!html) html = 'Не удалось сменить пароль. Проверьте текущий пароль и повторите попытку.';

                    messageDiv.innerHTML = html;
                    messageDiv.style.display = 'block';
                }
            });
        </script>
    @endsection
@endsection
