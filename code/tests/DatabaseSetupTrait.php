<?php
/**
 * Do our custom database setup
 */
declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class DatabaseSetupTrait
 * @package App\Tests
 */
trait DatabaseSetupTrait
{
    use DatabaseTransactions;

    /**
     * Set up the database after you've done all the parent setup stuff
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    /**
     * If migrations haven't ran (which is the first test), run them
     */
    protected function setupDatabase()
    {
        if (! TestHelper::$migrationsRan) {
            TestHelper::$migrationsRan = true;

            if (env('SKIP_MIGRATION')) {
                fwrite(STDOUT, 'Skipping requested db migration.' . PHP_EOL);
            }
            else {
                fwrite(STDOUT, 'Beginning db migration reload...');
                $this->artisan('migrate:fresh');
                fwrite(STDOUT, 'Done.' . PHP_EOL);
                $this->app[Kernel::class]->setArtisan(null);
            }
        }
        $this->beginDatabaseTransaction();
    }
}