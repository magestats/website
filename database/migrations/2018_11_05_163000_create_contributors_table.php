<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Contributors::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id');
            $table->string('node_id');
            $table->string('author');
            $table->dateTimeTz('first_contribution')->nullable();
            $table->string('name')->nullable();
            $table->integer('company')->nullable();
            $table->string('blog')->nullable();
            $table->string('location')->nullable();
            $table->string('bio')->nullable();
            $table->longText('meta');
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
        Schema::dropIfExists(\App\Contributors::TABLE);
    }
}
