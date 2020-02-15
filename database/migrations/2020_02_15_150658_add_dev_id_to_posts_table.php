<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDevIdToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('dev_id')->after('id');

            //1 - nome da coluna; EX.: dev_id
            //2 - nome do relacionamento (Opcional); EX.: devs_id_posts
            //3 - o campo de referencia na tabela Pai; EX.: id
            //4 - o nome da tabela; Ex.: devs
            $table->foreign('dev_id', 'devs_id_posts')->references('id')->on('devs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            // remove relacionamento
            $table->dropForeign('devs_id_posts');
            // remove coluna
            $table->dropColumn('dev_id');
        });
    }
}
