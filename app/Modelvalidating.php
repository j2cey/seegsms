<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Validationworkflow;
use App\Modelvalidatingstep;
use App\Modelvalidated;
use App\Modelrejected;
use App\Traits\DateUtilitiesTrait;

class Modelvalidating extends Model
{
    use DateUtilitiesTrait;

    protected $fillable = ['model', 'model_id', 'step', 'status', 'validationsteps'];
    protected $workflow;
    protected $workflowsteps;
    protected $workflowmaxstep;
    protected $to_validate = false;
    protected $validated;
    protected $validated_status = 0;

    public function __construct(array $attributes = array())
    {
        /* override your model constructor */
        parent::__construct($attributes);
    }

    public function steps()
    {
        return $this->hasMany('App\Modelvalidatingstep');
    }

    public function addStep($user, $action)
    {
        $modelvalidatingstep = Modelvalidatingstep::create([
            'validating_id' => $this->id,
            'step' => $this->step,
            'user' => $user,
            'action' => $action,
            'validate_at' => $this->getNowDateTime()
        ]);

        if ($this->step == $this->workflowmaxstep || $action < 0) {
            $this->closeValidation($action);
        } else {
            $this->step = $this->step + 1;
        }

        $this->save();
    }

    private function closeValidation($laststatus)
    {

        if (is_null($this->validationsteps) || empty($this->validationsteps)) {
            $allsteps = [];
        } else {
            $allsteps = json_decode($this->validationsteps);
        }

        $steps = Modelvalidatingstep::where('validating_id', '=', $this->id)
            ->orderBy('step', 'asc')
            ->get();

        $stepids = [];
        foreach ($steps as $step) {
            $allsteps[] = $step;
            $stepids[] = $step->id;
        }

        $allsteps = json_encode($allsteps);

        if ($laststatus < 0) {
            $modelvalidated = Modelrejected::create([
                'model' => $this->model,
                'model_id' => $this->model_id,
                'step' => $this->step,
                'status' => $laststatus,
                'validationsteps' => $allsteps,
                'validate_at' => $this->getNowDateTime(),
            ]);
        } else {
            $modelvalidated = Modelvalidated::create([
                'model' => $this->model,
                'model_id' => $this->model_id,
                'step' => $this->step,
                'status' => $laststatus,
                'validationsteps' => $allsteps,
                'validate_at' => $this->getNowDateTime(),
            ]);
        }

        $this->validated_status = $laststatus;
        $this->validated = $modelvalidated;

        // suppression des étapes
        Modelvalidatingstep::destroy($stepids);
        // suppression de la validation
        $this->destroy($this->id);
    }

    public function setWorkflow($workflow, $steps = [], $maxstep = 0)
    {
        //$this->to_validate = ( (is_null($workflow) || empty($workflow)) ? false : true );
        if (is_null($workflow) || empty($workflow)) {
            $this->to_validate = false;
        } else {
            $this->to_validate = true;
            $this->workflowsteps = $steps;
            $this->workflowmaxstep = $maxstep;
        }
        $this->workflow = $workflow;
    }

    public function getWorkflow()
    {
        return $this->workflow;
    }

    public function setValidated($validated)
    {
        $this->validated_status = ((is_null($validated) || empty($validated)) ? 0 : $validated->status);
        $this->validated = $validated;
    }

    public function getValidated()
    {
        return $this->validated;
    }

    public function getValidatedStatus()
    {
        return $this->validated_status;
    }

    public function isValidated()
    {

        if ($this->to_validate) {
            $isvalidate = (($this->validated_status == 0) ? false : ($this->validated_status == 1));
        } else {
            $isvalidate = true;
        }

        return $isvalidate;
    }
}
