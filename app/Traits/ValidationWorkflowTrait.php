<?php
/**
 * Created by PhpStorm.
 * User: JudeParfait
 * Date: 14/07/2016
 * Time: 16:06
 */

namespace App\Traits;
use App\Modelvalidatingstep;
use App\Validationworkflow;
use App\Modelvalidating;
use App\Modelvalidated;
use App\Modelrejected;

trait ValidationWorkflowTrait
{
    private function getValidating($model, $model_id)
    {
        // Vérifier si ce modèle a un circuit de validation
        $workflow = $this->checkValidation($model);

        if (is_null($workflow) || empty($workflow)) {
            $validating = new Modelvalidating([
                'model' => $model,
                'model_id' => $model_id
            ]);

        } else {
            $validating_count = Modelvalidating::where('model', '=', $model)
                ->where('model_id', '=', $model_id)
                ->count();

            // Nouvelle validation
            $validating = new Modelvalidating([
                'model' => $model,
                'model_id' => $model_id
            ]);
            if ($validating_count == 0) {
                // Pas de validation en cours pour ce modèle

                // On vérifie si ce model n'est pas déjà validé.
                $validated_count = Modelvalidated::where('model', '=', $model)
                    ->where('model_id', '=', $model_id)
                    ->count();
                if ($validated_count == 0) {
                    // On vérifie si ce model n'est pas déjà rejété.
                    $validated_count = Modelrejected::where('model', '=', $model)
                        ->where('model_id', '=', $model_id)
                        ->count();
                    if ($validated_count == 0) {
                        // Aucune validation effectuée, on créer une nouvelle
                        $validating->save();
                        $validated = null;
                    } else {
                        // Modèle rejété
                        $validated = Modelrejected::where('model', '=', $model)
                            ->where('model_id', '=', $model_id)
                            ->orderBy('validate_at', 'desc')
                            ->first();
                    }
                } else {
                    // Modèle déjà validé
                    $validated = Modelvalidated::where('model', '=', $model)
                        ->where('model_id', '=', $model_id)
                        ->orderBy('validate_at', 'desc')
                        ->first();
                }

                if ($validated_count == 0) {

                } else {
                    $validating->setValidated($validated);
                }
            } else {
                // Récupération de la validation en cours
                $validating = Modelvalidating::where('model', '=', $model)
                    ->where('model_id', '=', $model_id)
                    ->first();
            }
        }

        // Affecter le workflow de validation correspondant
        //$validating->setWorkflow($workflow);
        $this->setWorkflowToValidating($validating, $workflow);

        return $validating;
    }

    private function checkValidation($model)
    {
        $workflow = Validationworkflow::where('model', '=', $model)
            ->where('status', '=', 1)
            ->first();
        if (is_null($workflow) || empty($workflow)) {
            // Model à ne pas valider

        } else {
            // Modèle à valider
            //$workflow->steps();
        }

        return $workflow;
    }

    private function setWorkflowToValidating($modelvalidating, $workflow)
    {
        if (is_null($workflow) || empty($workflow)) {
            // Model à ne pas valider
        } else {
            // Modèle à valider
            $workflow->setSteps();
            $modelvalidating->setWorkflow($workflow, $workflow->getsteps(), $workflow->getmaxstep());
        }
    }

    private function validateModel($model, $model_id, $user, $action)
    {
        $modelvalidating = Modelvalidating::where('model', '=', $model)
            ->where('model_id', '=', $model_id)
            ->first();

        $workflow = $this->checkValidation($model);

        $this->setWorkflowToValidating($modelvalidating, $workflow);

        $modelvalidating->addStep($user, $action);

        return $modelvalidating;
    }

    private function deleteModelValidating($model, $model_id, $user)
    {
        $modelvalidating = Modelvalidating::where('model', '=', $model)
            ->where('model_id', '=', $model_id)
            ->first();

        if (is_null($modelvalidating) || empty($modelvalidating)) {
            // aucune validation en cours pour ce modèle
        } else {
            // validation en cours pour ce modèle
            $validatingsteps = Modelvalidatingstep::where('validating_id', '=', $modelvalidating->id)
                ->delete();
            Modelvalidating::destroy($modelvalidating->id);
        }

        return $modelvalidating;
    }
}
