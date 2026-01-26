<?php

namespace App\Domains\TimeEntry\Actions;


use App\Domains\TimeEntry\DataTransferObjects\UpdateTimeEntryData;
use App\Domains\TimeEntry\Model\TimeEntry;

class UpdateTimeEntryAction
{
    public static function execute(UpdateTimeEntryData $data, TimeEntry $timeEntry): TimeEntry
    {
        $updates = $data->getFilledFields();

        if(!empty($updates)){
            $timeEntry->update($updates);
        }

        return $timeEntry;
    }
}
