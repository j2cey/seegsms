<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mobile')->comment('numéro mobile du destinataire');
            $table->string('location')->nullable()->comment('localisation du destinataire');
            $table->string('mobile2')->nullable()->comment('numéro mobile 2 du destinataire');
            $table->string('mobile3')->nullable()->comment('numéro mobile 3 du destinataire');
            $table->string('mobile4')->nullable()->comment('numéro mobile 4 du destinataire');
            $table->string('mobile5')->nullable()->comment('numéro mobile 5 du destinataire');
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
        Schema::drop('receivers');
    }
}
