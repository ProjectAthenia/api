<?php
declare(strict_types=1);

use App\Models\Statistic\Statistic;
use App\Models\Statistic\StatisticFilter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class SetupArticleNotes
 */
class SetupArticleNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Create article_notes table
        Schema::create('article_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('article_id');
            $table->timestamp('completed_at')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('article_id')
                ->references('id')
                ->on('articles')
                ->onDelete('cascade');

            $table->unique(['user_id', 'article_id']);
        });

        // Add url and authors fields to articles table
        Schema::table('articles', function (Blueprint $table) {
            $table->string('url')->nullable()->after('title');
            $table->text('authors')->nullable()->after('url');
        });

        // Create the total_notes statistic
        $totalNotesStatistic = Statistic::create([
            'name' => 'total_notes',
            'model' => 'article',
            'relation' => 'articleNotes',
            'public' => true,
        ]);

        // Create the total_completed_notes statistic with a filter for completed notes
        $totalCompletedNotesStatistic = Statistic::create([
            'name' => 'total_completed_notes',
            'model' => 'article',
            'relation' => 'articleNotes',
            'public' => true,
        ]);

        // Add filter to only count notes where completed_at is not null
        StatisticFilter::create([
            'statistic_id' => $totalCompletedNotesStatistic->id,
            'field' => 'completed_at',
            'operator' => '!=',
            'value' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Delete statistics
        $statistics = Statistic::whereIn('name', ['total_notes', 'total_completed_notes'])->get();

        foreach ($statistics as $statistic) {
            // Delete associated filters (cascade delete should handle this, but being explicit)
            $statistic->filters()->delete();
            $statistic->delete();
        }

        // Drop url and authors columns from articles
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['url', 'authors']);
        });

        // Drop article_notes table
        Schema::dropIfExists('article_notes');
    }
}
