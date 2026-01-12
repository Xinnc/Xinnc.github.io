<?php

namespace App\Domains\Shared\Model;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name',
        'is_active'
    ];
}
