<?php
declare(strict_types=1);

namespace App\Athenia\Console\Commands;

use App\Athenia\Contracts\Repositories\AssetRepositoryContract;
use App\Athenia\Jobs\CalculateAssetDimensionsJob;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;

class AuditAssetDimensionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:audit-dimensions';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'Audit Asset Dimensions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all assets to make sure that the saved width and height are the most up to date data';

    /**
     * @param Dispatcher $dispatcher
     * @param AssetRepositoryContract $assetRepository
     */
    public function __construct(
        private Dispatcher $dispatcher,
        private AssetRepositoryContract $assetRepository,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->assetRepository->findAll([], [], [], [], null) as $asset) {
            $this->dispatcher->dispatch(new CalculateAssetDimensionsJob($asset));
        }
    }
}