@extends('layouts.app')
@section('title', 'Редактирование проекта')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Редактировать проект</h1>
        <a href="/projects/{{ $id }}" class="btn btn-outline-secondary">Назад</a>
    </div>

    <div id="message" class="mb-4" style="display:none;"></div>

    <div class="card">
        <div class="card-body">
            <form id="editProjectForm">
                <div class="mb-3">
                    <label class="form-label">Название проекта *</label>
                    <input type="text" class="form-control" name="name" id="nameInput" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea class="form-control" name="description" id="descInput" rows="4"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Дедлайн</label>
                    <input type="date" class="form-control" name="deadline" id="deadlineInput">
                </div>

                <button type="submit" class="btn btn-success">Сохранить изменения</button>
                <a href="/projects/{{ $id }}" class="btn btn-outline-secondary ms-2">Отмена</a>
            </form>
        </div>
    </div>

    @section('scripts')
        <script>
            const projectId = {{ $id }};

            async function loadProjectForEdit() {
                try {
                    const response = await apiRequest('GET', `/project/${projectId}`);

                    document.getElementById('nameInput').value = response.name || '';
                    document.getElementById('descInput').value = response.description || '';
                    document.getElementById('deadlineInput').value = response.deadline || '';

                } catch (err) {
                    showMessage('danger', err.message || 'Не удалось загрузить данные проекта');
                }
            }

            document.getElementById('editProjectForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                try {
                    await apiRequest('PATCH', `/project/${projectId}`, data);
                    showMessage('success', 'Проект успешно обновлён');
                    setTimeout(() => location.href = `/projects/${projectId}`, 1200);
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

            document.addEventListener('DOMContentLoaded', loadProjectForEdit);
        </script>
    @endsection
@endsection
