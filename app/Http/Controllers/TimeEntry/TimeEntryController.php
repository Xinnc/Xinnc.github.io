<?php

namespace App\Http\Controllers\TimeEntry;

use App\Domains\TimeEntry\Actions\StartTimeEntryAction;
use App\Domains\TimeEntry\Actions\StopTimeEntryAction;
use App\Domains\TimeEntry\Actions\StoreTimeEntryAction;
use App\Domains\TimeEntry\Actions\UpdateTimeEntryAction;
use App\Domains\TimeEntry\DataTransferObjects\FilterTimeEntryData;
use App\Domains\TimeEntry\DataTransferObjects\SortTimeEntryData;
use App\Domains\TimeEntry\DataTransferObjects\StartTimeEntryData;
use App\Domains\TimeEntry\DataTransferObjects\StoreTimeEntryData;
use App\Domains\TimeEntry\DataTransferObjects\UpdateTimeEntryData;
use App\Domains\TimeEntry\Model\TimeEntry;
use App\Domains\TimeEntry\Resources\TimeEntryResource;
use App\Http\Controllers\Controller;

class TimeEntryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TimeEntry::class, 'time_entry');
    }

    public function index(FilterTimeEntryData $filter, SortTimeEntryData $sort)
    {
        $entries = TimeEntry::query()
            ->filter($filter)
            ->sortByTime($sort->sort_by_time)
            ->paginate(15);

        return TimeEntryResource::collection($entries);
    }

    public function show(TimeEntry $timeEntry)
    {
        return response()->json([
            'timeEntry' => new TimeEntryResource($timeEntry),
        ]);
    }

    public function store(StoreTimeEntryData $data)
    {
        return response()->json([
            'message' => 'Запись успешно создана!',
            'timeEntry' => new TimeEntryResource(StoreTimeEntryAction::execute($data))
        ], 201);
    }

    public function update(UpdateTimeEntryData $data, TimeEntry $timeEntry)
    {
        return response()->json([
            'message' => 'Запись успешно обновлена!',
            'timeEntry' => new TimeEntryResource(UpdateTimeEntryAction::execute($data, $timeEntry))
        ]);
    }

    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();
        return response()->json([], 204);
    }

    public function start(StartTimeEntryData $data)
    {
        return response()->json([
            'message' => 'Запись времени началась!',
            'timeEntry' => new TimeEntryResource(StartTimeEntryAction::execute($data))
        ], 201);
    }

    public function getStarted()
    {
        return response()->json([
            'timeEntry' => new TimeEntryResource(TimeEntry::where('user_id', auth()->id())->whereNull('end_time')->first())
        ]);
    }

    public function stop()
    {
        return response()->json([
            'message' => 'Запись времени завершена!',
            'timeEntry' => new TimeEntryResource(StopTimeEntryAction::execute())
        ]);
    }
}
