<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleModificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('has_full_modification_history')->default(false);
        });
        Schema::create('article_modifications', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('articles');

            $table->string('action');

            $table->integer('start_position')->default(0);
            $table->integer('length')->nullable();
            $table->string('content')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('article_versions', function (Blueprint $table) {
            $table->renameColumn('iteration_id', 'article_iteration_id');
        });
        Schema::rename('iterations', 'article_iterations');
        Schema::table('article_iterations', function (Blueprint $table) {
            $table->unsignedInteger('article_modification_id')->nullable();
            $table->foreign('article_modification_id')->references('id')->on('article_modifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_iterations', function (Blueprint $table) {

            $table->dropForeign('article_iterations_article_modification_id_foreign');
            $table->dropColumn('article_modification_id');
        });
        Schema::rename('article_iterations', 'iterations');
        Schema::table('article_versions', function (Blueprint $table) {
            $table->renameColumn('article_iteration_id', 'iteration_id');
        });
        Schema::dropIfExists('article_modifications');
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('has_full_modification_history');
        });
    }
}
