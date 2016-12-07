<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelvalidatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modelvalidatings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model')->index()->comment('modèle en cours de validation');
            $table->integer('model_id')->unsigned()->index()->comment('id du modèle en cours de validation');
            $table->integer('step')->unsigned()->default(1)->index()->comment('dernière étape validée');
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
        Schema::drop('modelvalidatings');
    }
}
