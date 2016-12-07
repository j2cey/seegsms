<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\DateUtilitiesTrait;

class Campaignplannings extends Model
{
    use DateUtilitiesTrait;

    protected $fillable = [
        'campaign_id',
        'plan_at',
        'receivers_fileid',
        'status',
        'user_id',
        'plandone_at',
        'stat_all', 'stat_sending', 'stat_success', 'stat_failed', 'stat_done'];

    public function campaign()
    {
        return $this->belongsTo('App\Campaign', 'campaign_id');
    }

    public function file()
    {
        return $this->belongsTo('App\File', 'receivers_fileid');
    }

    public function closePlanning($status, $nb_planned)
    {
        $this->status = $status;
        $this->plandone_at = $this->getNowDateTime();
        $this->stat_all = $nb_planned;
        $this->save();
    }
}
