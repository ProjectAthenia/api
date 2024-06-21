<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Asset;

use App\Athenia\Services\Asset\AssetConfigurationService;
use Tests\TestCase;

class AssetConfigurationServiceTest extends TestCase
{
    public function testGetServerUrl()
    {
        $service = new AssetConfigurationService(
            'http://hello.bye',
            'assets',
        );

        $this->assertEquals('http://hello.bye', $service->getServerUrl());
    }

    public function testGetBaseAssetDirectory()
    {
        $service = new AssetConfigurationService(
            'http://hello.bye',
            'assets',
        );

        $this->assertEquals('assets', $service->getBaseAssetDirectory());
    }
}