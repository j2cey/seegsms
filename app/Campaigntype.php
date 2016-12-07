<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaigntype extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'descript', 'prioritylevel',
    ];

    /**
     * Les campagnes ayant ce type
     *
     * @return mixed
     */
    public function campaigns()
    {
        return $this->hasMany('App\Campaign');
    }
}
