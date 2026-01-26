<?php

namespace App\Domains\Task\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use App\Domains\Task\Enums\TaskStatus;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class StatusUpdateTaskData extends Data
{
    use ValidationError;

    public function __construct(
        #[Required, Enum(TaskStatus::class)]
        public ?string $status,
    ) {}

    public static function attributes(): array
    {
        return [
            'status' => 'статус',
        ];
    }

    public static function messages(): array
    {
        $allowedValues = array_column(TaskStatus::cases(), 'value');
        $formattedValues = implode(', ', $allowedValues);

        return [
            'status.enum' => "Статус должен быть одним из: {$formattedValues}.",
        ];
    }
}
