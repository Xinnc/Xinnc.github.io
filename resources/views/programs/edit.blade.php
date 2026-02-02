@extends('layouts.app')
@section('title', 'Редактирование программы')

@section('content')
    <h1 class="h3 mb-4">Редактировать программу #{{ $id }}</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="editProgramForm">
                <div class="mb-3">
                    <label class="form-label">Название программы *</label>
                    <input type="text" class="form-control" name="name" id="nameInput" required autofocus>
                </div>

                <button type="submit" class="btn btn-success">Сохранить</button>
                <a href="/programs/{{ $id }}" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            const programId = {{ $id }};

            // 2. Обработка ошибок (одинаково для всех страниц)
            function showMessage(type, text) {
                const div = document.getElementById('message');
                if (!div) return;
                div.className = `alert alert-${type} mb-4`;
                div.innerHTML = text;
                div.style.display = 'block';
                setTimeout(() => div.style.display = 'none', 7000);
            }

            // 3. Сохранение
            document.getElementById('editProgramForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('message');
                messageDiv.style.display = 'none';
                messageDiv.innerHTML = '';
                messageDiv.className = 'mb-4';

                try {
                    await apiRequest('PATCH', `/program/${programId}`, data);

                    messageDiv.className = 'alert alert-success mb-4';
                    messageDiv.innerHTML = 'Программа обновлена!';
                    messageDiv.style.display = 'block';

                    setTimeout(() => location.href = `/programs`, 1200);
                } catch (err) {
                    messageDiv.className = 'alert alert-danger mb-4';

                    let html = '';

                    // Основное сообщение
                    if (err.message) {
                        html += `<p><strong>${err.message}</strong></p>`;
                    }

                    // Детальные ошибки по полям
                    if (err.errors && typeof err.errors === 'object') {
                        html += '<ul class="mb-0 ps-4 mt-2">';
                        Object.entries(err.errors).forEach(([field, messages]) => {
                            messages.forEach(msg => {
                                html += `<li><strong>${field}:</strong> ${msg}</li>`;
                            });
                        });
                        html += '</ul>';
                    }

                    if (!html) {
                        html = 'Не удалось сохранить изменения. Проверьте данные.';
                    }

                    messageDiv.innerHTML = html;
                    messageDiv.style.display = 'block';
                }
            });

            // Запуск
            document.addEventListener('DOMContentLoaded', () => {
                loadProgram();
            });
        </script>
    @endsection
@endsection
