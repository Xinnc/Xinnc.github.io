<?php

namespace App\Domains\TimeEntry\Actions;

use App\Domains\Project\Model\Project;
use App\Domains\Shared\Model\Program;
use App\Domains\TimeEntry\DataTransferObjects\UpdateTimeEntryData;
use App\Domains\TimeEntry\Model\TimeEntry;

class UpdateTimeEntryAction
{
    public static function execute(UpdateTimeEntryData $data, TimeEntry $timeEntry): TimeEntry
    {
        if($data->program_id)Program::where('id', $data->program_id)->first()->ensureIsActive();
        if($data->project_id)Project::where('id', $data->project_id)->first()->ensureIsActive();
        Project::where('id', $timeEntry->project_id)->first()->ensureIsActive();
        $updates = $data->getFilledFields();

        if (!empty($updates)) {
            $timeEntry->update($updates);
        }

        return $timeEntry;
    }
}
