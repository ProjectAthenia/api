<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\Payment;

use App\Athenia\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Athenia\Events\Payment\DefaultPaymentMethodSetEvent;
use App\Athenia\Listeners\Payment\DefaultPaymentMethodSetListener;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class DefaultPaymentMethodSetListenerTest
 * @package Tests\Athenia\Unit\Listeners\Payment
 */
final class DefaultPaymentMethodSetListenerTest extends TestCase
{
    public function testHandle(): void
    {
        $defaultPaymentMethod = new PaymentMethod([
            'owner' => new User([
                'paymentMethods' => collect([]),
            ]),
        ]);
        $defaultPaymentMethod->id = 142;

        $oldDefault = new PaymentMethod([
            'default' => true,
        ]);
        $oldDefault->id = 2342;

        $nonDefault = new PaymentMethod([
            'default' => false,
        ]);
        $nonDefault->id = 36;

        $defaultPaymentMethod->owner->paymentMethods->push($oldDefault);
        $defaultPaymentMethod->owner->paymentMethods->push($defaultPaymentMethod);
        $defaultPaymentMethod->owner->paymentMethods->push($nonDefault);

        $event = new DefaultPaymentMethodSetEvent($defaultPaymentMethod);

        $repository = mock(PaymentMethodRepositoryContract::class);
        $repository->shouldReceive('update')->once()->with($oldDefault, ['default' => false]);

        $listener = new DefaultPaymentMethodSetListener($repository);

        $listener->handle($event);
    }
}
