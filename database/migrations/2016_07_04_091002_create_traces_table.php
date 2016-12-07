<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user')->comment('utilisateur / élément à l\' origine de l\' action');
            $table->string('module')->comment('module concerné par la requête');
            $table->string('service')->comment('service du module concerné par la requête');
            $table->string('request_code')->comment('code de la requête');
            $table->string('request')->comment('intitulé de la requête');
            $table->longText('trace_report')->nullable()->comment('rapport de pile des traitements');
            $table->enum('status', ['en cours', 'execute'])->default('en cours')->comment('statut de la requête');
            $table->integer('result')->default(0)->comment('résultat de la requête');
            $table->timestamp('start_at')->comment('début effectif de la requête');
            $table->timestamp('end_at')->nullable()->comment('fin effective de la requête');
            $table->string('time')->nullable()->comment('durée globale du traitement');
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
        Schema::drop('traces');
    }
}
