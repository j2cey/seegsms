<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsendingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaignsendings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_id')->unsigned()->index()->comment('id de la campagne');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->integer('planning_id')->unsigned()->index()->comment('id de la planification de la campagne');
            $table->foreign('planning_id')->references('id')->on('campaignplannings')->onDelete('cascade');
            $table->string('msg')->comment('message à envoyer');
            $table->string('receiver')->comment('destinataire du message');
            $table->string('receiverinfos')->nullable()->comment('infos supplementaires de destinataire');
            $table->integer('resultcode')->default(0)->comment('code du resultat');
            $table->string('resulttrace',1000)->nullable()->comment('trace de la pile d execution');
            $table->integer('nbtry')->default(0)->comment('nombre de tentatives d envoi');
            $table->string('resultstring')->nullable()->comment('texte du resultat');
            $table->integer('status')->default(0)->comment('statut du traitement: 0=en attente, 1=succès, -1=échec et en cours, -2=échec et fin');
            $table->timestamp('plan_at')->nullable()->comment('date de planification effective');
            $table->timestamp('start_at')->nullable()->comment('debut du traitement');
            $table->timestamp('end_at')->nullable()->comment('fin du traitement');
            $table->unique(['campaign_id','planning_id','receiver']);
            $table->string('pickupflag')->default('0')->comment('flag de sélection');
            $table->integer('prioritylevel')->default(0)->comment('niveau de priorité');
            $table->timestamps();
            // $table->enum('status', ['attente', 'succes', 'echec - en cours', 'echec - fin'])->default('attente')->comment('statut du traitement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('campaignsendings');
    }
}
