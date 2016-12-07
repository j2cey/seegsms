<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelvalidatingstepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modelvalidatingsteps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('validating_id')->unsigned()->index()->comment('id de la validation en cours');
            $table->foreign('validating_id')->references('id')->on('modelvalidatings')->onDelete('cascade');
            $table->integer('step')->index()->comment('étape');
            $table->string('user')->index()->comment('utilisateur');
            $table->integer('action')->index()->comment('action effectuée: 1 = validé, -1 = rejété');
            $table->timestamp('validate_at')->nullable()->comment('date de validation');
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
        Schema::drop('modelvalidatingsteps');
    }
}
