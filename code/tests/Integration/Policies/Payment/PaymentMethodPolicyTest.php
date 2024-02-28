<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Payment;

use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Policies\Payment\PaymentMethodPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class PaymentMethodPolicyTest
 * @package Tests\Integration\Policies\Payment
 */
final class PaymentMethodPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testCreate(): void
    {
        $policy = new PaymentMethodPolicy();

        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->assertFalse($policy->create($me, $other));
        $this->assertTrue($policy->create($me, $me));
    }

    public function testUpdate(): void
    {
        $policy = new PaymentMethodPolicy();

        $me = User::factory()->create();
        $other = User::factory()->create();

        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $me->id,
        ]);

        $this->assertFalse($policy->update($me, $other, $paymentMethod));
        $this->assertFalse($policy->update($other, $other, $paymentMethod));
        $this->assertTrue($policy->update($me, $me, $paymentMethod));
    }

    public function testDelete(): void
    {
        $policy = new PaymentMethodPolicy();

        $me = User::factory()->create();
        $other = User::factory()->create();

        $paymentMethod = PaymentMethod::factory()->create([
            'owner_id' => $me->id,
        ]);

        $this->assertFalse($policy->delete($me, $other, $paymentMethod));
        $this->assertFalse($policy->delete($other, $other, $paymentMethod));
        $this->assertTrue($policy->delete($me, $me, $paymentMethod));
    }
}
