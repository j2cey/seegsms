<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\DateUtilitiesTrait;
use App\Tracestep;

class Trace extends Model
{
    use DateUtilitiesTrait;

    protected $fillable = [
        'user', 'module', 'service', 'request_code', 'request', 'trace_report', 'status', 'result', 'start_at', 'end_at', 'time',
    ];

    /**
     * Opération en cours d'exécution.
     *
     * @var Tracestep
     */
    protected $currstep;

    public function __construct(array $attributes = array(), $start = false)
    {
        /* override your model constructor */
        parent::__construct($attributes);

        // Création d'une nouvelle requête
        if ($start) {
            $this->start_at = $this->getNowDateTime();
            $this->save();
        }
    }

    public function steps()
    {
        return $this->hasMany('App\Tracestep');
    }

    /**
     * Get the start_at attribute.
     * @param  $date
     * @return string
     */
    public function getStartAtAttribute($date)
    {
        return $this->defaultDateTimeFormated($date);
    }

    /**
     * Get the end_at attribute.
     * @param  $date
     * @return string
     */
    public function getEndAtAttribute($date)
    {
        return $this->defaultDateTimeFormated($date);
    }

    /**
     * Réinitialise l'opération temporaire (en cours d'exécution)
     */
    private function resetTrace()
    {
        /*foreach ($this->trace_tmp as $item){
            $item = null;
        }*/
        $this->currstep = null;
    }

    /**
     * Démarre une nouvelle opération
     * @param $title
     */
    public function startnew($title)
    {
        $this->resetTrace();

        /*$this->trace_tmp['title'] = $title;
        $this->trace_tmp['start'] = $this->getNowDateTime();
        $this->trace_tmp['execode'] = 0;*/

        $this->currstep = new Tracestep(['title' => $title, 'start_at' => $this->getNowDateTime(), 'execode' => 0, 'trace_id' => $this->id], true);
    }

    /**
     * Termine l'opération en cours
     *
     * @param $execode      int         Code d'exécution de l'opération
     * @param $exestring    string      Text d'exécution de l'opération
     * @param $result       int         Résultat d'exécution de l'opération
     * @param bool|false $endrequest Détermine si cette opération marque la fin de la requête
     * @return int
     */
    public function endone($execode, $exestring, $result, $endrequest = false)
    {
        $this->currstep->end($execode, $exestring, $result);

        if ($endrequest) {
            $this->closeRequest();
        }

        return 1;
    }

    public function endException(\Exception $exception, $execode)
    {
        //dd($exception->getMessage());
        return $this->endone($execode, $exception->getMessage(), -1, true);
    }

    /**
     * Termine la requête
     */
    public function closeRequest()
    {
        $this->end_at = $this->getNowDateTime();
        $this->time = $this->diffDateTime($this->start_at, $this->end_at);
        $this->result = 1;
        $this->status = "execute";

        $this->save();
    }
}
