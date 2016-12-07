<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaigntypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigntypes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('titre du type de campagne');
            $table->string('descript')->comment('description du type de campagne');
            $table->integer('prioritylevel')->comment('niveau de priorité du type de campagne');
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
        Schema::drop('campaigntypes');
    }
}
