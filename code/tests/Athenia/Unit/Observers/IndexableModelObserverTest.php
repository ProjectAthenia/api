<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Observers;

use App\Athenia\Contracts\Repositories\ResourceRepositoryContract;
use App\Athenia\Observer\IndexableModelObserver;
use App\Models\Resource;
use App\Models\User\User;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class IndexableModelObserverTest
 * @package Tests\Athenia\Unit\Observers
 */
final class IndexableModelObserverTest extends TestCase
{
    /**
     * @var IndexableModelObserver
     */
    private $observer;

    /**
     * @var ResourceRepositoryContract|CustomMockInterface
     */
    private $resourceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resourceRepository = mock(ResourceRepositoryContract::class);
        $this->observer = new IndexableModelObserver($this->resourceRepository);
    }

    public function testCreated(): void
    {
        $user = new User([
            'resource' => null,
            'name' => 'Someone',
        ]);

        $this->resourceRepository->shouldReceive('create')->once()->with(\Mockery::on(function($data) {

            $this->assertArrayHasKey('content', $data);
            $this->assertArrayHasKey('resource_id', $data);
            $this->assertArrayHasKey('resource_type', $data);

            $this->assertEquals('user', $data['resource_type']);

            return true;
        }));

        $this->observer->created($user);
    }

    public function testUpdated(): void
    {
        $user = new User([
            'resource' => null,
            'name' => 'Someone',
        ]);

        $this->resourceRepository->shouldReceive('create')->once()->with(\Mockery::on(function($data) {

            $this->assertArrayHasKey('content', $data);
            $this->assertArrayHasKey('resource_id', $data);
            $this->assertArrayHasKey('resource_type', $data);

            $this->assertEquals('user', $data['resource_type']);

            return true;
        }));

        $this->observer->updated($user);

        $resource = new Resource();
        $user = new User([
            'resource' => $resource,
            'name' => 'Someone',
        ]);

        $this->resourceRepository->shouldReceive('update')->once()->with($resource, \Mockery::on(function($data) {

            $this->assertArrayHasKey('content', $data);
            $this->assertArrayHasKey('resource_id', $data);
            $this->assertArrayHasKey('resource_type', $data);

            $this->assertEquals('user', $data['resource_type']);

            return true;
        }));

        $this->observer->updated($user);
    }

    public function testDeleted(): void
    {
        $resource = new Resource();
        $user = new User([
            'resource' => $resource,
        ]);

        $this->resourceRepository->shouldReceive('delete')->once()->with($resource);

        $this->observer->deleted($user);
    }
}