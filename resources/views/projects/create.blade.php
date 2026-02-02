@extends('layouts.app')
@section('title', 'Новый проект')

@section('content')
    <h1 class="mb-4">Создать проект</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="createProjectForm">
                <div class="mb-3">
                    <label class="form-label">Название проекта *</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea class="form-control" name="description" rows="4"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Дедлайн</label>
                    <input type="date" class="form-control" name="deadline">
                </div>

                <button type="submit" class="btn btn-success">Создать проект</button>
                <a href="/projects" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            document.getElementById('createProjectForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('message');

                try {
                    const response = await apiRequest('POST', '/project', data);

                    messageDiv.className = 'alert alert-success';
                    messageDiv.innerHTML = 'Проект успешно создан';
                    messageDiv.style.display = 'block';

                    setTimeout(() => location.href = '/projects', 1200);
                } catch (err) {
                    messageDiv.className = 'alert alert-danger';

                    let html = err.message ? `<p>${err.message}</p>` : '';

                    if (err.errors) {
                        html += '<ul class="mb-0 ps-3">';
                        Object.values(err.errors).flat().forEach(msg => {
                            html += `<li>${msg}</li>`;
                        });
                        html += '</ul>';
                    }

                    messageDiv.innerHTML = html || 'Не удалось создать проект';
                    messageDiv.style.display = 'block';
                }
            });
        </script>
    @endsection
@endsection
