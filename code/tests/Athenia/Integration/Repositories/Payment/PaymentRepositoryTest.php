<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Payment;

use App\Athenia\Repositories\Payment\LineItemRepository;
use App\Athenia\Repositories\Payment\PaymentRepository;
use App\Models\Payment\LineItem;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class PaymentRepositoryTest
 * @package Tests\Integration\Repositories\Payment
 */
final class PaymentRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var PaymentRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new PaymentRepository(
            new Payment(),
            $this->getGenericLogMock(),
            new LineItemRepository(
                new LineItem(),
                $this->getGenericLogMock()
            ),
        );
    }

    public function testFindAllSuccess(): void
    {
        Payment::factory()->count(5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty(): void
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess(): void
    {
        $model = Payment::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        Payment::factory()->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }

    public function testCreateSuccess(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        /** @var Payment $payment */
        $payment = $this->repository->create([
            'amount' => 11.32,
        ], $paymentMethod);

        $this->assertEquals(11.32, $payment->amount);
        $this->assertEquals($paymentMethod->id, $payment->payment_method_id);
    }

    public function testCreateSuccessWithLineItems(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        /** @var Payment $payment */
        $payment = $this->repository->create([
            'amount' => 11.32,
            'line_items' => [
                [
                    'item_type' => 'donation',
                    'amount' => 11.32,
                ]
            ]
        ], $paymentMethod);

        $this->assertEquals(11.32, $payment->amount);
        $this->assertEquals($paymentMethod->id, $payment->payment_method_id);
        $this->assertCount(1, $payment->lineItems);
    }

    public function testUpdateSuccess(): void
    {
        $model = Payment::factory()->create();
        LineItem::factory()->create([
            'payment_id' => $model->id,
        ]);
        $this->repository->update($model, [
            'refunded_at' => Carbon::now(),
        ]);

        /** @var Payment $updated */
        $updated = Payment::find($model->id);
        $this->assertNotNull($updated->refunded_at);
        $this->assertCount(1, $updated->lineItems);
    }

    public function testUpdateSuccessWithLineItems(): void
    {
        $model = Payment::factory()->create();
        $keep = LineItem::factory()->create([
            'payment_id' => $model->id,
        ]);
        LineItem::factory()->create([
            'payment_id' => $model->id,
        ]);
        $this->repository->update($model, [
            'refunded_at' => Carbon::now(),
            'line_items' => [
                [
                    'id' => $keep->id,
                    'item_type' => 'donation',
                    'amount' => 2.11,
                ],
                [
                    'item_type' => 'donation',
                    'amount' => 12.11,
                ],
                [
                    'item_type' => 'donation',
                    'amount' => 1.11,
                ],
            ]
        ]);

        /** @var Payment $updated */
        $updated = Payment::find($model->id);
        $this->assertNotNull($updated->refunded_at);
        $this->assertCount(3, $updated->lineItems);
    }

    public function testDeleteSuccess(): void
    {
        $model = Payment::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(Payment::find($model->id));
    }
}
