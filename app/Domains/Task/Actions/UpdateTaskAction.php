<?php

namespace App\Domains\Task\Actions;


use App\Domains\Task\DataTransferObjects\UpdateTaskData;
use App\Domains\Task\Model\Task;

class UpdateTaskAction
{
    public static function execute(UpdateTaskData $data, Task $task): Task
    {
        $updates = $data->getFilledFields();

        if(!empty($updates)){
            $task->update($updates);
        }

        return $task;
    }
}
