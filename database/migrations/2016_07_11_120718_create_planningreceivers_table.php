<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanningreceiversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planningreceivers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('planning_id')->unsigned()->index()->comment('id de la planification');
            $table->foreign('planning_id')->references('id')->on('campaignplannings')->onDelete('cascade');
            $table->integer('receiver_id')->unsigned()->index()->comment('id du destinataire');
            $table->foreign('receiver_id')->references('id')->on('receivers')->onDelete('cascade');
            $table->unique(['planning_id','receiver_id']);
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
        Schema::drop('planningreceivers');
    }
}
