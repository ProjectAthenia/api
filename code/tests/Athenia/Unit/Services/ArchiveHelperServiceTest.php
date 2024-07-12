<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services;

use App\Athenia\Services\ArchiveHelperService;
use Illuminate\Filesystem\FilesystemAdapter;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Tests\CustomMockInterface;
use Tests\TestCase;
use ZipArchive;

class ArchiveHelperServiceTest extends TestCase
{
    /**
     * @var array|(MockInterface&LegacyMockInterface)|CustomMockInterface|ZipArchive|(ZipArchive&MockInterface&LegacyMockInterface)|(ZipArchive&CustomMockInterface)
     */
    private $zipArchive;

    /**
     * @var array|FilesystemAdapter|(FilesystemAdapter&MockInterface&LegacyMockInterface)|(FilesystemAdapter&CustomMockInterface)|(MockInterface&LegacyMockInterface)|CustomMockInterface
     */
    private $filesystem;

    /**
     * @var ArchiveHelperService
     */
    private ArchiveHelperService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->zipArchive = mock(ZipArchive::class);
        $this->filesystem = mock(FilesystemAdapter::class);

        $this->service = new ArchiveHelperService($this->zipArchive);
    }

    public function testUnzipArchiveFailsWithoutArchive()
    {
        $this->filesystem->shouldReceive('path')->with('test.zip')->andReturn('/tmp/test.zip');
        $this->zipArchive->shouldReceive('open')->andReturnFalse();

        $this->expectException(\RuntimeException::class);

        $this->service->unzipArchive($this->filesystem, 'test.zip');
    }

    public function testUnzipArchiveSuccess()
    {
        $this->filesystem->shouldReceive('path')->with('test.zip')->andReturn('/tmp/test.zip');
        $this->zipArchive->shouldReceive('open')->andReturnTrue();

        $this->filesystem->shouldReceive('path')->with('test')->andReturn('/tmp/test');
        $this->zipArchive->shouldReceive('extractTo')->with('/tmp/test');

        $result = $this->service->unzipArchive($this->filesystem, 'test.zip');

        $this->assertEquals('test/', $result);
    }
}