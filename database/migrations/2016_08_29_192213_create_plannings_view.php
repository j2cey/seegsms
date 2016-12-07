<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanningsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql_stmt = "CREATE VIEW plannings_vw AS SELECT";
        $sql_stmt = $sql_stmt . " campaignplannings.id AS planning_id,campaignplannings.campaign_id,";
        $sql_stmt = $sql_stmt . " campaigns.title AS campaign_title,campaigns.msg AS campaign_msg,";
        $sql_stmt = $sql_stmt . " campaignplannings.plan_at AS toplandate,campaignplannings.plandone_at AS plandate,";
        $sql_stmt = $sql_stmt . " campaignplannings.status AS planning_status,";
        $sql_stmt = $sql_stmt . " case 	when campaignplannings.status = 1 then 'attente validation'";
        $sql_stmt = $sql_stmt . " when campaignplannings.status = 2 then 'attente date'";
        $sql_stmt = $sql_stmt . " when campaignplannings.status = 0";
        $sql_stmt = $sql_stmt . " OR (campaignplannings.status >= 3 AND campaignplannings.status < 5)  then 'traitement en cours'";
        $sql_stmt = $sql_stmt . " when campaignplannings.status < 0 then 'rejetee'";
        $sql_stmt = $sql_stmt . " when campaignplannings.status = 5 then 'executee'";
        $sql_stmt = $sql_stmt . " else 'N.D' end AS planning_statusstring,";
        $sql_stmt = $sql_stmt . " sendingstart_at AS sendingstart, sendingend_at AS sendingend,";
        $sql_stmt = $sql_stmt . " campaignplannings.stat_all AS nbplanned,campaignplannings.stat_sending AS nbsending,";
        $sql_stmt = $sql_stmt . " campaignplannings.stat_success AS nbsuccess,campaignplannings.stat_failed AS nbfailed,";
        $sql_stmt = $sql_stmt . " campaignplannings.stat_done AS nbdone,";
        $sql_stmt = $sql_stmt . " (case when campaignplannings.stat_all = 0 then 0 else (campaignplannings.stat_done::float / campaignplannings.stat_all) * 100 end) AS processrate,";
        $sql_stmt = $sql_stmt . " (case when campaignplannings.stat_done = 0 then 0 else (campaignplannings.stat_success::float / campaignplannings.stat_done) * 100 end) AS successrate,";
        $sql_stmt = $sql_stmt . " (case when campaignplannings.stat_done = 0 then 0 else (campaignplannings.stat_failed::float / campaignplannings.stat_done) * 100 end) AS failurerate";
        $sql_stmt = $sql_stmt . " FROM campaignplannings";
        $sql_stmt = $sql_stmt . " LEFT JOIN campaigns on campaigns.id = campaignplannings.campaign_id;";

        $sqlrslt = DB::connection('pgsql')->statement($sql_stmt);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql_stmt = "DROP VIEW plannings_vw;";
        $sqlrslt = DB::connection('pgsql')->statement($sql_stmt);
    }
}
