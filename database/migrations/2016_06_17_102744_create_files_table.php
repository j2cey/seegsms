<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('basename')->nullable()->comment('le nom de la composante finale du chemin (extension comprise)');
            $table->string('dirname')->nullable()->comment('le chemin du dossier parent');
            $table->string('extension')->nullable()->comment('l\' extension du fichier');
            $table->string('originename')->nullable()->comment('le nom d\' origine du fichier (sans extension)');
            $table->string('type')->nullable()->comment('Le type du fichier');
            $table->string('tmp_name')->nullable()->comment('Le chemin temp du fichier');
            $table->string('error')->nullable()->comment('erreurs sur le fichier');
            $table->string('size')->nullable()->comment('la taille du fichier');

            $table->string('local_name')->nullable()->comment('le nom du fichier en local');
            $table->string('local_path')->nullable()->comment('le chemin local du fichier');

            $table->boolean('status')->default(0)->comment('Si le fichier est utilisé');
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
        Schema::drop('files');
    }
}
