<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\DateUtilitiesTrait;

class Tracestep extends Model
{
    use DateUtilitiesTrait;

    protected $fillable = [
        'title', 'execode', 'exestring', 'result', 'start_at', 'end_at', 'time', 'trace_id',
    ];

    public function __construct(array $attributes = array(), $start = false)
    {
        /* override your model constructor */
        parent::__construct($attributes);

        // Création d'une nouvelle étape
        if ($start) {
            $this->start_at = $this->getNowDateTime();
            $this->save();
        }
    }

    public function trace()
    {
        return $this->belongsTo('App\Trace');
    }

    public function end($execode, $exestring, $result)
    {
        /*$force_add = true;
        $operation_count = 0;*/

        // Obtention de la pile complète sous forme de tableau
        /*if (empty($this->trace)){
            $trace_tmp = array();
        }else{
            $trace_tmp = json_decode($this->trace, true);

            // Décompte du nombre d'opération du même nom que la nouvelle opération
            foreach ($trace_tmp as $item){
                $nieme_operation = $this->trace_tmp['title']."(".($operation_count + 1).")";

                if ( $item['title'] == $this->trace_tmp['title'] || ( $item['title'] == $nieme_operation ) ){
                    $operation_count = $operation_count + 1;
                }
            }
        }

        // Vérification des conditions d'ajout de la nouvelle trace
        if ($operation_count > 0){
            if ($force_add){
                $this->trace_tmp['title'] = $this->trace_tmp['title']."(".($operation_count + 1).")";
                $new_trace = 0;
            }else{
                $this->trace_tmp['title'] = "";
                $new_trace = (-1) * $operation_count;
            }
        }else{
            $new_trace = 0;
        }*/

        /*if ($new_trace == 0){
            list($this->trace_tmp['execode'],$this->trace_tmp['exestring'],$this->trace_tmp['result'],$this->trace_tmp['end']) = [$execode,$exestring,$result,$this->getNowDateTime()];

            $this->trace_tmp['time'] = $this->diffDateTime($this->trace_tmp['start'], $this->trace_tmp['end']);

            $trace_tmp[] = $this->trace_tmp;
            $new_trace = json_encode($trace_tmp);

            $this->trace = $new_trace;
        }else{
            // Impossible d'ajouter la nouvelle trace
        }*/

        list($this->execode, $this->exestring, $this->result, $this->end_at) = [$execode, $exestring, $result, $this->getNowDateTime()];

        $this->time = $this->diffDateTime($this->start_at, $this->end_at);

        $this->save();

        return 1;
    }
}
