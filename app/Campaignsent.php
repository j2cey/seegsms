<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaignsent extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'campaign_id', 'planning_id', 'msg', 'receiver',
        'receiverinfos', 'resultcode', 'resulttrace', 'nbtry',
        'resultstring', 'status', 'plan_at', 'start_at', 'end_at','pickupflag'
    ];

    public function __construct(array $attributes = array())
    {
        /* override your model constructor */
        parent::__construct($attributes);
    }
}
