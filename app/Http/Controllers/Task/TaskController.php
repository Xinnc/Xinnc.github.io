<?php

namespace App\Http\Controllers\Task;

use App\Domains\Project\Model\Project;
use App\Domains\Task\Actions\StatusUpdateTaskAction;
use App\Domains\Task\Actions\StoreTaskAction;
use App\Domains\Task\Actions\UpdateTaskAction;
use App\Domains\Task\DataTransferObjects\FilterTaskData;
use App\Domains\Task\DataTransferObjects\StatusUpdateTaskData;
use App\Domains\Task\DataTransferObjects\StoreTaskData;
use App\Domains\Task\DataTransferObjects\UpdateTaskData;
use App\Domains\Task\Model\Task;
use App\Domains\Task\Resources\TaskResource;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
        $this->middleware('can:updateStatus,task')->only(['updateStatus']);
    }

    public function allTasks(FilterTaskData $filter)
    {
        return TaskResource::collection(Task::filter($filter)->paginate(15));
    }

    public function index(Project $project, FilterTaskData $filter)
    {
        $tasks = Task::where('project_id', $project->id)
            ->filter($filter)
            ->paginate(15);

        return TaskResource::collection($tasks);
    }

    public function show(Project $project, Task $task)
    {
        return response()->json([
            'task' => new TaskResource($task),
        ]);
    }

    public function store(StoreTaskData $data, Project $project)
    {
        return response()->json([
            'message' => 'Задача успешно создана!',
            'task' => new TaskResource(StoreTaskAction::execute($data, $project)),
        ], 201);
    }

    public function update(UpdateTaskData $data, Project $project, Task $task)
    {
        $project->ensureIsActive();
        return response()->json([
            'message' => 'Задача успешно обновлена!',
            'task' => new TaskResource(UpdateTaskAction::execute($data, $task))
        ]);
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();
        return response()->json([], 204);
    }

    public function updateStatus(StatusUpdateTaskData $data, Project $project, Task $task)
    {
        return response()->json([
            'message' => 'Статус задачи успешно обновлен!',
            'task' => new TaskResource(StatusUpdateTaskAction::execute($data, $task))
        ]);
    }
}
