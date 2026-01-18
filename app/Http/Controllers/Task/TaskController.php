<?php

namespace App\Http\Controllers\Task;

use App\Domains\Project\Model\Project;
use App\Domains\Task\Actions\StoreTaskAction;
use App\Domains\Task\Actions\UpdateTaskAction;
use App\Domains\Task\DataTransferObjects\StoreTaskData;
use App\Domains\Task\DataTransferObjects\UpdateTaskData;
use App\Domains\Task\Model\Task;
use App\Domains\Task\Resources\TaskResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json(TaskResource::collection(Task::all()));
    }
    public function show(Task $task)
    {
        return response()->json([
            'task' => new TaskResource($task),
        ]);
    }
    public function store(StoreTaskData $data, Task $task, Project $project)
    {
        return response()->json([
            'message' => 'Задача успешно создана!',
            'task' => new TaskResource(StoreTaskAction::execute($data, $task, $project)),
        ]);
    }
    public function update(UpdateTaskData $data, Task $task)
    {
        return response()->json([
            'message' => 'Задача успешно обнолвлена!',
            'task' => new TaskResource(UpdateTaskAction::execute($data, $task))
        ]);
    }
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json([
            'message' => 'Задачу успешно удалена!'
        ]);
    }
}
