<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Issues::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('issue_id');
            $table->string('node_id');
            $table->string('html_url');
            $table->integer('number');
            $table->string('repo');
            $table->string('state');
            $table->string('title')->nullable();
            $table->string('author');
            $table->string('author_association');
            $table->string('labels')->nullable();
            $table->string('label_ids')->nullable();
            $table->dateTimeTz('created')->nullable();
            $table->dateTimeTz('updated')->nullable();
            $table->dateTimeTz('closed')->nullable();
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
        Schema::dropIfExists(\App\Issues::TABLE);
    }
}
