@extends('layouts.app')
@section('title', 'Редактирование профиля')

@section('content')
    <h1 class="h3 mb-4">Редактировать профиль</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="editProfileForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Имя</label>
                        <input type="text" class="form-control" name="first_name" id="firstNameInput">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Фамилия</label>
                        <input type="text" class="form-control" name="surname" id="surnameInput">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Отчество</label>
                    <input type="text" class="form-control" name="last_name" id="lastNameInput">
                </div>

                <div class="mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="emailInput">
                </div>

                <button type="submit" class="btn btn-success">Сохранить</button>
                <a href="/profile" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            document.getElementById('editProfileForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('message');
                messageDiv.style.display = 'none';
                messageDiv.innerHTML = '';
                messageDiv.className = 'mb-4';

                try {
                    await apiRequest('PATCH', '/profile', data);

                    messageDiv.className = 'alert alert-success mb-4';
                    messageDiv.innerHTML = 'Профиль обновлён!';
                    messageDiv.style.display = 'block';

                    setTimeout(() => location.href = '/profile', 1200);
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

                    if (!html) html = 'Не удалось сохранить изменения';

                    messageDiv.innerHTML = html;
                    messageDiv.style.display = 'block';
                }
            });

            document.addEventListener('DOMContentLoaded', () => {
            });
        </script>
    @endsection
@endsection
