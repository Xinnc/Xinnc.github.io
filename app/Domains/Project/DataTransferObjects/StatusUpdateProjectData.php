<?php

namespace App\Domains\Project\DataTransferObjects;

use App\Domains\Project\Enums\ProjectStatus;
use App\Domains\Shared\Concerns\ValidationError;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class StatusUpdateProjectData extends Data
{
    use ValidationError;

    public function __construct(
        #[Required, Enum(ProjectStatus::class)]
        public string $status,
    ) {}

    public static function attributes(): array
    {
        return [
            'status' => 'статус',
        ];
    }

//    public function getStatusValue(): string
//    {
//        return $this->status;
//    }
}
