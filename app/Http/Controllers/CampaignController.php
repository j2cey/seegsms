<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Campaign;
use App\Campaigntype;
use Auth;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Input;
use Validator;
use App\File as AppFile;
use Illuminate\Support\Facades\Storage;
use App\Campaignplannings;
use App\Trace;
use App\Modelvalidating;
use App\Traits\ValidationWorkflowTrait;
use DB;
use App\User;
use App\Traits\CampaignsTrait;

class CampaignController extends Controller
{
    use ValidationWorkflowTrait, CampaignsTrait;

    /*
     * GESTION des CAMPAGNES
     */

    /**
     * Renvoie toutes les campagnes du système
     * @return mixed
     */
    public function getIndex()
    {
        $campaigns = Campaign::all();

        return response()->success(compact('campaigns'));
    }

    /**
     * Renvoie toutes les campagnes du système
     * @return mixed
     */
    public function getCampaigns()
    {
        //$campaigns = Campaign::all();
        $campaigns = DB::table('campaigns_vw')
            ->get();

        return response()->success(compact('campaigns'));
    }

    /**
     * Renvoie les dernières campagnes crées
     * @return mixed
     */
    public function getLastcampaigns()
    {
        $lastcampaigns = DB::table('campaigns_vw')
            ->orderBy('campaign_id', 'desc')
            ->take(4)
            ->get();

        return response()->success(compact('lastcampaigns'));
    }

    /**
     * Renvoie une campagne
     * @param int $id ID de la campagne
     * @return mixed
     */
    public function getCampaignsShow($id)
    {
        $campaign = Campaign::find($id);
        $campaign['user'] = User::find($campaign->user_id);
        $campaign['type'] = Campaigntype::find($campaign->campaigntype_id);

        /*$campaign['plannings'] = DB::table('campaignplannings')
            ->where('campaign_id', $id)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        $campaign['plannings_drop'] = [];*/

        return response()->success($campaign);
    }

    /**
     * Modifie une campagne
     * @return mixed
     */
    public function putCampaignsShow()
    {
        $campaignForm = Input::get('data');
        $campaignData = [
            'user_id' => $campaignForm['user_id'],
            'title' => $campaignForm['title'],
            'descript' => $campaignForm['descript'],
            'msg' => $campaignForm['msg'],
            'campaigntype_id' => $campaignForm['campaigntype_id'],
            'status' => $campaignForm['status'],
        ];

        $campaignAffectedRows = Campaign::where('id', '=', intval($campaignForm['id']))->update($campaignData);

        /*$campaign = Campaign::find($campaignForm['id']);
        $plannings = $campaignForm['plannings'];

        foreach ($plannings as $plannig){
            $planningData = [
                'campaign_id' => $campaignForm['id'],
                'plan_at' => $plannig['plan_at'],
                'receivers_fileid' => $plannig['receivers_fileid'],
                'status' => $plannig['status'],
            ];

            $planningAffectedRows = Campaignplannings::where('id', '=', intval($plannig['id']))->update($planningData);
        }*/

        return response()->success('success');
    }

    /**
     * Crée une nouvelle campagne
     * @return mixed
     */
    public function postCampaigns()
    {
        // 1. recupération des données de l'utilisateur
        $userdata = json_decode(Input::get('userData'));

        // 2. création de la trace
        $trace = new Trace([
            'user' => $userdata->name,
            'module' => "Campagne",
            'service' => "Ajout",
            'request_code' => "",
            'request' => "Ajout campagne " . Input::get('title'),
        ], true);

        /*try{*/

        // 3. récupération du fichier des destinataires
        if (empty($_FILES)) {
            return response()->error('Echec transmission fichier');
        }

        if (!(isset($_FILES['receiversfile']))) {
            return response()->error('Fichier receiversfile non trouvé');
        }

        // 4. création de la campagne
        $campaigntype = json_decode(Input::get('type'));
        $campaign_descript = Input::get('descript');
        $campaign_data = [
            'user_id' => null,
            'title' => Input::get('title'),
            'descript' => (isset($campaign_descript) ? Input::get('descript') : ""),
            'campaigntype_id' => $campaigntype->id,
        ];

        // Gestion des campagnes par fichier
        $campaign_file = Input::get('campaignfile');
        if (isset($campaign_file)) {
            $campaignfile = [0, $_FILES['receiversfile']];
            $receiversfile = null;
        } else {
            $campaign_data['msg'] = Input::get('msg');
            $campaignfile = null;
            $receiversfile = [0, $_FILES['receiversfile']];
        }

        $campaign_data = json_decode(json_encode($campaign_data));
        $campaign = $this->addNewCampaign($campaign_data, $userdata, $trace, $campaignfile);

        // 5. création de la planification
        $campaignplanning_data = json_decode(json_encode([
            'campaign_id' => $campaign->id,
            'user_id' => $userdata->id,
            'plan_at' => $date = Input::get('plandate'),
            'receivers_fileid' => null,
        ]));

        $campaignplanning = $this->addNewCampaignplanning($campaignplanning_data, $receiversfile, $userdata, $trace, true);

        if ($campaignplanning[0] < 0) {
            return response()->error($campaignplanning[1]);
        }

        return response()->success(compact('campaign'));

        /*}catch (\Exception $e){
            $trace->startnew("Erreur inattendue");
            $trace->endException($e, -1);
        }*/
    }

    /**
     * Supprime une campagne
     * @param   int $id ID de la campagne
     * @return mixed
     */
    public function deleteCampaigns($id)
    {
        Campaign::destroy($id);

        return response()->success('success');
    }

    public function getCampaignsending(){
        $campaignsending_all = DB::table('plannings_vw')
            ->where('planning_statusstring', "traitement en cours")
            ->orderBy('campaign_id', 'desc')
            ->get();

        $campaignsending = [];
        $curr_heading = "";
        $last_heading = "";
        $content = [];
        $processrate_campaign = [0, 0, 0];
        foreach ($campaignsending_all as $item){
            $curr_heading = $item->campaign_title;
            $curr_content = [
                'planningId'=>$item->planning_id,
                'processrate'=>$item->processrate,
                'campaignmsg'=>$item->campaign_msg,
                'status'=>$item->planning_statusstring
            ];
            if ($curr_heading == $last_heading || $last_heading != ""){
                // In the same content
                $processrate_campaign[0] = $processrate_campaign[0] + $item->processrate;
                $processrate_campaign[1]++;
                $content[] = $curr_content;
            }else{
                // New content

                // Last campaign register:
                if ($processrate_campaign[1] > 0){
                    $processrate_campaign[2] = $processrate_campaign[0] / $processrate_campaign[1];
                }
                $campaignsending[] = [
                    'heading' => $last_heading,
                    'processrate' => $processrate_campaign[2],
                    'content' => $content,
                ];

                //
                $content = [];
                $content[] = $curr_content;
            }
            $last_heading = $curr_heading;
        }

        return response()->success(compact('campaignsending'));
    }

    /*
     * GESTION des TYPES de CAMPAGNE
     */

    /**
     * Renvoie tous les types de compagne
     * @return mixed
     */
    public function getCampaigntypes()
    {
        $campaigntypes = Campaigntype::all();

        return response()->success(compact('campaigntypes'));
    }

    /**
     * Supprime un type de campagne
     * @param   int $id ID du type de campagne
     * @return mixed
     */
    public function deleteCampaigntypes($id)
    {
        Campaigntype::destroy($id);

        return response()->success('success');
    }

    /*
     * GESTION des PLANIFICATION de CAMPAGNE
     */

    /**
     * Renvoie toutes les planifications de campagne
     * @return mixed
     */
    public function getCampaignplannings()
    {
        $campaignplannings = DB::table('plannings_vw')
            ->get();

        return response()->success(compact('campaignplannings'));
    }

    public function getCampaignplanningsShow($id)
    {
        /*$campaignplannings = DB::table('plannings_vw')
            ->where('planning_id',$id)
            ->first();*/
        $campaignplannings = Campaignplannings::find($id);
        // A FAIRE: ajouter le user id dans les vues campaign et campaignplanning
        $campaignplannings['user'] = User::find($campaignplannings->user_id);
        $campaignplannings['campaign'] = Campaign::find($campaignplannings->campaign_id);;
        $campaignplannings['stats'] = DB::table('plannings_vw')
            ->where('planning_id',$id)
            ->first();

        return response()->success($campaignplannings);
    }

    public function putCampaignplanningsShow()
    {
        /*$campaignplannings = DB::table('plannings_vw')
            ->where('planning_id',$id)
            ->first();*/
        $planningForm = Input::get('data');
        $planningData = [
            'user_id' => $planningForm['user_id'],
            'campaign_id' => $planningForm['campaign_id'],
            'plan_at' => $planningForm['plan_at'],
            'receivers_fileid' => $planningForm['receivers_fileid'],
            'status' => $planningForm['status'],
        ];

        $planningAffectedRows = Campaignplannings::where('id', '=', intval($planningForm['id']))->update($planningData);

        return response()->success('success');
    }

    public function getPlanningscampaignShow($campaignId)
    {
        $planningscampaign = DB::table('plannings_vw')
            ->where('campaign_id',$campaignId)
            ->get();

        return response()->success(compact('planningscampaign'));
    }

    /**
     * Renvoie les dernières planifications de campagne crées
     * @return mixed
     */
    public function getLastcampaignplannings()
    {
        $lastcampaignplannings = DB::table('plannings_vw')
            ->orderBy('planning_id', 'desc')
            ->take(7)
            ->get();

        return response()->success(compact('lastcampaignplannings'));
    }

    /**
     * Crée une nouvelle planification de campagne
     * @return mixed
     */
    public function postCampaignplannings()
    {

        // 1. recupération des données de l'utilisateur
        $userdata = json_decode(Input::get('user'));

        // 2.création de la trace
        $trace = new Trace([
            'user' => $userdata->name,
            'module' => "Campaignplannings",
            'service' => "Ajout",
            'request_code' => "",
            'request' => "Ajout Campaignplannings a la campagne" . Input::get('campaign_id'),
        ], true);

        // 3. récupération des données du planning
        $campaignplanning_data = json_decode(json_encode([
            'campaign_id' => Input::get('campaign_id'),
            'plan_at' => $date = Input::get('plan_at'),
            'receivers_fileid' => Input::get('receivers_fileid'),
        ]));

        if (is_null($campaignplanning_data->receivers_fileid)) {
            if (empty($_FILES)) {
                return response()->error('Echec transmission fichier');
            }

            if (!(isset($_FILES['receiversfile']))) {
                return response()->error('Fichier receiversfile non trouvé');
            }

            $receiversfile = [0, $_FILES['receiversfile']];
        } else {
            $receiversfile = [1, $campaignplanning_data->receivers_fileid];
        }

        $campaignplanning = $this->addNewCampaignplanning($campaignplanning_data, $receiversfile, $userdata, $trace, true);

        if ($campaignplanning[0] < 0) {
            return response()->error($campaignplanning[1]);
        }

        $campaignplanning = $campaignplanning[2];
        //return response()->success($campaignplanning);
        return response()->success(compact('campaignplanning'));
    }

    /**
     * Supprime une planification de campagne
     * @param   int $id ID de la planification de campagne
     * @return mixed
     */
    public function deleteCampaignplannings($deldata)
    {
        $deldataarray = json_decode($deldata);
        $planningId = $deldataarray->planningId;
        $user = $deldataarray->user;

        $this->deleteModelValidating("Campaignplannings",$planningId,$user);

        Campaignplannings::destroy($planningId);

        /*return response()->success('success');*/
        return response()->success(compact('deldataarray'));
    }

    /*
     * GESTION des VALIDATION de PLANIFICATION de CAMPAGNE
     */

    /**
     * Renvoie les plannifications à valider pour un utilisateur
     * @param   int $id ID de l'utilisateur
     * @return mixed
     */
    public function getPlanningvalidatingShow($id)
    {
        // http://localhost:8080/api/campaigns/Planningvalidating-show 4
        $model = "Campaignplannings";

        /*$user = User::find($id);
        $user['role'] = $user
            ->roles()
            ->select(['slug', 'roles.id', 'roles.name'])
            ->get();*/

        $planningvalidating = DB::table('planningvalidating_vw')
            //->where('campaign_id', 1)
            //->where('stepvalidator', 'manage.campaign')
            ->whereIn('stepvalidator', DB::table('permissions')
                ->select('slug')
                ->distinct()
                ->whereIn('id', DB::table('permission_role')
                    ->select('permission_id')
                    ->distinct()
                    ->whereIn('role_id', DB::table('role_user')
                        ->select('role_id')
                        ->distinct()
                        ->where('user_id', $id)
                    )))
            ->get();

        return response()->success(compact('planningvalidating'));
    }

    /**
     * Renvoie toutes les plannifications à valider
     * @return mixed
     */
    public function getPlanningvalidatings()
    {
        $planningvalidatings = DB::table('planningvalidating_vw')
            ->get();

        return response()->success(compact('planningvalidatings'));
    }

    /**
     * Effectue une la validation d'une planification de campagne
     * @return mixed
     */
    public function postPlanningvalidatings()
    {
        $model = "Campaignplannings";
        $validatingdata_raw = Input::get('validatingdata');
        $validatingdata = json_decode($validatingdata_raw);
        $user = User::find(Input::get('user_id'));

        foreach ($validatingdata as $item) {
            $validatingdone = $this->validateModel($model, $item->model_id, $user->name, $item->action);

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
                $this->campaignplanningChangeStatus($item->model_id, $new_status);
            }
        }

        return response()->success(compact('user', 'validatingdata_raw', 'validatingdata', 'validatingdone'));
    }


    /*
     * GESTION DES ENVOIS DE CAMPAGNE
     */

    public function getPlanningsentsShow($planningId)
    {
        $planningsents = DB::table('campaignsents')
            ->where('planning_id',$planningId)
            ->get();

        return response()->success(compact('planningsents'));
    }


    public function uploadImage(Request $request)
    {
        $galleryId = $request->input('galleryId');

        // check if the file exist
        if (!$request->hasFile('file')) {
            return response('No file sent.', 400);
        }

        // check if the file is valid file
        if (!$request->file('file')->isValid()) {
            return response('File is not valid.', 400);
        }

        // validation rules
        $validator = Validator::make($request->all(), [
            'galleryId' => 'required|integer',
            'file' => 'required|mimes:jpeg,jpg,png|max:8000',
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response('There are errors in the form data', 400);
        }

        /*$mimetype = $request->file('file')->getClientMimeType();
        $filesize = $request->file('file')->getClientSize();
        $filename = 'gallery_' . $galleryId . uniqid() . '.' . $request->file('file')->guessClientExtension();*/

        $fileObj = new File;
        $fileUpload = $fileObj->uploadThumbAndMainImage($request);
        return response($fileUpload, 201);
    }

    public function deleteSingleImage(Request $request)
    {
        $imageId = $request->input('id');

        try {
            DB::beginTransaction();

            // delete the file from the files table
            $file = File::findOrFail($imageId);
            $file->delete();

            // remove the entry from the gallery image pivot table
            DB::table('gallery_images')->where('file_id', $file->id)->delete();

            // delete the actual image from S3
            $fileName = str_replace(env('S3_URL'), '', $file->file_path);
            $fileName = explode('/', $fileName);

            $mainImage = "gallery_{$request->input('galleryId')}/main/" . $fileName[count($fileName) - 1];
            $thumbImage = "gallery_{$request->input('galleryId')}/thumb/" . $fileName[count($fileName) - 1];
            $mediummage = "gallery_{$request->input('galleryId')}/medium/" . $fileName[count($fileName) - 1];

            $s3 = Storage::disk('s3');
            $s3->delete($mainImage);
            $s3->delete($thumbImage);
            $s3->delete($mediummage);

            DB::commit();
        } catch (\PDOException $e) {
            DB::rollBack();
        }

        return response($this->show($request->input('galleryId')), 200);
    }
}
