<?php

namespace App\Domains\Task\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'project_name' => $this->project->name,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status
        ];
    }
}
