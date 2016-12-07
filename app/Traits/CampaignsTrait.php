<?php
/**
 * Created by PhpStorm.
 * User: JudeParfait
 * Date: 22/07/2016
 * Time: 14:37
 */

namespace App\Traits;

use App\Campaign;
use App\Campaignplannings;
use App\Trace;
use App\File as AppFile;

trait CampaignsTrait
{
    /**
     * Cree une nouvelle campagne dans le systeme
     *
     * @param $campaigndata
     * @param $user
     * @param $trace
     * @param $file
     * @param bool|false $endrequest
     * @return null|static
     */
    private function addNewCampaign($campaigndata, $user, &$trace, $file, $endrequest = false)
    {
        if (is_null($trace)) {
            $trace = new Trace([
                'user' => $user->name,
                'module' => "Campagne",
                'service' => "Ajout",
                'request_code' => "",
                'request' => "Ajout campagne " . $campaigndata->title,
            ], true);
        }

        // 2. save campaign
        $campaigndata_tmp = [
            'user_id' => $user->id,
            'title' => $campaigndata->title,
            'descript' => $campaigndata->descript,
            'campaigntype_id' => $campaigndata->campaigntype_id,
        ];

        if (is_null($file)) {
            $filesaved[0] = 1;
            $filesaved[1] = null;
            $campaigndata_tmp['msg'] = $campaigndata->msg;
        } else {
            // Campagne par fichier
            if ($file[0] == 1) {
                // fichier existant
                $fileObj = AppFile::find($file[1]);
                $filesaved[0] = 1;
                $filesaved[1] = $fileObj;
            } else {
                $fileObj = new AppFile();
                $filesaved = $fileObj->storeAndSave($trace, $file[1]);
            }

            $campaigndata_tmp['campaign_fileid'] = $filesaved[1]->id;
        }

        // Enregistrement d'un fichier dont l'id a été introduite avec les donnees de la campagne
        if (isset($campaigndata->campaign_fileid)) {
            $campaigndata_tmp['campaign_fileid'] = $campaigndata->campaign_fileid;
        }

        $trace->startnew("inscription de la campagne");

        if ($filesaved[0] < 0) {
            $result = [-1, 'Echec enregistrement du fichier'];
            $campaign = null;
        } else {
            $campaign = Campaign::create($campaigndata_tmp);

            // Mise en validation (le cas échéant)
            $campaign_validating = $this->getValidating("Campaign", $campaign->id);
            $result = [1, 'Succès'];
        }

        //$trace->endone(1, "succès", 1, $endrequest);
        $trace->endone(1, $result[1], $result[0], $endrequest);

        return $campaign;
    }

    private function addNewCampaignplanning($campaignplanningdata, $file, $user, &$trace, $endrequest = false)
    {
        $result = [0, null];
        $campaignplanning = null;

        if (is_null($trace)) {
            $trace = new Trace([
                'user' => $user->name,
                'module' => "Campagne",
                'service' => "Ajout planification",
                'request_code' => "",
                'request' => "Ajout planification de la campagne " . $campaignplanningdata->campaign_id,
            ], true);
        }

        $campaignplanningdata_tmp = [
            'campaign_id' => $campaignplanningdata->campaign_id,
            'user_id' => $user->id,
            'plan_at' => $campaignplanningdata->plan_at,
            'status' => 2,
        ];

        if (is_null($file)) {
            $filesaved[0] = 1;
            $filesaved[1] = null;
        } else {
            if ($file[0] == 1) {
                // fichier existant
                $fileObj = AppFile::find($file[1]);
                $filesaved[0] = 1;
                $filesaved[1] = $fileObj;
            } else {
                $fileObj = new AppFile();
                $filesaved = $fileObj->storeAndSave($trace, $file[1]);
            }

            $campaignplanningdata_tmp['receivers_fileid'] = $filesaved[1]->id;
        }

        $trace->startnew("inscription de la planification");

        if ($filesaved[0] < 0) {
            $result = [-1, 'Echec enregistrement du fichier'];
        } else {
            // 3. save planning
            $campaignplanning = Campaignplannings::create($campaignplanningdata_tmp);

            // Mise en validation (le cas échéant)
            $campaignplanning_validating = $this->getValidating("Campaignplannings", $campaignplanning->id);

            if ($campaignplanning_validating->isValidated()) {
                //
            } else {
                $this->campaignplanningChangeStatus($campaignplanning->id, 1);
            }

            $result = [1, 'Succès'];
        }

        $trace->endone(1, $result[1], $result[0], $endrequest);

        return [$result[0], $result[1], $campaignplanning];
    }

    private function deleteCampaignplanning($id, $user, &$trace, $endrequest = false)
    {
        $result = [0, null];

        if (is_null($trace)) {
            $trace = new Trace([
                'user' => $user->name,
                'module' => "Campagne",
                'service' => "Suppression planification",
                'request_code' => "",
                'request' => "Suppression planification " . $id,
            ], true);
        }

        $trace->startnew("suppression de la planification " . $id);
    }

    private function campaignplanningChangeStatus($id, $newstatus)
    {
        return Campaignplannings::where('id', $id)
            ->update(['status' => $newstatus]);
    }
}
