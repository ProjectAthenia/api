<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Services\Asset;

use App\Athenia\Contracts\Repositories\AssetRepositoryContract;
use App\Athenia\Services\Asset\AssetImportService;
use App\Models\Asset;
use App\Models\User\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\CustomMockInterface;
use Tests\TestCase;

class AssetImportServiceTest extends TestCase
{
    /**
     * @var AssetRepositoryContract|(AssetRepositoryContract&MockInterface&LegacyMockInterface)|(AssetRepositoryContract&CustomMockInterface)|array|(MockInterface&LegacyMockInterface)|CustomMockInterface
     */
    private $assetRepository;

    /**
     * @var array|Client|(Client&MockInterface&LegacyMockInterface)|(Client&CustomMockInterface)|(MockInterface&LegacyMockInterface)|CustomMockInterface
     */
    private $client;

    /**
     * @var AssetImportService
     */
    private AssetImportService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assetRepository = mock(AssetRepositoryContract::class);
        $this->client = mock(Client::class);

        $this->service = new AssetImportService($this->assetRepository, $this->client);
    }

    public function testImportAssetFailsWithInvalidPath()
    {
        $result = $this->service->importAsset(new User(), "hello my friend");

        $this->assertNull($result);
    }

    public function testImportAssetFailsWithHTTPException()
    {
        $this->client->shouldReceive('get')->andThrow(mock(ClientException::class));

        $result = $this->service->importAsset(new User(), "https://www.hello.bye/greetings.jpg");

        $this->assertNull($result);
    }

    public function testImportAssetFailsWithInvalidStatus()
    {
        $response = mock(ResponseInterface::class);

        $this->client->shouldReceive('get')->andReturn($response);

        $response->shouldReceive('getStatusCode')->andReturn(404);

        $result = $this->service->importAsset(new User(), "https://www.hello.bye/greetings.jpg");

        $this->assertNull($result);
    }

    public function testImportAssetSuccess()
    {
        $asset = new Asset();

        $this->assetRepository->shouldReceive('create')->andReturn($asset);

        $response = mock(ResponseInterface::class);

        $this->client->shouldReceive('get')->andReturn($response);

        $response->shouldReceive('getStatusCode')->andReturn(200);

        $body = mock(StreamInterface::class);
        $response->shouldReceive('getBody')->andReturn($body);

        $body->shouldReceive('getContents')->andReturn('');

        $result = $this->service->importAsset(new User(), "https://www.hello.bye/greetings.jpg");

        $this->assertEquals($result, $asset);
    }
}