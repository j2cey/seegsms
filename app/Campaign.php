<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'title', 'descript', 'msg', 'campaigntype_id', 'result', 'status', 'campaign_fileid',
    ];

    /**
     * Le type de la campagne
     *
     * @return mixed
     */
    public function campaigntype()
    {
        return $this->belongsTo('App\Campaigntype');
    }

    public function campaignplannings()
    {
        return $this->hasMany('App\Campaignplannings', 'campaign_id');
    }

    public function file()
    {
        return $this->belongsTo('App\File', 'campaign_fileid');
    }
}
