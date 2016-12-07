<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Validationworkflowstep;

class Validationworkflow extends Model
{
    protected $fillable = [
        'title', 'model', 'status',
    ];

    protected $workflowsteps;
    protected $maxstep;

    public function totalStep()
    {
        $totalstep = 1;
        $nextstep = 2;

        while ($nextstep > 0) {
            if (property_exists($this, "step" . $nextstep)) {
                // L'étape existe comme propriété de la classe
                // On vérifie si l'atpe est null ou vide
                if (is_null($this->{$property}) || empty($this->{$property})) {
                    $nextstep = 0;
                } else {
                    $totalstep = $nextstep;
                    $nextstep = $nextstep + 1;
                }
            } else {
                $nextstep = 0;
            }
        }

        return $totalstep;
    }

    public function steps()
    {
        return $this->hasMany('App\Validationworkflowstep', 'workflow_id');
    }

    public function setSteps()
    {
        $this->workflowsteps = Validationworkflowstep::where('workflow_id', '=', $this->id)
            ->get();//$this->hasMany('App\Validationworkflowstep','workflow_id');

        $this->determineMaxstep();
    }

    private function determineMaxstep()
    {
        $this->maxstep = 0;

        if (is_null($this->workflowsteps)) {
            //
        } else {
            foreach ($this->workflowsteps as $workflowstep) {

                if ($workflowstep->step > $this->maxstep) {
                    $this->maxstep = $workflowstep->step;
                }
            }
        }

        /*$this->maxstep = Validationworkflowstep::where('workflow_id', '=', $this->id)
            ->max('step');*/

        return $this->maxstep;
    }

    public function getmaxstep()
    {
        return $this->maxstep;
    }

    public function getsteps()
    {
        return $this->workflowsteps;
    }
}
