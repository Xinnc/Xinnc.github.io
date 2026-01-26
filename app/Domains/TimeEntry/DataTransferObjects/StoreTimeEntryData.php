<?php

namespace App\Domains\TimeEntry\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class StoreTimeEntryData extends Data
{
    use ValidationError;

    public function __construct(
        #[Required, IntegerType, Exists('projects', 'id')]
        public int $project_id,

        #[Nullable, Exists('tasks', 'id')]
        public ?int $task_id,

        #[Nullable, Exists('programs', 'id')]
        public ?int $program_id,

        #[Required, Date]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i', 'd.m.Y  H:i', 'd/m/Y H:i'])]
        public Carbon $start_time,

        #[Required, Date]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i', 'd.m.Y H:i', 'd/m/Y H:i'])]
        public Carbon $end_time,
    ) {}

    public static function attributes(): array
    {
        return [
            'project_id'       => 'проект',
            'task_id'          => 'задача',
            'program_id'       => 'программа',
            'start_time'       => 'время начала',
            'end_time'         => 'время окончания',
            'duration_minutes' => 'продолжительность',
            'is_manual'        => 'тип ввода времени',
        ];
    }
    public static function messages(): array
    {
        return [
            'start_time' => 'Формат времени должен быть одним из "Г-м-д", "д.м.Г", "д/м/Г"',
            'end_time' => 'Формат времени должен быть одним из "Г-м-д", "д.м.Г", "д/м/Г"'
        ];
    }
}
