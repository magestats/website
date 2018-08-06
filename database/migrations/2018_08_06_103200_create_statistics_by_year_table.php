<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticsByYearTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Statistics\StatisticsByYear::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('year');
            $table->string('author');
            $table->integer('created');
            $table->integer('open');
            $table->integer('closed');
            $table->integer('merged');
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
        Schema::dropIfExists(\App\Statistics\StatisticsByYear::TABLE);
    }
}
