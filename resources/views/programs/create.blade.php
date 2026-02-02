@extends('layouts.app')
@section('title', 'Новая программа')

@section('content')
    <h1 class="h3 mb-4">Новая программа</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="createProgramForm">
                <div class="mb-3">
                    <label class="form-label">Название программы *</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <button type="submit" class="btn btn-success">Создать</button>
                <a href="/programs" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            document.getElementById('createProgramForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('message');
                messageDiv.style.display = 'none';
                messageDiv.innerHTML = '';
                messageDiv.className = 'mb-4';

                try {
                    await apiRequest('POST', '/program', data);
                    messageDiv.className = 'alert alert-success mb-4';
                    messageDiv.innerHTML = 'Программа создана!';
                    messageDiv.style.display = 'block';

                    setTimeout(() => location.href = '/programs', 1200);
                } catch (err) {
                    messageDiv.className = 'alert alert-danger mb-4';

                    let html = err.message ? `<p>${err.message}</p>` : '';

                    if (err.errors) {
                        html += '<ul class="mb-0 ps-3">';
                        Object.values(err.errors).flat().forEach(msg => {
                            html += `<li>${msg}</li>`;
                        });
                        html += '</ul>';
                    }

                    messageDiv.innerHTML = html || 'Не удалось создать программу';
                    messageDiv.style.display = 'block';
                }
            });
        </script>
    @endsection
@endsection
