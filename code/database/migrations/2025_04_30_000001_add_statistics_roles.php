<?php
declare(strict_types=1);

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Class AddStatisticsRoles
 */
class AddStatisticsRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
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
        DB::table('roles')->whereIn('id', [
            Role::CONTENT_EDITOR,
            Role::SUPPORT_STAFF,
        ])->delete();
    }
} 