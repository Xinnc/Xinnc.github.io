<?php

namespace App\Domains\TimeEntry\Actions;

use App\Domains\Project\Model\Project;
use App\Domains\Shared\Exceptions\ConflictHttpException;
use App\Domains\Shared\Model\Program;
use App\Domains\TimeEntry\DataTransferObjects\StartTimeEntryData;
use App\Domains\TimeEntry\Model\TimeEntry;
use Carbon\Carbon;

class StartTimeEntryAction
{
    public static function execute(StartTimeEntryData $data): TimeEntry
    {
        if($data->program_id)Program::where('id', $data->program_id)->first()->ensureIsActive();
        Project::where('id', $data->project_id)->first()->ensureIsActive();
        if (TimeEntry::where('user_id', auth()->id())->whereNull('end_time')->exists()) {
            throw new ConflictHttpException(409, 'У вас уже есть активная запись времени.');
        }

        $timeEntry = TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $data->project_id,
            'task_id' => $data->task_id,
            'program_id' => $data->program_id,
            'start_time' => Carbon::now(),
            'is_manual' => false,
        ]);

        return $timeEntry;
    }
}
