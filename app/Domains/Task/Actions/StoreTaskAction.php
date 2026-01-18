<?php

namespace App\Domains\Task\Actions;


use App\Domains\Project\Model\Project;
use App\Domains\Task\DataTransferObjects\StoreTaskData;
use App\Domains\Task\Model\Task;
use App\Domains\Task\Resources\TaskResource;

class StoreTaskAction
{
    public static function execute(StoreTaskData $data, Task $task, Project $project): Task
    {
        $task = Task::create([
            'project_id' => $project->id,
            'name' => $data->name,
            'description' => $data->description,
        ]);

        return $task;
    }
}
