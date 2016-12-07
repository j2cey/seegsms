<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;

class DashboardController extends Controller
{
    /**
     * Renvoie les planifications en attente de validation
     * @return mixed
     */
    public function getPlanningvalidatings()
    {
        $planningvalidatings = DB::table('planningvalidating_vw')
            ->get();

        return response()->success(compact('planningvalidatings'));
    }

    /**
     * Renvoie les planifications en cours de traitement
     * @return mixed
     */
    public function getPlanningrunnings()
    {
        $planningrunnings = DB::table('plannings_vw')
            ->where('planning_statusstring', 'traitement en cours')
            ->get();

        return response()->success(compact('planningrunnings'));
    }

    public function getPlanningfailed()
    {
        $planningdoneandfailed = DB::table('plannings_vw')
            ->select(DB::raw('SUM(nbdone) as nbdone'),DB::raw('SUM(nbfailed) as nbfailed'))
            ->first();

        $planningfailed = [];

        list($planningfailed['nbdone'], $planningfailed['nbfailed'], $planningfailed['pctgfailed']) = [
            $planningdoneandfailed->nbdone,
            $planningdoneandfailed->nbfailed,
            ($planningdoneandfailed->nbdone == 0 ? 0 : ($planningdoneandfailed->nbfailed / $planningdoneandfailed->nbdone))
        ];

        $planningfailed = [$planningfailed];

        return response()->success(compact('planningfailed'));
    }
}
