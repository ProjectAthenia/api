<?php
declare(strict_types=1);

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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop url and authors columns from articles
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['url', 'authors']);
        });

        // Drop article_notes table
        Schema::dropIfExists('article_notes');
    }
}
