@extends('layouts.app')
@section('title', 'Редактировать задачу')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Редактировать задачу #{{ $taskId }}</h1>
        <a href="/projects/{{ $projectId }}/task/{{ $taskId }}" class="btn btn-outline-secondary">Назад</a>
    </div>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="editTaskForm">
                <div class="mb-3">
                    <label class="form-label">Название задачи *</label>
                    <input type="text" class="form-control" name="name" id="nameInput" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Описание</label>
                    <textarea class="form-control" name="description" id="descInput" rows="4"></textarea>
                </div>

                <button type="submit" class="btn btn-success">Сохранить изменения</button>
                <a href="/projects/{{ $projectId }}/task/{{ $taskId }}" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            const projectId = {{ $projectId }};
            const taskId = {{ $taskId }};

            async function loadTaskForEdit() {
                try {
                    const task = await apiRequest('GET', `/project/${projectId}/task/${taskId}`);

                    document.getElementById('nameInput').value = task.name || '';
                    document.getElementById('descInput').value = task.description || '';

                } catch (err) {
                    showMessage('danger', err.message || 'Не удалось загрузить задачу');
                }
            }

            document.getElementById('editTaskForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                try {
                    await apiRequest('PATCH', `/project/${projectId}/task/${taskId}`, data);
                    showMessage('success', 'Задача обновлена');
                    setTimeout(() => location.href = `/projects/${projectId}/task/${taskId}`, 1000);
                } catch (err) {
                    let html = err.message ? `<p>${err.message}</p>` : '';

                    if (err.errors) {
                        html += '<ul class="mb-0 ps-3">';
                        Object.values(err.errors).flat().forEach(msg => {
                            html += `<li>${msg}</li>`;
                        });
                        html += '</ul>';
                    }

                    showMessage('danger', html || 'Не удалось сохранить изменения');
                }
            });

            document.addEventListener('DOMContentLoaded', loadTaskForEdit);
        </script>
    @endsection
@endsection
