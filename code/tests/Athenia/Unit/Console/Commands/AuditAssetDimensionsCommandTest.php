<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Console\Commands;

use App\Athenia\Console\Commands\AuditAssetDimensionsCommand;
use App\Athenia\Contracts\Repositories\AssetRepositoryContract;
use App\Models\Asset;
use Illuminate\Contracts\Bus\Dispatcher;
use Tests\TestCase;

class AuditAssetDimensionsCommandTest extends TestCase
{
    public function testHandle()
    {
        $dispatcher = mock(Dispatcher::class);
        $assetRepository = mock(AssetRepositoryContract::class);

        $command = new AuditAssetDimensionsCommand($dispatcher, $assetRepository);

        $assetRepository->shouldReceive('findAll')->andReturn(collect([
            new Asset(),
            new Asset(),
            new Asset(),
        ]));

        $dispatcher->shouldReceive('dispatch')->times(3);

        $command->handle();
    }
}