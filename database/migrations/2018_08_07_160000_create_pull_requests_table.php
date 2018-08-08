<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePullRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\PullRequests::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pr_id');
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
            $table->string('milestone')->nullable();
            $table->string('milestone_url')->nullable();
            $table->dateTimeTz('created')->nullable();
            $table->dateTimeTz('updated')->nullable();
            $table->dateTimeTz('closed')->nullable();
            $table->dateTimeTz('merged')->nullable();
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
        Schema::dropIfExists(\App\PullRequests::TABLE);
    }
}
