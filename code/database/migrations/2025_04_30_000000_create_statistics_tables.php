<?php
declare(strict_types=1);

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->boolean('public')->default(false);
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
            $table->float('value')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Add Content Editor role
        DB::table('roles')->insert([
            'id' => Role::CONTENT_EDITOR,
            'name' => 'Content Editor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add Support Staff role
        DB::table('roles')->insert([
            'id' => Role::SUPPORT_STAFF,
            'name' => 'Support Staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Remove roles
        DB::table('roles')->whereIn('id', [
            Role::CONTENT_EDITOR,
            Role::SUPPORT_STAFF,
        ])->delete();

        Schema::dropIfExists('target_statistics');
        Schema::dropIfExists('statistic_filters');
        Schema::dropIfExists('statistics');
    }
} 