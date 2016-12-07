<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelvalidatedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modelvalidateds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model')->index()->comment('modèle validé');
            $table->integer('model_id')->unsigned()->index()->comment('id du modèle validé');
            $table->integer('step')->unsigned()->index()->comment('dernière étape validée');
            $table->integer('status')->unsigned()->default(0)->index()->comment('statut de validation: 0 = en cours, 1 = validé, -1 = rejété');
            $table->timestamp('validate_at')->nullable()->comment('date de validation');
            $table->string('validationsteps',1000)->nullable()->comment('détail des étapes de validation');
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
        Schema::drop('modelvalidateds');
    }
}
