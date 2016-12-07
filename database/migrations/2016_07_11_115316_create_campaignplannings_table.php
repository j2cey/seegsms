<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignplanningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaignplannings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_id')->unsigned()->index()->comment('id de la campagne');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index()->comment('createur de la planification de campagne');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('plan_at')->nullable()->comment('date planifie du traitement');
            $table->integer('receivers_fileid')->unsigned()->index()->nullable()->comment('id du fichier contenant la liste des destinataires');
            $table->foreign('receivers_fileid')->references('id')->on('files')->onDelete('cascade');
            $table->integer('status')->index()->default(0)->comment('statut du traitement de la campagne: -1=rejété, 0=attente traitement, 1=attente validation, 2=attente date planif, 3=planif effective, 4=traitement en cours, 5=fin traitement');
            $table->timestamp('plandone_at')->nullable()->comment('date effective de la planification du traitement');
            $table->timestamp('sendingstart_at')->nullable()->comment('date début de l\'envoi ');
            $table->timestamp('sendingend_at')->nullable()->comment('date fin de l\'envoi ');
            $table->integer('stat_all')->default(0)->comment('nombre total des traitements à effectuer');
            $table->integer('stat_sending')->default(0)->comment('nombre de traitement en cours');
            $table->integer('stat_success')->default(0)->comment('nombre de succès');
            $table->integer('stat_failed')->default(0)->comment('nombre d\' échecs');
            $table->integer('stat_done')->default(0)->comment('nombre de traitement effectués');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('campaignplannings');
    }
}
