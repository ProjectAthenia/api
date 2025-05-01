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
    public function up(): void
    {
        // Create statistics table
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->string('relation');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create statistic filters table
        Schema::create('statistic_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statistic_id')
                ->constrained('statistics')
                ->onDelete('cascade');
            $table->string('field');
            $table->string('operator');
            $table->string('value')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create target statistics table
        Schema::create('target_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statistic_id')
                ->constrained('statistics')
                ->onDelete('cascade');
            $table->morphs('target');
            $table->json('result')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('target_statistics');
        Schema::dropIfExists('statistic_filters');
        Schema::dropIfExists('statistics');
    }
} 