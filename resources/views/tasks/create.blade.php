@extends('layouts.app')
@section('title', 'Новая задача')

@section('content')
    <h1 class="h3 mb-4">Новая задача в проекте #{{ $projectId }}</h1>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="createTaskForm">
                <div class="mb-3">
                    <label class="form-label">Название задачи *</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Описание</label>
                    <textarea class="form-control" name="description" rows="4"></textarea>
                </div>

                <button type="submit" class="btn btn-success">Создать задачу</button>
                <a href="/projects/{{ $projectId }}" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            document.getElementById('createTaskForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const messageDiv = document.getElementById('message');
                messageDiv.style.display = 'none';
                messageDiv.innerHTML = '';
                messageDiv.className = 'mb-4';

                try {
                    console.log('Отправка данных на создание задачи:', data);

                    const response = await apiRequest('POST', '/project/{{ $projectId }}/task', data);
                    console.log('Задача создана:', response);

                    messageDiv.className = 'alert alert-success mb-4';
                    messageDiv.innerHTML = 'Задача успешно создана!';
                    messageDiv.style.display = 'block';

                    // Редирект после короткой паузы
                    setTimeout(() => {
                        location.href = '/projects/{{ $projectId }}';
                    }, 1000); // 1 секунда, чтобы увидеть сообщение

                } catch (err) {
                    console.error('Ошибка создания задачи:', err);

                    messageDiv.className = 'alert alert-danger mb-4';

                    let html = err.message ? `<p>${err.message}</p>` : '';

                    if (err.errors) {
                        html += '<ul class="mb-0 ps-3">';
                        Object.values(err.errors).flat().forEach(msg => {
                            html += `<li>${msg}</li>`;
                        });
                        html += '</ul>';
                    }

                    messageDiv.innerHTML = html || 'Не удалось создать задачу';
                    messageDiv.style.display = 'block';
                }
            });
        </script>
    @endsection
@endsection
