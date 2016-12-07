<?php

namespace App\Http\Controllers;

use App\Campaignsending;
use App\Campaignsent;
use App\Campaigntype;
use App\Campaign;
use App\Taskqueue;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Traits\SqlTrait;
use App\Campaignplannings;
use App\Trace;
use App\Modelvalidating;
use App\Traits\ValidationWorkflowTrait;
use Illuminate\Support\Facades\Config;
use League\Csv\Reader as csvreader;
use League\Csv\Writer as csvwriter;
use App\Traits\FilesTrait;
use App\Commands\SMS\Sms;
use App\Traits\CampaignsTrait;
use App\File as AppFile;

class CampaignplanningController extends Controller
{
    use SqlTrait, ValidationWorkflowTrait, FilesTrait, CampaignsTrait {
        SqlTrait::defaultDateTimeFormated insteadof FilesTrait;
        SqlTrait::getNowDateTime insteadof FilesTrait;
        SqlTrait::diffTimeStamp insteadof FilesTrait;
        SqlTrait::tsUnixDate insteadof FilesTrait;
        SqlTrait::diffDateTime insteadof FilesTrait;
    }

    /**
     * Plannifie les campagnes validées dont la date de planification est atteinte.
     * @return int
     */
    public function smsplan()
    {
        $plan_rslt = 0;
        $nb_planned = 0;
        $nbcheck = 0;
        $checkdone = false;

        // 1. Obtenir une planification non effectuée et dont la date de planification est échue
        //$trace->startnew("recherche d'une planification non échue");

        $campaignplanning = null;

        try {
            while (!($checkdone)) {
                $campaignplanning = Campaignplannings::where('plan_at', '<=', $this->getNowDateTime())
                    ->where('status', 2)
                    ->skip($nbcheck)
                    ->first();

                if (is_null($campaignplanning)) {
                    //$trace->endone(1, "aucune planification disponible", 0, true);
                    $checkdone = true;
                } else {
                    $campaignplanning_validating = $this->getValidating("Campaignplannings", $campaignplanning->id);

                    if ($campaignplanning_validating->isValidated()) {
                        //$trace->endone(1, "succès après ".($nbcheck + 1)." tentatives", 1);
                        $plan_rslt = 1;
                        $checkdone = true;
                    } else {
                        //$trace->endone(1, "planification (" . $campaignplanning->id . ") non validée", 1, true);

                        // A faire: Boucler les suivants, jusqu'à avoir une bonne planification ou plus du tout de planification

                        $plan_rslt = 0;
                    }
                }

                if ($nbcheck == 3) {
                    //dd($campaignplanning);
                }

                $nbcheck = $nbcheck + 1;
            }

            if ($plan_rslt > 0 && !(is_null($campaignplanning))) {
                $campaign = $campaignplanning->campaign;
                $campaigntype = Campaigntype::find($campaign->campaigntype_id);
                $campaignfile = $campaign->file;

                if (is_null($campaignfile)) {
                    $file = $campaignplanning->file;

                    // 2. Importation du fichier des destinataires
                    //$trace->startnew("Chargement du fichier des destinataires");
                    $table_tmp = "receivers_" . uniqid();

                    $db_rslt = $this->createNewTable($table_tmp, ["mobile VARCHAR(255) not null", "receiver_id INTEGER"]);
                    if ($db_rslt['qry'] !== true) {
                        //$trace->endone(1, "échec création de la table temp", -1, true);
                        $plan_rslt = -1;
                    } else {
                        // Table temp créée avec succès, on peut procéder à l'importation du fichier
                        $db_rslt = $this->copyFlatFileToTable($file->local_path, $table_tmp . "(mobile)", null);
                        if ($db_rslt['qry'] !== true) {
                            //$trace->endone(1, "échec importation du fichier", -2, true);
                            $plan_rslt = -2;
                        } else {
                            //$trace->endone(1, "succès", 1);
                        }
                    }

                    if ($plan_rslt > 0) {
                        // La table temp est créée et chargée
                        // 3. Traitement des doublons
                        //$trace->startnew("Traitement des doublons");

                        $doublons = $this->getDoublonsOneColumn($table_tmp, "mobile");

                        if (is_null($doublons['qry']) || empty($doublons['qry'])) {
                            //$trace->endone(1, "aucun doublon détecté", 0);
                        } else {
                            $db_rslt = $this->deleteDoublonsOneColumn($table_tmp, "mobile");
                            if (is_null($db_rslt['qry']) || empty($db_rslt['qry']) || ($db_rslt['qry'] < 1)) {
                                //$trace->endone(1, count($doublons['qry']) . " doublon détectés et Echec suppression", -3, true);
                                $plan_rslt = -3;
                            } else {
                                //$trace->endone(1, count($doublons['qry']) . " doublon détectés et " . $db_rslt['qry'] . " supprimés", 1);
                            }
                        }

                        if ($plan_rslt > 0) {
                            // 4. Delete des lignes non conformes
                            /*$trace->startnew("suppression des lignes non conformes");

                            $mobilefails = $this->getMobileFalse($table_tmp, "mobile");

                            if (is_null($mobilefails['qry']) || empty($mobilefails['qry'])) {
                                $trace->endone(1, "aucune ligne non conforme détectée", 0);
                            } else {
                                $db_rslt = $this->deleteMobileFalse($table_tmp, "mobile");
                                if (is_null($db_rslt['qry']) || empty($db_rslt['qry']) || ($db_rslt['qry'] < 1)) {
                                    $trace->endone(1, count($mobilefails['qry']) . " ligne(s) non conforme(s) détecté(s) et Echec suppression", -4, true);
                                    $plan_rslt = -4;
                                } else {
                                    $trace->endone(1, count($mobilefails['qry']) . " ligne(s) non conforme(s) détecté(s) et " . $db_rslt['qry'] . " supprimée(s)", 1);
                                }
                            }*/

                            if ($plan_rslt > 0) {
                                // 5. inscription des destinataires à la planification
                                //$trace->startnew("inscription des destinataires à la planification");
                                // Ajout de l'indicatif
                                $db_rslt = $this->addIndicatifMobile($table_tmp);

                                // Etapes à repéter jusqu'à ce que la nouvelle table soit vide
                                $table_tmp_count = $this->countTable($table_tmp, "*");
                                $nb_planned = $table_tmp_count['qry'];

                                while ($table_tmp_count['qry'] > 0) {
                                    // 5.1. récupération des ids de destinataires existant dans la table des destinataires
                                    $upd_rslt = $this->updFromOneColumn("receivers", "mobile", "id", $table_tmp, "mobile", "receiver_id");

                                    // 5.2. plan receivers
                                    $planreceiv_rslt = $this->planReceivers($table_tmp, $campaignplanning->id);

                                    // 5.3. delete planned receivers
                                    $del_rslt = $this->delReceiversPlanned($table_tmp);

                                    // 5.4. insertion dans la table des destinataires
                                    $add_rslt = $this->addReceivers($table_tmp);

                                    $table_tmp_count = $this->countTable($table_tmp, "*");

                                    //dd(['upd'=>$upd_rslt,'plan'=>$plan_rslt,'del'=>$del_rslt,'add'=>$add_rslt,'tblcnt'=>$table_tmp_count['qry']]);
                                }

                                //$trace->endone(1, "traitement effectué", 1, true);

                                // 6. inscription à la liste d'envoi
                                //$trace->startnew("inscription à la liste d'envoi");

                                $sending_rslt = $this->sendPlan($campaignplanning->id, $campaign->id, $campaign->msg, $campaigntype->prioritylevel);

                                //$trace->endone(1, ($sending_rslt['qry'] ? "succès traitement" : "échèc traitement"), ($sending_rslt['qry'] ? 1 : -1), true);

                                // 7. clôturer la planification
                                $campaignplanning->closePlanning(3, $nb_planned);
                            }
                        }

                        // supression de la table temporaire
                        $db_rslt = $this->dropTable($table_tmp);
                    }
                } else {
                    // Campagne constituée à partir d'un fichier
                    //$trace->startnew("Chargement du fichier de campagne");
                    $table_tmp = "campaignsendingtemps";
                    $db_rslt = $this->copyFlatFileToTable($campaignfile->local_path, $table_tmp . "(msg,receiver)", ";");
                    if ($db_rslt['qry'] !== true) {
                        //$trace->endone(1, "échec importation du fichier", -2, true);
                        $plan_rslt = -2;
                    } else {
                        //$trace->endone(1, "succès", 1);
                        //$trace->startnew("Mise en LIVE");

                        // Ajout d'infos additionnelles
                        $db_rslt = $this->updSimple($table_tmp, [
                            'campaign_id' => $campaign->id,
                            'planning_id' => $campaignplanning->id,
                            'prioritylevel' => $campaigntype->prioritylevel
                        ]);

                        $nb_planned = $db_rslt['qry'];

                        if ($db_rslt['qry'] < 0) {
                            $plan_rslt = -5;
                            //$trace->endone(1, "échec Ajout d'infos additionnelles", $plan_rslt, true);
                        } else {
                            $doublons = $this->getDoublonsManyColumns($table_tmp, ['msg', 'receiver']);

                            if (is_null($doublons['qry']) || empty($doublons['qry'])) {
                                //$trace->endone(1, "aucun doublon détecté", 0);
                            } else {
                                $db_rslt = $this->deleteDoublonsManyColumn($table_tmp, ['msg', 'receiver']);
                                if (is_null($db_rslt['qry']) || empty($db_rslt['qry']) || ($db_rslt['qry'] < 1)) {
                                    //$trace->endone(1, count($doublons['qry']) . " doublon détectés et Echec suppression", -3, true);
                                    $plan_rslt = -3;
                                } else {
                                    //$trace->endone(1, count($doublons['qry']) . " doublon détectés et " . $db_rslt['qry'] . " supprimés", 1);
                                }

                                // Retrait du nombre de doublons sur le total planifié
                                $nb_planned = (int)$nb_planned - count($doublons['qry']);
                            }

                            if ($plan_rslt > 0) {
                                // Transfert des infos vers la table live
                                $db_rslt = $this->insFromTable($table_tmp, "campaignsendings", [
                                    'campaign_id',
                                    'planning_id',
                                    'msg',
                                    'receiver',
                                    'prioritylevel'
                                ]);

                                if ($db_rslt['qry'] !== true) {
                                    $plan_rslt = -6;
                                    //$trace->endone(1, "échec insertion dans la table LIVE", $plan_rslt, true);
                                } else {
                                    // Vidage (truncate) de la table temp
                                    $db_rslt = $this->truncTable($table_tmp);

                                    // Clôturer la planification
                                    $campaignplanning->closePlanning(3, $nb_planned);

                                    //$trace->endone(1, "succès", 1);
                                }
                            }
                        }
                    }
                }
            }

            //dd($campaignplanning);

            return $plan_rslt;

        } catch (\Exception $e) {
            $trace = new Trace([
                'user' => "sys",
                'module' => "Campagne",
                'service' => "Planification",
                'request_code' => "",
                'request' => "planification des campagnes dont la date de planification est échue",
            ], true);

            $trace->startnew("Erreur inattendue");
            $trace->endException($e, -1);
        }
    }

    /**
     * Récupère et enregistre les SMS dans des fichiers afin de les traiter.
     */
    public function smspickup()
    {
        $rslt = 0;

        try {
            $uid = uniqid();
            $nextask = Config::get('taskconfig.tasks.0');

            $sendingtable = "campaignsendings";
            $newpickedfile = public_path(env('SMSFILE_RAW', 'files/smscampaign/') . 'new_' . $uid . '.csv');

            //$trace->startnew("blocage (freez) des lignes");
            $pickedlines = $this->freezLines($sendingtable, $uid, 10);

            if ($pickedlines['qry'] > 0) {
                // Modification du statut de la planif
                $this->changePlanningStatusFromPickupflag($uid);

                // Modification des Stats des planifs
                $this->updPlanningStatsByCountId($uid, null, "campaignsendings", "stat_sending", true);

                //$trace->endone(1, $pickedlines['qry']." lignes bloquées", 1);

                //$trace->startnew("récupération des lignes bloquées de la BD");
                $campaignsendings = Campaignsending::where('pickupflag', $uid)
                    ->get();
                //$trace->endone(1, "succès", 1);

                //$trace->startnew("écriture dans le fichier");
                // create a file
                $csvwriter = csvwriter::createFromFileObject(new \SplFileObject($newpickedfile, 'w'));
                $csvwriter->setDelimiter(';');

                //$csvwriter->insertOne(\Schema::getColumnListing('campaignsendings'));

                foreach ($campaignsendings as $campaignsending) {
                    $csvwriter->insertOne($campaignsending->toArray());

                    $rslt++;
                }

                // Incription d'une tâche d'envoi SMS dans la file
                Taskqueue::create(['taskcode' => $nextask['code'], 'taskuid' => $uid, 'taskdesc' => $nextask['desc']]);

                //$file_rslt = $this->fileLockAndWrite($pickedlistfile,$uid."|0");
                //$trace->endone(1, "succès enregistrement fichier ".$uid, 1, true);

            } else {
                //$trace->endone(1, "aucune ligne disponible", 1, true);
            }

            return $rslt;

        } catch (\Exception $e) {
            dd($e->getMessage());
            $trace = new Trace([
                'user' => "sys",
                'module' => "SMS",
                'service' => "Pickup lines",
                'request_code' => "",
                'request' => "récupération des lignes pour traitement",
            ], true);

            $trace->startnew("Erreur inattendue");
            $trace->endException($e, -1);
        }
    }

    /**
     * Traite et envoie les SMS contenus dans les fichiers.
     */
    public function smssend()
    {
        $rslt = 0;

        try {
            $newpickedfile_template = public_path(env('SMSFILE_RAW', 'files/smscampaign/') . 'new_{uid}.csv');
            $newsmsdonefile_template = public_path(env('SMSFILE_RAW', 'files/smscampaign/') . 'smsdone_{uid}.csv');

            $maxtry = (int)env('SMSFILE_MAXTRY');

            /* SMS */
            $sms = new Sms();
            $smssender = "SEEG";

            /* TASK CONFIG */
            $currtask = Config::get('taskconfig.tasks.0');
            $nextask = Config::get('taskconfig.tasks.1');

            //$trace->startnew("récupération d'une tache envoi SMS");
            $taskqueue = new Taskqueue(['taskcode' => $currtask['code']]);

            if ($taskqueue->getfree()) {
                //$trace->endone(1, "fichier ".$taskqueue->taskuid." récupéré et bloqué avec la clef ".$taskqueue->pickupuid, 1);

                $newpickedfile = strtr($newpickedfile_template, array('{uid}' => $taskqueue->taskuid));
                $newsmsdonefile = strtr($newsmsdonefile_template, array('{uid}' => $taskqueue->taskuid));

                //$trace->startnew("traitement du fichier");

                if (file_exists($newpickedfile)) {
                    $csvreader = csvreader::createFromPath($newpickedfile);
                    $csvwriter = csvwriter::createFromFileObject(new \SplFileObject($newsmsdonefile, 'w'));
                    $csvwriter->setDelimiter(';');
                    $csvreader->setDelimiter(';');

                    foreach ($csvreader as $index => $row) {
                        $row_arr = $row; //explode(";",$row[0]);

                        $newtracestep = $this->newTracestep("envoi vers " . $row_arr[4]);

                        // le message msg
                        $msg = $row_arr[3];
                        if ($this->isphonenumber($row_arr[4])) {
                            $send_rslt = $sms->send($msg,$smssender,$row_arr[4]);

                            if ($send_rslt > 0) {
                                // trace
                                $sendrslt_str = "succès";
                                $newtrace = $this->addTraceStep($row_arr[7], $newtracestep, 1, "succès", 1);
                            } else {
                                // trace
                                $sendrslt_str = "erreur inatendue";
                                $newtrace = $this->addTraceStep($row_arr[7], $newtracestep, 1, "erreur inatendue", $send_rslt);
                            }

                        } else {
                            $send_rslt = -1;
                            // trace
                            $sendrslt_str = "numero de telephone incorrect";
                            $newtrace = $this->addTraceStep($row_arr[7], $newtracestep, 1, "numero de telephone incorrect", -3);
                        }

                        // résultat du traitement resultcode
                        $row_arr[6] = 1;

                        // nombre d'essaie nbtry
                        $row_arr[8] = ((int)$row_arr[8]) + 1;

                        // start start_at
                        if (empty($row_arr[12])) {
                            $row_arr[12] = $this->getNowDateTime();
                        } else {
                            //
                        }
                        // statut status et end end_at
                        if ($send_rslt == 1) {
                            $row_arr[13] = $this->getNowDateTime();
                            // résultat du traitement resultstring
                            $row_arr[9] = $sendrslt_str;
                            // statut status
                            $row_arr[10] = 1;
                        } elseif ($row_arr[8] == $maxtry) {
                            $row_arr[13] = $this->getNowDateTime();
                            // résultat du traitement resultstring
                            $row_arr[9] = $sendrslt_str . "";
                            // statut status
                            $row_arr[10] = -2;
                        } else {
                            $row_arr[9] = $sendrslt_str . ", Traitement en cours";
                            $row_arr[10] = -1;
                        }

                        $csvwriter->insertOne($row_arr);

                        $rslt++;
                    }

                    // Go to next task
                    $taskqueue->goNext($nextask['code'], $nextask['desc']);

                    // Add picked file to delete
                    $this->deleteFile($newpickedfile);

                    // MAJ Stats
                    //$this->updPlanningStatsByPickupflagStatus($taskqueue->taskuid, -1, $tablefrom, $updfiled, $incrementvalue, $increment = true);

                    //$trace->endone(1, "fin traitement du fichier", 1, true);
                } else {
                    $taskqueue->setfree();
                    //$trace->endone(1, "fichier innexistant: ".$newpickedfile, -1, true);
                }
            } else {
                //$trace->endone(1, "aucun fichier disponible", 1, true);
            }

            return $rslt;

        } catch (\Exception $e) {
            $trace = new Trace([
                'user' => "sys",
                'module' => "SMS",
                'service' => "Send lines",
                'request_code' => "",
                'request' => "envoi des SMS à partir des fichiers",
            ], true);

            $trace->startnew("Erreur inattendue");
            $trace->endException($e, -1);
        }
    }

    private function isphonenumber($phonenumber)
    {
        return (is_numeric($phonenumber) && ((strlen($phonenumber) == 8) || (strlen($phonenumber) == 11)));
    }

    /**
     * Met à jour les statistiques après envoi de sms
     *
     * @param string $planning_id ID de la planification
     * @param bool|true $success
     */
    private function statNewSendingDone($planning_id, $success = true)
    {
        $incrementvalue = 1;

        if ($success) {
            // One success more
            $this->incrementPlanningStats($planning_id, "stat_success", $incrementvalue);
        } else {
            // One faillure more
            $this->incrementPlanningStats($planning_id, "stat_failed", $incrementvalue);
        }

        // One sending less
        $this->decrementPlanningStats($planning_id, "stat_sending", $incrementvalue);
        // One done more
        $this->incrementPlanningStats($planning_id, "stat_done", $incrementvalue);
    }

    /**
     * Rapporte le résultat des traitements dans la base de données.
     */
    public function smsdbmaj()
    {
        $rslt = 0;

        /*$treatedlistfile = public_path( env('SMSFILE_TREATED_LIST', 'files/smscampaign/smstreatedlist.txt') );
        $todeletelistfile = public_path( env('SMSFILE_TO_DELETE', 'files/smscampaign/smsfiletodeletelist.txt') );*/

        //try {

        $newsmsdonefile_template = public_path(env('SMSFILE_RAW', 'files/smscampaign/') . 'smsdone_{uid}.csv');

        $currtask = Config::get('taskconfig.tasks.1');

        //$trace->startnew("récupération d'un fichier libre");

        $taskqueue = new Taskqueue(['taskcode' => $currtask['code']]);
        if ($taskqueue->getfree()) {
            //$trace->endone(1, "fichier ".$taskqueue->taskuid." récupéré et bloqué avec la clef ".$taskqueue->pickupuid, 1);

            $newsmsdonefile = strtr($newsmsdonefile_template, array('{uid}' => $taskqueue->taskuid));

            //$trace->startnew("traitement du fichier");

            if (file_exists($newsmsdonefile)) {
                $csvreader = csvreader::createFromPath($newsmsdonefile);
                $csvreader->setDelimiter(';');

                $endlines = [];
                foreach ($csvreader as $index => $row) {
                    //do something meaningful here with $row !!
                    //$row is an array where each item represent a CSV data cell
                    //$index is the CSV row index

                    //$row_arr = explode(";",$row[0]);

                    $row_model = [
                        'campaign_id' => $row[1], 'planning_id' => $row[2],
                        'msg' => $row[3], 'receiver' => $row[4], 'receiverinfos' => $row[5],
                        'resultcode' => $row[6], 'resulttrace' => $row[7], 'nbtry' => $row[8],
                        'resultstring' => $row[9], 'status' => $row[10]
                    ];

                    if (empty($row[11])) {
                        //
                    } else {
                        $row_model['plan_at'] = $row[11];
                    }

                    if (empty($row[12])) {
                        //
                    } else {
                        $row_model['start_at'] = $row[12];
                    }

                    if (empty($row[13])) {
                        //
                    } else {
                        $row_model['end_at'] = $row[13];
                    }

                    //dd($row,$row_model);

                    $campagnesent = -1;
                    $campagnesending = -1;
                    $sending_rslt = (int)$row[10];
                    if ($row[10] == "1" || ($sending_rslt == -2)) {
                        $row_model['pickupflag'] = $row[14];

                        $campagnesent = new Campaignsent($row_model);

                        $endlines[] = $row[0];
                        $campagnesent->save();

                        $del_rslt = Campaignsending::destroy($endlines);

                        // Mise à jour du statut
                        $this->changePlanningStatusFromPickupflag($row_model['pickupflag']);

                        // ? vider le pickupflag après traitement

                        // MAJ Stats
                        if ($sending_rslt < 0) {
                            $this->statNewSendingDone($row[2], false);
                        } else {
                            $this->statNewSendingDone($row[2], true);
                        }

                        // Traitements de fin d'envoi, le cas échéant
                        $this->endSending($campagnesent);
                    } else {
                        //$row_model['id'] = $row[0];

                        $row_model['pickupflag'] = "0";//$row[14];
                        $row_model['prioritylevel'] = $row[15];

                        $campagnesending = Campaignsending::where('id', $row[0])
                            ->update($row_model);
                    }
                    $rslt++;
                }

                if (empty($endlines)) {
                    //
                } else {
                    //
                }

                // Delete uid request from file to process
                $taskqueue->endqueue();

                // Add picked file to delete
                //$this->fileLockAndWrite($todeletelistfile,$newsmsdonefile);
                $this->deleteFile($newsmsdonefile);

                //$trace->endone(1, "fin traitement du fichier", 1, true);

            } else {
                $taskqueue->setfree();
                //$trace->endone(1, "fichier innexistant: ".$newsmsdonefile, -1, true);
            }

        } else {
            //$trace->endone(1, "aucun fichier disponible", 1, true);
        }

        return $rslt;

        /*}catch (\Exception $e){
            $trace = new Trace([
                'user' => "SYS",
                'module' => "SMS",
                'service' => "Update lines",
                'request_code' => "",
                'request' => "Mise à jour des traitements effectués à partir des fichiers",
            ], true);

            $trace->startnew("Erreur inattendue");
            $trace->endException($e, -1);
        }*/
    }

    /**
     * Upload et enregistre les campagnes déposées sous forme de fichier.
     */
    public function loadCampaignfile()
    {
        $user = json_decode(json_encode([
            'id' => 1,
            'name' => "sys",
        ]));

        try {
            $directory = env('SMSFILE_CAMPAIGN_DIR', '/vagrant/seegsmsftp/');
            $sep = env('SMSFILE_CAMPAIGN_SEP', '_');
            $extensions = explode(',', env('SMSFILE_CAMPAIGN_FILEEXT', 'txt'));

            $files = \File::allFiles($directory);

            foreach ($files as $file) {
                if (in_array($file->getExtension(), $extensions)) {
                    // Enregistrement du fichier
                    //$fileObj = new AppFile();
                    //$filesaved = $fileObj->storeAndSave($trace, $this->fileArray($file->getRealPath(), $file->getType(), $file->getRealPath(), false, $file->getSize()));

                    // Enregistrement de la campagne
                    $fileinfos = explode($sep, $file->getFilename());
                    $campaigntype = Campaigntype::find((int)$fileinfos[0]);

                    $campaign_data = json_decode(json_encode([
                        'user_id' => null,
                        'title' => $fileinfos[1],
                        'descript' => "",
                        'msg' => "",
                        'campaigntype_id' => $campaigntype->id,
                        //'campaign_fileid' => $filesaved[1]->id,
                    ]));
                    $campaign = $this->addNewCampaign($campaign_data, $user, $trace, [0, $this->fileArray($file->getRealPath(), $file->getType(), $file->getRealPath(), false, $file->getSize())]);

                    // Planification de la campagne
                    $campaignplanning_data = json_decode(json_encode([
                        'campaign_id' => $campaign->id,
                        'user_id' => $user->id,
                        'plan_at' => $date = $this->getNowDateTime(),
                        'receivers_fileid' => null,
                    ]));

                    $campaignplanning = $this->addNewCampaignplanning($campaignplanning_data, null, $user, $trace, true);

                    // Suppression du fichier
                    $this->deleteFile($file->getRealPath());
                } else {
                    // Mauvaise extension
                }
            }

        } catch (\Exception $e) {
            $trace = new Trace([
                'user' => $user->name,
                'module' => "Campagne",
                'service' => "Chargement fichier campagne",
                'request_code' => "",
                'request' => "Chargement fichier campagne",
            ], true);

            $trace->startnew("Erreur inattendue");
            $trace->endException($e, -1);
        }
    }

    /**
     * Télécharge un fichier de campagne exécuté
     * @param $campagnesent
     */
    public function downloadCampaignfile($campagnesent)
    {
        $campaign = Campaign::find($campagnesent->campaign_id);
        if (is_null($campaign->campaign_fileid)) {
            // Campagne sans fichier
        } else {
            $fileprefix = "";
            $campaignfile = AppFile::find($campaign->campaign_fileid);
            $originename = explode('_', $campaignfile->originename);
            $newfilename = env('SMSFILE_CAMPAIGN_DIR', '/vagrant/seegsmsftp/') . end($originename) . '_resp_' . uniqid() . '.CSV';

            // Campagne avec fichier
            $dwnld_rslt = $this->downloadFileFromCampaignPlanning($campagnesent->planning_id, $newfilename, ';');
            if (empty($dwnld_rslt['stmt'])) {
                // DOWNLOAD NON EFFECTIF
            } else {
                $this->csvTOtxt($newfilename);
            }
        }
    }

    private function csvTOtxt($filename)
    {
        $newfile = $filename . ".txt";
        $csvreader = csvreader::createFromPath($filename);
        foreach ($csvreader as $index => $row) {
            $newline = "";
            foreach ($row as $key => $line) {
                if (empty($newline)) {
                    $newline = $line;
                } else {
                    $newline = $newline . ";" . $line;
                }
            }
            $newline = $newline . "\r\n";
            $fp = fopen($newfile, 'a');
            fwrite($fp, $newline);
        }
    }

    public function csvTOtxtTest()
    {
        $newfilename = env('SMSFILE_CAMPAIGN_DIR', '/vagrant/seegsmsftp/') . 'monfulltes03_resp_57d8255c4c19d.CSV';
        $this->csvTOtxt($newfilename);
    }

    private function endSending($campagnesent)
    {
        // 1. Vérifier s'il y a encore des envois en cours pour ce planning
        $countSending = $this->countTable("campaignsendings", "id", "planning_id", $campagnesent->planning_id);
        if ($countSending['qry'] > 0) {
            // envois en cours pour ce planning
        } elseif ($countSending['qry'] == 0) {
            // plus d'envoi en cours pour ce planning
            // 2. Mise à jour des dates
            $this->startEndPlanningUpd($campagnesent->planning_id);
            // 3. Mise à zéro du nombre sending
            $this->updSimple("campaignplannings", ['stat_sending' => 0], "id", $campagnesent->planning_id);
            // 4. download le fichier resultat, le cas échéant
            $this->downloadCampaignfile($campagnesent);
        } else {
            // erreur innatendue
        }
    }
}
