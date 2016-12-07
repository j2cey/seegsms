<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modelrejected extends Model
{
    protected $fillable = ['model', 'model_id', 'step', 'status','validationsteps'];
}
