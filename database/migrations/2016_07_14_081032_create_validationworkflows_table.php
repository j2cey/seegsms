<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValidationworkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('validationworkflows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->index()->comment('titre du workflow de validation');
            $table->string('model')->index()->comment('modèle à valider');
            $table->integer('status')->default(1)->index()->comment('statut du workflow: 1=activé, 0=désactivé');
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
        Schema::drop('validationworkflows');
    }
}
