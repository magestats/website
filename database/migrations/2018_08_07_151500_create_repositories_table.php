<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepositoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Repositories::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('repo_id');
            $table->string('node_id');
            $table->string('owner');
            $table->string('owner_type');
            $table->string('name');
            $table->string('full_name');
            $table->string('html_url');
            $table->string('description')->nullable();
            $table->string('homepage')->nullable();
            $table->boolean('has_issues');
            $table->boolean('has_projects');
            $table->boolean('has_downloads');
            $table->boolean('has_wiki');
            $table->integer('size');
            $table->integer('stargazers_count');
            $table->integer('watchers_count');
            $table->integer('network_count');
            $table->integer('subscribers_count');
            $table->integer('forks');
            $table->integer('open_issues');
            $table->string('default_branch');
            $table->dateTimeTz('created')->nullable();
            $table->dateTimeTz('updated')->nullable();
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
        Schema::dropIfExists(\App\Repositories::TABLE);
    }
}
