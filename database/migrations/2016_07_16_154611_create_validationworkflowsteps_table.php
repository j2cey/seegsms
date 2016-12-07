<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValidationworkflowstepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('validationworkflowsteps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('workflow_id')->unsigned()->index()->comment('id du workflow');
            $table->foreign('workflow_id')->references('id')->on('validationworkflows')->onDelete('cascade');
            $table->integer('step')->index()->comment('étape');
            $table->string('validator')->index()->comment('slug de permission pour la profile du validateur de cette étape');
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
        Schema::drop('validationworkflowsteps');
    }
}
