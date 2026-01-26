<?php

namespace App\Domains\Project\DataTransferObjects;

use App\Domains\Shared\Concerns\ValidationError;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\{AfterOrEqual, Date, Max, Min, Nullable, StringType};
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class UpdateProjectData extends Data
{
    use ValidationError;

    public function __construct(
        #[Nullable, StringType, Min(1), Max(255)]
        public ?string $name,

        #[Nullable, StringType, Min(1), Max(1000)]
        public ?string $description,

        #[Nullable, Date]
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'd.m.Y', 'd/m/Y'])]
        #[AfterOrEqual('today')]
        public ?Carbon $deadline,
    ) {}

    public static function attributes(): array
    {
        return [
            'name'       => 'название',
            'description' => 'описание',
            'deadline'   => 'дедлайн',
        ];
    }

    public function getFilledFields(): array
    {
        $filled = [];

        if($this->name !== null) {
            $filled['name'] = $this->name;
        }
        if($this->description !== null) {
            $filled['description'] = $this->description;
        }
        if ($this->deadline !== null) {
            $filled['deadline'] = $this->deadline;
        }
        return $filled;
    }
}
