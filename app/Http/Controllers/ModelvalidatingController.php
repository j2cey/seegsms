<?php

namespace App\Http\Controllers;

use App\Trace;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Modelvalidating;
use App\Traits\ValidationWorkflowTrait;

class ModelvalidatingController extends Controller
{
    use ValidationWorkflowTrait;

    public function modelsvalidate()
    {
        $model = "Campaignplannings";
        $model_id = 4;
        $action = 1;
        $user_id = 2;

        $user = User::find($user_id);

        $username = $user->name;

        // 1. création de la trace
        $trace = new Trace([
            'user' => $user->name,
            'module' => "Modelvalidating",
            'service' => "Validation",
            'request_code' => "",
            'request' => ($action == 1 ? "Validation" : "Réjet" )." du model ".$model." ".$model_id,
        ], true);

        $trace->startnew("validation");

        $validatingdone = $this->validateModel($model, $model_id, $username, $action);

        $trace->endone(1, "succès", 1);

        // Changement d'état pour les modèles validés
        $validated_status = $validatingdone->getValidatedStatus();
        if ($validated_status == 0) {
            // Validation en cours
        } else {
            if ($validated_status > 0) {
                // Validation effectif
                $new_status = 2;
            } else {
                // Réjet
                $new_status = -1;
            }
            $this->campaignplanningChangeStatus($model_id, $new_status);
        }

        dd($validatingdone, $validated_status);
        return response()->success(compact('validatingdone'));
    }
}
