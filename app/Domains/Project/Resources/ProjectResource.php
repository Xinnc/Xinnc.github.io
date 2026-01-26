<?php

namespace App\Domains\Project\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'deadline' => $this->deadline,
            'manager_id' => $this->manager ? [
                'id' => $this->manager->id,
                'name' => "{$this->manager->first_name} {$this->manager->surname}",
                'email' => $this->manager->email,
            ] : null,
            'status' => $this->status
        ];
    }
}
