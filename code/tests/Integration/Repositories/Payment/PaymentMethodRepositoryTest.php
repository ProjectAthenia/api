<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Payment;

use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Repositories\Payment\PaymentMethodRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class PaymentMethodRepositoryTest
 * @package Tests\Integration\Repositories\Payment
 */
class PaymentMethodRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var PaymentMethodRepository
     */
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new PaymentMethodRepository(
            new PaymentMethod(),
            $this->getGenericLogMock(),
        );
    }

    public function testFindAllSuccess()
    {
        factory(PaymentMethod::class, 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty()
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess()
    {
        $model = factory(PaymentMethod::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(PaymentMethod::class)->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }

    public function testCreateSuccess()
    {
        $user = factory(User::class)->create();

        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->repository->create([
            'payment_method_type' => 'cash',
            'owner_id' => $user->id,
            'owner_type' => 'user',
        ]);

        $this->assertEquals('cash', $paymentMethod->payment_method_type);
        $this->assertEquals($user->id, $paymentMethod->owner_id);
    }

    public function testUpdateSuccess()
    {
        $model = factory(PaymentMethod::class)->create([
            'payment_method_key' => 'test_key'
        ]);
        $this->repository->update($model, [
            'payment_method_key' => 'new_key'
        ]);

        /** @var PaymentMethod $updated */
        $updated = PaymentMethod::find($model->id);
        $this->assertEquals('new_key', $updated->payment_method_key);
    }

    public function testDeleteSuccess()
    {
        $model = factory(PaymentMethod::class)->create();

        $this->repository->delete($model);

        $this->assertNull(PaymentMethod::find($model->id));
    }
}