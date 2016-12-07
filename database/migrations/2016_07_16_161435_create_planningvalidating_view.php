<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanningvalidatingView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql_stmt = "CREATE VIEW planningvalidating_vw AS SELECT";
        $sql_stmt = $sql_stmt . " modelvalidatings.id, modelvalidatings.step, modelvalidatings.status,";
        $sql_stmt = $sql_stmt . " validationworkflowsteps.validator AS stepvalidator,";
        $sql_stmt = $sql_stmt . " campaigns.id AS campaign_id, campaigntypes.id AS campaign_typeid,";
        $sql_stmt = $sql_stmt . " campaigntypes.title AS campaign_type, campaigns.title AS campaign_title,";
        $sql_stmt = $sql_stmt . " campaigns.msg AS campaign_msg, campaigns.descript AS campaign_descript,";
        $sql_stmt = $sql_stmt . " campaignplannings.id AS planning_id";
        $sql_stmt = $sql_stmt . " FROM modelvalidatings";
        $sql_stmt = $sql_stmt . " INNER JOIN validationworkflows ON modelvalidatings.model = validationworkflows.model";
        $sql_stmt = $sql_stmt . " INNER JOIN validationworkflowsteps ON modelvalidatings.model = validationworkflows.model";
        $sql_stmt = $sql_stmt . " AND validationworkflows.id = validationworkflowsteps.workflow_id";
        $sql_stmt = $sql_stmt . " AND modelvalidatings.step = validationworkflowsteps.step";
        $sql_stmt = $sql_stmt . " INNER JOIN campaignplannings ON modelvalidatings.model_id = campaignplannings.id";
        $sql_stmt = $sql_stmt . " INNER JOIN campaigns ON modelvalidatings.model_id = campaignplannings.id AND campaignplannings.campaign_id = campaigns.id";
        $sql_stmt = $sql_stmt . " INNER JOIN campaigntypes ON modelvalidatings.model_id = campaignplannings.id AND campaignplannings.campaign_id = campaigns.id AND campaigns.campaigntype_id = campaigntypes.id;";

        $sqlrslt = DB::connection('pgsql')->statement($sql_stmt);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql_stmt = "DROP VIEW planningvalidating_vw;";
        $sqlrslt = DB::connection('pgsql')->statement($sql_stmt);
    }
}
