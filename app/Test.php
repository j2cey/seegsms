<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field1', 'field2', 'field3', 'field4', 'field5',
    ];

    protected $table = "sestest";
}
