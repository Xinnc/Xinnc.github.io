<?php

namespace App\Domains\Shared\Model;

use App\Domains\Shared\Exceptions\ForbiddenForYouException;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name',
        'is_active'
    ];

    public function ensureIsActive(): void
    {
        throw_if(! $this->is_active, new ForbiddenForYouException(403, 'Программа не активна!'));
    }
}
