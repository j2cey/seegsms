<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskqueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taskqueues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('taskcode')->index()->comment('code unique de la tache');
            $table->string('taskuid')->index()->comment('uid de la tache');
            $table->string('taskdesc')->nullable()->comment('description de la tache de la tache');
            $table->string('pickupuid')->default('0')->index()->comment('uid du process qui utilise la tache actuellement');
            $table->timestamp('pickup_at')->nullable()->comment('date de récupération de la tache');
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
        Schema::drop('taskqueues');
    }
}
