<?php

namespace App\Domains\Task\Actions;


use App\Domains\Task\DataTransferObjects\StatusUpdateTaskData;
use App\Domains\Task\Model\Task;

class StatusUpdateTaskAction
{
    public static function execute(StatusUpdateTaskData $data, Task $task): Task
    {
        $task->status = $data->status;
        $task->save();
        return $task;
    }
}
