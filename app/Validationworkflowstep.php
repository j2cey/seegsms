<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Validationworkflowstep extends Model
{
    protected $fillable = [
        'workflow_id','step','validator',
    ];

    public function workflow()
    {
        return $this->belongsTo('App\Validationworkflow', 'workflow_id');
    }
}
