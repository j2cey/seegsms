<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql_stmt = "CREATE VIEW campaigns_vw AS SELECT";
        $sql_stmt = $sql_stmt . " campaigns.id AS campaign_id,campaigns.title AS campaign_title,";
        $sql_stmt = $sql_stmt . " campaigns.descript AS campaign_descript,campaigns.campaigntype_id AS campaigntype_id,";
        $sql_stmt = $sql_stmt . " campaigns.msg AS campaign_msg,campaigns.status AS campaign_status,campaigntypes.title AS campaigntype,";
        $sql_stmt = $sql_stmt . " (SELECT COUNT(campaignplannings.id) FROM campaignplannings WHERE campaignplannings.campaign_id = campaigns.id";
        $sql_stmt = $sql_stmt . " AND campaignplannings.status = 1) AS plansvalidating,";
        $sql_stmt = $sql_stmt . " (SELECT COUNT(campaignplannings.id) FROM campaignplannings WHERE campaignplannings.campaign_id = campaigns.id";
        $sql_stmt = $sql_stmt . " AND campaignplannings.status = 2) AS planswaitingdate,";
        $sql_stmt = $sql_stmt . " (SELECT COUNT(campaignplannings.id) FROM campaignplannings WHERE campaignplannings.campaign_id = campaigns.id";
        $sql_stmt = $sql_stmt . " AND campaignplannings.status >= 3 AND campaignplannings.status < 5) AS plansrunning,";
        $sql_stmt = $sql_stmt . " (SELECT COUNT(campaignplannings.id) FROM campaignplannings WHERE campaignplannings.campaign_id = campaigns.id";
        $sql_stmt = $sql_stmt . " AND campaignplannings.status < 0) AS plansrejected,";
        $sql_stmt = $sql_stmt . " (SELECT COUNT(campaignplannings.id) FROM campaignplannings WHERE campaignplannings.campaign_id = campaigns.id";
        $sql_stmt = $sql_stmt . " AND campaignplannings.status = 5) AS plansdone";
        $sql_stmt = $sql_stmt . " FROM campaigns";
        $sql_stmt = $sql_stmt . " INNER JOIN campaigntypes ON campaigns.campaigntype_id = campaigntypes.id";
        $sql_stmt = $sql_stmt . " GROUP BY campaigns.id,campaigntypes.title";

        $sqlrslt = DB::connection('pgsql')->statement($sql_stmt);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql_stmt = "DROP VIEW campaigns_vw;";
        $sqlrslt = DB::connection('pgsql')->statement($sql_stmt);
    }
}
