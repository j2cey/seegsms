<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index()->comment('createur de la campagne');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('campaign_fileid')->unsigned()->index()->nullable()->comment('id du fichier contenant toutes les infos de la campagne (le cas échéant)');
            $table->foreign('campaign_fileid')->references('id')->on('files')->onDelete('cascade');
            $table->string('title')->comment('titre de la campagne');
            $table->string('descript')->comment('description de la campagne');
            $table->string('msg')->nullable()->comment('message de la campagne');
            $table->integer('campaigntype_id')->unsigned()->index()->comment('type de la campagne');
            $table->foreign('campaigntype_id')->references('id')->on('campaigntypes')->onDelete('cascade');
            $table->integer('status')->default(0)->comment('statut de la campagne');
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
        Schema::drop('campaigns');
    }
}
