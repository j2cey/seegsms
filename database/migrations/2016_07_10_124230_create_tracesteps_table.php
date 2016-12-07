<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracestepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracesteps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('titre de l\' étape');
            $table->string('execode')->nullable()->comment('code d\'exécution');
            $table->string('exestring')->nullable()->comment('texte d\'exécution');
            $table->integer('result')->default(0)->comment('résultat d\'excution (0: non exécuté, 1: succès, -1: échec');
            $table->timestamp('start_at')->comment('début effectif de l\' étape');
            $table->timestamp('end_at')->nullable()->comment('fin effective de l\' étape');
            $table->string('time')->nullable()->comment('durée d\'exécution converti en d h m s');
            $table->integer('trace_id')->unsigned()->index()->comment('id de la trace');
            $table->foreign('trace_id')->references('id')->on('traces')->onDelete('cascade');
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
        Schema::drop('tracesteps');
    }
}
