<?php

namespace App\Domains\TimeEntry\Actions;


use App\Domains\Shared\Exceptions\ConflictHttpException;
use App\Domains\TimeEntry\Model\TimeEntry;
use Carbon\Carbon;

class StopTimeEntryAction
{
    public static function execute(): TimeEntry
    {
        $timeEntry = TimeEntry::where('user_id', auth()->id())->whereNull('end_time')->first();
        if(!$timeEntry){
            throw new ConflictHttpException(409, 'Нет активной записи времени.');
        }
        $timeEntry->update([
            'end_time' => Carbon::now(),
            'duration_minutes' => (int) $timeEntry->start_time->diffInSeconds(Carbon::now()),
        ]);

        return $timeEntry;
    }
}
