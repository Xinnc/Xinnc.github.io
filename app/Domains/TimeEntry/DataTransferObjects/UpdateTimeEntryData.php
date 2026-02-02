<?php

namespace App\Domains\TimeEntry\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\AfterOrEqual;
use Spatie\LaravelData\Attributes\Validation\BeforeOrEqual;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class UpdateTimeEntryData extends Data
{
    use ValidationError;

    public function __construct(
        #[Nullable, Exists('projects', 'id')]
        public ?int    $project_id,

        #[Nullable, Exists('tasks', 'id')]
        public ?int    $task_id,

        #[Nullable, Exists('programs', 'id')]
        public ?int    $program_id,

        #[Nullable, Date, AfterOrEqual('1900-01-01'), BeforeOrEqual('2100-01-01')]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i', 'd/m/Y H:i', 'd.m.Y H:i', 'Y-m-d', 'd.m.Y', 'd/m/Y', 'd.m.Y\TH:i', 'Y-m-d\TH:i', 'd/m/Y\TH:i'])]
        public ?Carbon $start_time,

        #[Nullable, Date, AfterOrEqual('start_time'), BeforeOrEqual('2100-01-01')]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i', 'd/m/Y H:i', 'd.m.Y H:i', 'Y-m-d', 'd.m.Y', 'd/m/Y', 'd.m.Y\TH:i', 'Y-m-d\TH:i', 'd/m/Y\TH:i'])]
        public ?Carbon $end_time,
    )
    {
    }

    public static function attributes(): array
    {
        return [
            'project_id' => 'проект',
            'task_id' => 'задача',
            'program_id' => 'программа',
            'start_time' => 'время начала',
            'end_time' => 'время окончания',
        ];
    }

    public function getFilledFields(): array
    {
        $filled = [];

        if ($this->project_id !== null) {
            $filled['project_id'] = $this->project_id;
        }

        if ($this->task_id !== null) {
            $filled['task_id'] = $this->task_id;
        }

        if ($this->program_id !== null) {
            $filled['program_id'] = $this->program_id;
        }

        if ($this->start_time !== null) {
            $filled['start_time'] = $this->start_time;
        }

        if ($this->end_time !== null) {
            $filled['end_time'] = $this->end_time;
        }

        return $filled;
    }

    public static function messages(): array
    {
        return [
            'task_id.exists' => 'Выбранная задача не существует или недоступна.',
            'project_id.exists' => 'Выбранный проект не существует или недоступен.',
            'program_id.exists' => 'Выбранная программа не существует или недоступна.',
        ];
    }
}
