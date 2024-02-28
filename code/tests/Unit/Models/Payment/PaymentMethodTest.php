<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Payment;

use App\Models\Payment\PaymentMethod;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

/**
 * Class PaymentMethodTest
 * @package Tests\Unit\Models\Payment
 */
class PaymentMethodTest extends TestCase
{
    public function testPayments(): void
    {
        $user = new PaymentMethod();
        $relation = $user->payments();

        $this->assertEquals('payment_methods.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('payments.payment_method_id', $relation->getQualifiedForeignKeyName());
    }

    public function testSubscriptions(): void
    {
        $user = new PaymentMethod();
        $relation = $user->subscriptions();

        $this->assertEquals('payment_methods.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('subscriptions.payment_method_id', $relation->getQualifiedForeignKeyName());
    }

    public function testOwner(): void
    {
        $model = new PaymentMethod();
        $relation = $model->owner();

        $this->assertEquals('payment_methods.owner_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('owner_type', $relation->getMorphType());
    }
}