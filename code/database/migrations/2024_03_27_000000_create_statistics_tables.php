<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateStatisticsTables
 */
class CreateStatisticsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('public')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('statistic_filters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('statistic_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type');
            $table->json('options')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('statistic_id')->references('id')->on('statistics');
        });

        Schema::create('user_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('statistic_id');
            $table->json('filters')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('statistic_id')->references('id')->on('statistics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_statistics');
        Schema::dropIfExists('statistic_filters');
        Schema::dropIfExists('statistics');
    }
} 