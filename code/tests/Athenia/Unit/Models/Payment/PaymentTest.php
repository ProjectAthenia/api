<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Payment;

use App\Models\Payment\Payment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

/**
 * Class PaymentTest
 * @package Tests\Athenia\Unit\Models\Payment
 */
final class PaymentTest extends TestCase
{
    public function testLineItems(): void
    {
        $model = new Payment();
        $relation = $model->lineItems();

        $this->assertEquals('payments.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('line_items.payment_id', $relation->getQualifiedForeignKeyName());
    }

    public function testOwner(): void
    {
        $model = new Payment();
        $relation = $model->owner();

        $this->assertEquals('payments.owner_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('owner_type', $relation->getMorphType());
    }

    public function testPaymentMethod(): void
    {
        $model = new Payment();
        $relation = $model->paymentMethod();

        $this->assertEquals('payment_methods.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('payments.payment_method_id', $relation->getQualifiedForeignKeyName());
    }
}