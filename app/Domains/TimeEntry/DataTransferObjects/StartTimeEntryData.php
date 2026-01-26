<?php

namespace App\Domains\TimeEntry\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

class StartTimeEntryData extends Data
{
    use ValidationError;

    public function __construct(
        #[Nullable, Exists('projects', 'id')]
        public int $project_id,

        #[Nullable, Exists('tasks', 'id')]
        public ?int $task_id,

        #[Nullable, Exists('programs', 'id')]
        public ?int $program_id,
    ) {}

    public static function attributes(): array
    {
        return [
            'project_id'       => 'проект',
            'task_id'          => 'задача',
            'program_id'       => 'программа',
        ];
    }
}
