<?php

namespace App\Domains\TimeEntry\Actions;

use App\Domains\Project\Model\Project;
use App\Domains\Shared\Model\Program;
use App\Domains\TimeEntry\DataTransferObjects\StoreTimeEntryData;
use App\Domains\TimeEntry\Model\TimeEntry;

class StoreTimeEntryAction
{
    public static function execute(StoreTimeEntryData $data): TimeEntry
    {
        if($data->program_id)Program::where('id', $data->program_id)->first()->ensureIsActive();
        Project::where('id', $data->project_id)->first()->ensureIsActive();
        $timeEntry = TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $data->project_id,
            'task_id' => $data->task_id,
            'program_id' => $data->program_id,
            'start_time' => $data->start_time,
            'end_time' => $data->end_time,
            'duration_seconds' => $data->start_time->diffInSeconds($data->end_time),
            'is_manual' => true,
        ]);

        return $timeEntry;
    }
}
