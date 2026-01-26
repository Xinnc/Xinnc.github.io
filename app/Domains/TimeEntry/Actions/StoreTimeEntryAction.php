<?php

namespace App\Domains\TimeEntry\Actions;


use App\Domains\TimeEntry\DataTransferObjects\StoreTimeEntryData;
use App\Domains\TimeEntry\Model\TimeEntry;

class StoreTimeEntryAction
{
    public static function execute(StoreTimeEntryData $data): TimeEntry
    {
        $timeEntry = TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $data->project_id,
            'task_id' => $data->task_id,
            'program_id' => $data->program_id,
            'start_time' => $data->start_time,
            'end_time' => $data->end_time,
            'duration_minutes' => $data->start_time->diffInSeconds($data->end_time),
            'is_manual' => true,
        ]);

        return $timeEntry;
    }
}
