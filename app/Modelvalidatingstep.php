<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modelvalidatingstep extends Model
{
    protected $fillable = [
        'validating_id',
        'step', 'user', 'action', 'validate_at',
    ];
}
