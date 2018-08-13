<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRepositoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(\App\Repositories::TABLE, function(Blueprint $table) {
            $table->dateTimeTz('created')->nullable();
            $table->dateTimeTz('updated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(\App\Repositories::TABLE, function(Blueprint $table) {
            $table->dropColumn('created');
            $table->dropColumn('updated');
        });
    }
}
