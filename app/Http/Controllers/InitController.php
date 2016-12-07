<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use App\Campaigntype;
use DB;
use App\User;
use App\Campaign;
use App\Commands\SMS\Sms;

class InitController extends Controller
{
    public function test()
    {
        /*$seegsmsconfigs = Config::get('seegsms.campaignfile');
        $campaigntypes = Campaigntype::all();
        $campaigns = DB::table('campaigns_vw')
            ->orderBy('campaign_id', 'desc')
            ->take(4)
            ->get();
        dd($seegsmsconfigs, $campaigntypes, $campaigns);

        $err['error'] = false;

        return response()->success($err, compact('campaigns'));*/

        /*$planningdoneandfailed = DB::table('plannings_vw')
            ->select(DB::raw('SUM(nbdone) as nbdone'), DB::raw('SUM(nbfailed) as nbfailed'))
            ->first();

        $planningfailed = [];

        list($planningfailed['nbdone'], $planningfailed['nbfailed'], $planningfailed['pctgfailed']) = [
            $planningdoneandfailed->nbdone,
            $planningdoneandfailed->nbfailed,
            ($planningdoneandfailed->nbdone == 0 ? 0 : ($planningdoneandfailed->nbfailed / $planningdoneandfailed->nbdone))
        ];

        return response()->success(compact('planningfailed'));*/

        /*$campaignplannings = DB::table('plannings_vw')
            ->where('planning_id',1)
            ->get();
        // A FAIRE: ajouter le user id dans les vues campaign et campaignplanning
        $campaignplannings = $campaignplannings[0];
        //$campaignplannings['user'] = User::find(2);

        dd($campaignplannings);

        return response()->success($campaignplannings);*/

        //$campaign = Campaign::find(1);

        //dd($campaign);

        $campaignsending_all = DB::table('plannings_vw')
            //->where('planning_statusstring', "traitement en cours")
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

            $samecontent = ($curr_heading == $last_heading || $last_heading == "");

            if ($samecontent){
                // In the same content
                $processrate_campaign[0] = $processrate_campaign[0] + $item->processrate;
                $processrate_campaign[1]++;
                $content[] = $curr_content;

                $last_heading = $curr_heading;
            }else{
                // New content
            }

            if ( (! $samecontent) || (!(next($campaignsending_all)==true))){
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
            }else{
                // Nothing to register
            }
        }

        dd($campaignsending_all);

        return response()->success($campaignsending_all);
    }

    public function testsms(){
        $sms = new Sms();
        $smssender = "SEEG";

        $msg = "sms seeg test send";

        $to = "24105300354";

        $send_rslt = $sms->send($msg,$smssender,$to);

        dd($send_rslt);
    }
}
