<?php
/**
 * Created by PhpStorm.
 * User: JudeParfait
 * Date: 10/07/2016
 * Time: 18:40
 */

namespace App\Traits;

use DB;
use App\Traits\DateUtilitiesTrait;

trait SqlTrait
{
    use DateUtilitiesTrait;

    private function copyFlatFileToTable($file, $table, $delimiter)
    {
        // COPY myTable FROM value.txt (DELIMITER('|'));
        if (empty($delimiter) || is_null($delimiter)) {
            $stmt = "COPY " . $table . " FROM '" . $file . "' ;";//(DELIMITER('".$delimiter."'))
        } else {
            $stmt = "COPY " . $table . " FROM '" . $file . "' (DELIMITER('" . $delimiter . "'));";
        }

        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function exportToFlatFile($file, $table, $delimiter)
    {
        //$stmt = "COPY (SELECT * FROM ".$table.") TO '".$file."' WITH CSV DELIMITER ','";
        $stmt = "COPY (SELECT * FROM " . $table . ") TO '" . $file . "' WITH DELIMITER '" . $delimiter . "'";
        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function downloadFileFromCampaignPlanning($planningId, $newfilename, $delimiter){
        /*$countSending = DB::table("campaignsendings")
            ->where('planning_id', $planningId)
            ->count('id');*/
        $countSending = $this->countTable("campaignsendings","id","planning_id",$planningId);
        if ($countSending['qry'] > 0){
            return $this->sqlResult("", false);
        }
        $stmt = "COPY (Select msg,receiver,CASE status WHEN 1 THEN 'O' ELSE 'N' END,end_at,nbtry,resultstring From campaignsents Where planning_id = " . $planningId . ") TO '" . $newfilename . "' WITH DELIMITER '" . $delimiter . "'";
        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function countTable($table, $column, $where_col = null, $where_val = null)
    {
        /*return DB::table($table)
            ->select(DB::raw('count('.$column.') as tab_count'))
            ->get();*/

        if (is_null($where_col)){
            $stmt = "";
            $qry = DB::table($table)->count($column);
        }else{
            $stmt = "";
            $qry = DB::table($table)
                ->where($where_col, $where_val)
                ->count($column);
        }
        return $this->sqlResult($stmt, $qry);
    }

    private function startEndPlanningUpd($planningId){
        $minstartdate = DB::table('campaignsents')->min('start_at');
        $maxenddate = DB::table('campaignsents')->max('end_at');

        return $this->sqlResult("", DB::table('campaignplannings')
            ->where('id', $planningId)
            ->update(['sendingstart_at' => $minstartdate, 'sendingend_at' => $maxenddate]));
    }

    private function createTableFromTable($oldtable, $newtable)
    {
        //$db_rslt = $this->execRaw("SELECT * INTO ".$newtable." FROM ".$oldtable.";");
        //$db_rslt = $this->execRaw("SELECT * INTO ".$newtable." FROM ".$oldtable.";");
        //$db_rslt = $this->execRaw("ALTER TABLE ".$newtable." ADD PRIMARY KEY (id)");

        $stmt = "CREATE TABLE " . $newtable . "( id serial primary key, mobile VARCHAR(255) not null);";
        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function createNewTable($newtable, $fieldlist)
    {
        $stmt = "CREATE TABLE " . $newtable . "( id serial primary key";

        foreach ($fieldlist as $fieldinfos) {
            $stmt = $stmt . "," . $fieldinfos;
        }
        $stmt = $stmt . ");";

        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function dropTable($table)
    {
        $stmt = "DROP TABLE " . $table . ";";
        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function getDoublonsOneColumn($table, $column)
    {
        //$stmt = "SELECT COUNT(".$column.") AS nbr_doublon, ".$column." FROM ".$table." GROUP BY ".$column." HAVING COUNT(".$column.") > 1";
        return $this->sqlResult("", DB::table($table)->select(DB::raw('' . $column . ', count(' . $column . ') as nbr_doublon'))
            ->groupBy($column)
            ->havingRaw('COUNT(' . $column . ') > 1')
            ->get()
        );
    }

    private function getDoublonsManyColumns($table, $columns)
    {
        //$stmt = "SELECT COUNT(".$column.") AS nbr_doublon, ".$column." FROM ".$table." GROUP BY ".$column." HAVING COUNT(".$column.") > 1";
        $columnlist_pipe = $this->getColumnlist($columns, "||");
        $columnlist_comma = $this->getColumnlist($columns, ",");
        return $this->sqlResult("", DB::table($table)
            ->select(DB::raw('' . $columnlist_comma . ', count(' . $columnlist_pipe . ') as nbr_doublon'))
            ->groupBy($columns)
            ->havingRaw('COUNT(' . $columnlist_pipe . ') > 1')
            ->get()
        );
    }

    private function deleteDoublonsManyColumn($table, $columns)
    {

        $columnlist = $this->getColumnlist($columns, ",");
        return $this->sqlResult("", DB::table($table)
            ->whereNotIn('id', DB::table($table)
                ->select(DB::raw('MAX(id)'))
                ->groupBy($columns))
            ->delete()
        );
    }

    private function deleteDoublonsOneColumn($table, $column)
    {
        /*$stmt = "DELETE FROM ".$table." LEFT OUTER JOIN ";
        $stmt = $stmt . "( SELECT MIN(id) as id, ".$column." FROM ".$table." GROUP BY ".$column." ) as t1 ";
        $stmt = $stmt . "ON ".$table.".id = t1.id WHERE t1.id IS NULL";*/

        /*$stmt = "DELETE FROM ".$table." WHERE id NOT IN ";
        $stmt = $stmt . "( SELECT max(id) FROM ".$table." GROUP BY ".$column." );";

        return $this->execRaw($stmt);*/

        return $this->sqlResult("", DB::table($table)
            ->whereNotIn('id', DB::table($table)
                ->select(DB::raw('MAX(id)'))
                ->groupBy($column))
            ->delete()
        );
    }

    private function deleteMobileFalse($table, $column)
    {
        return $this->sqlResult("", DB::table($table)
            ->where(DB::raw('LENGTH(' . $column . ')'), '<>', 8)
            ->where(DB::raw('LENGTH(' . $column . ')'), '<>', 11)
            ->orWhere(DB::raw('isnumeric(' . $column . ')'), '<>', true)
            ->delete()
        );
    }

    private function getMobileFalse($table, $column)
    {
        return $this->sqlResult("", DB::table($table)
            ->where(DB::raw('LENGTH(' . $column . ')'), '<>', 8)
            ->where(DB::raw('LENGTH(' . $column . ')'), '<>', 11)
            ->orWhere(DB::raw('isnumeric(' . $column . ')'), '<>', true)
            ->get()
        );
    }

    private function addIndicatifMobile($table)
    {
        $stmt = "UPDATE " . $table . " SET mobile = '241' || mobile WHERE LENGTH(mobile) = 8;";
        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function updFromOneColumn($table_from, $col_from_eq, $col_from_upd, $table_to, $col_to_eq, $col_to_upd)
    {
        /*
            UPDATE receivers_5783ce65595d8 AS t1
            SET receiver_id = t2.id
            FROM receivers AS t2
            WHERE t1.mobile = t2.mobile;
        */
        $stmt = "UPDATE " . $table_to . " AS t1";
        $stmt = $stmt . " SET " . $col_to_upd . " = t2." . $col_from_upd;
        $stmt = $stmt . " FROM " . $table_from . " AS t2";
        $stmt = $stmt . " WHERE t1." . $col_to_eq . " = t2." . $col_from_eq . ";";

        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function addReceivers($receivers_tmp)
    {
        // insert into receivers(mobile) select mobile from receivers_5783ce65595d8;
        $stmt = "INSERT INTO receivers(mobile) SELECT ";
        $stmt = $stmt . "mobile FROM " . $receivers_tmp . " ";
        $stmt = $stmt . "WHERE receiver_id IS NULL;";

        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function planReceivers($receivers_tmp, $planning_id)
    {
        // insert into planningreceivers(planning_id,receiver_id) select 1, receiver_id from receivers_5784b5e38962d where receiver_id is not null  ON DUPLICATE KEY UPDATE planningreceivers.id=planningreceivers.id;

        $stmt = "INSERT INTO planningreceivers(planning_id,receiver_id) SELECT " . $planning_id . ", ";
        $stmt = $stmt . "receiver_id FROM " . $receivers_tmp . " ";
        $stmt = $stmt . "WHERE receiver_id IS NOT NULL;";// ON DUPLICATE KEY UPDATE planningreceivers.id=planningreceivers.id;";

        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function delReceiversPlanned($receivers_tmp)
    {

        // insert into planningreceivers(planning_id,receiver_id) select 1, receiver_id from receivers_5784b5e38962d where receiver_id is not null;
        return $this->sqlResult("", DB::table($receivers_tmp)->whereNotNull('receiver_id')->delete());
    }

    private function sendPlan($planning_id, $campaign_id, $msg, $prioritylevel)
    {
        // insert into planningreceivers(planning_id,receiver_qid) select 1, receiver_id from receivers_5784b5e38962d where receiver_id is not null  ON DUPLICATE KEY UPDATE planningreceivers.id=planningreceivers.id;

        $stmt = "INSERT INTO campaignsendings(campaign_id,planning_id,msg,prioritylevel,receiver) SELECT DISTINCT " . $campaign_id . ", " . $planning_id . ", '" . $msg . "', " . $prioritylevel . ", ";
        $stmt = $stmt . "mobile FROM receivers ";
        $stmt = $stmt . "INNER JOIN planningreceivers ON receivers.id = planningreceivers.receiver_id AND planningreceivers.planning_id = " . $planning_id . ";";

        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function freezLines($table, $uid, $limit)
    {

        $freez_rslt = DB::table($table)
            ->whereIn('id', DB::table($table)
                ->select('id')
                ->where('pickupflag', '0')
                ->orderBy('prioritylevel', 'desc')
                ->take($limit)
            )
            ->update(['pickupflag' => $uid]);

        return $this->sqlResult(
            "",
            $freez_rslt
        );
    }


    private function updPlanningStatsByPickupflagStatus($pickupflag, $status, $tablefrom, $updfiled, $incrementvalue, $increment = true)
    {

        if ($increment) {
            if (is_null($status)) {
                $upd_rslt = DB::table("campaignplannings")
                    ->whereIn('id', DB::table($tablefrom)
                        ->select('id')
                        ->where('pickupflag', $pickupflag)
                    )
                    ->increment($updfiled, $incrementvalue);
            } else {
                $upd_rslt = DB::table("campaignplannings")
                    ->whereIn('id', DB::table($tablefrom)
                        ->select('id')
                        ->where('pickupflag', $pickupflag)
                        ->where('status', $status)
                    )
                    ->increment($updfiled, $incrementvalue);
            }
        } else {
            if (is_null($status)) {
                $upd_rslt = DB::table("campaignplannings")
                    ->whereIn('id', DB::table($tablefrom)
                        ->select('id')
                        ->where('pickupflag', $pickupflag)
                    )
                    ->decrement($updfiled, $incrementvalue);
            } else {
                $upd_rslt = DB::table("campaignplannings")
                    ->whereIn('id', DB::table($tablefrom)
                        ->select('id')
                        ->where('pickupflag', $pickupflag)
                        ->where('status', $status)
                    )
                    ->decrement($updfiled, $incrementvalue);
            }
        }

        return $this->sqlResult(
            "",
            $upd_rslt
        );
    }

    private function updPlanningStatsByCountId($pickupflag, $status, $tablefrom, $updfiled, $increment = true)
    {

        if (is_null($status)) {
            $idlist = DB::table($tablefrom)
                ->select('planning_id')
                ->distinct()
                ->where('pickupflag', $pickupflag)
                ->get();
        } else {
            $idlist = DB::table($tablefrom)
                ->select('planning_id')
                ->distinct()
                ->where('pickupflag', $pickupflag)
                ->where('status', $status)
                ->get();
        }

        $id_n_count = [];

        foreach ($idlist as $id) {

            $id_n_count[] = [$id->planning_id, DB::table($tablefrom)
                ->where('pickupflag', $pickupflag)
                ->where('planning_id', $id->planning_id)
                ->count('planning_id')];
        }

        $upd_rslt = 0;
        if ($increment) {
            foreach ($id_n_count as $item) {
                $upd_rslt = $this->incrementPlanningStats($item[0], $updfiled, $item[1]);
            }
        } else {
            foreach ($id_n_count as $item) {
                $upd_rslt = $this->decrementPlanningStats($item[0], $updfiled, $item[1]);
            }
        }

        return $this->sqlResult(
            "",
            $upd_rslt
        );
    }

    private function incrementPlanningStats($id, $updfiled, $incrementvalue)
    {

        $upd_rslt = DB::table("campaignplannings")
            ->where('id', $id)
            ->increment($updfiled, $incrementvalue);

        return $this->sqlResult(
            "",
            $upd_rslt
        );
    }

    private function decrementPlanningStats($id, $updfiled, $incrementvalue)
    {

        $upd_rslt = DB::table("campaignplannings")
            ->where('id', $id)
            ->decrement($updfiled, $incrementvalue);

        return $this->sqlResult(
            "",
            $upd_rslt
        );
    }


    private function changePlanningStatusFromPickupflag($pickupflag)
    {

        // Liste des IDs des plannings en cours de traitement pour ce pickupflag
        $running_ids = DB::table("campaignsendings")
            ->where('pickupflag', $pickupflag)
            ->distinct()->select('planning_id');

        // Liste des IDs des plannings traités pour ce pickupflag
        $done_ids = DB::table("campaignsents")
            ->where('pickupflag', $pickupflag)
            ->distinct()->select('planning_id');

        // Liste des IDs des plannings en cours de traitement parmi les plannings traités pour ce pickupflag
        $waiting_done_ids = DB::table("campaignsendings")
            ->whereIn('id', $done_ids)
            ->distinct()->select('planning_id');

        // Traitement en cours
        $nb_running = DB::table("campaignplannings")
            ->whereIn('id', $running_ids)
            ->update(['status' => 4]);

        // Fin Traitement
        $nb_done = DB::table("campaignplannings")
            ->whereIn('id', $done_ids)
            ->whereNotIn('id', $waiting_done_ids)
            ->update(['status' => 5]);

        return [$nb_running, $nb_done];
    }


    private function updSimple($table, $updvalues, $where_col = null, $where_val = null)
    {
        if (is_null($where_col)){
            $qry = DB::table($table)
                ->update($updvalues);
        }else{
            $qry = DB::table($table)
                ->where($where_col, $where_val)
                ->update($updvalues);
        }

        return $this->sqlResult("", $qry);
    }

    private function insFromTable($tablefrom, $tableto, $columns)
    {

        $columnlist = $this->getColumnlist($columns, ",");

        $stmt = "INSERT INTO " . $tableto . "(" . $columnlist . ") ";
        $stmt = $stmt . "SELECT DISTINCT " . $columnlist . "";
        $stmt = $stmt . " FROM " . $tablefrom;

        return $this->sqlResult($stmt, $this->execRaw($stmt));
    }

    private function truncTable($table)
    {
        //TRUNCATE TABLE campaignsendingtemps;
        return $this->sqlResult(
            "",
            DB::table($table)
                ->truncate()
        );
    }

    private function getColumnlist($columns, $separator)
    {
        $columnlist = "";
        foreach ($columns as $column) {
            if (empty($columnlist)) {
                $columnlist = $column;
            } else {
                $columnlist = $columnlist . $separator . $column;
            }
        }

        return $columnlist;
    }

    private function execRaw($sql_stmt, $connection = "default")
    {
        $connection = (($connection == "default") ? "pgsql" : $connection);
        return DB::connection($connection)->statement($sql_stmt);
    }

    private function sqlResult($stmt, $qry)
    {
        return ['stmt' => $stmt, 'qry' => $qry];
    }
}
