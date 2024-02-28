<?php
declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Models\Asset;
use App\Models\Organization\Organization;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentMethod;
use App\Validators\OwnedByValidator;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Class OwnedByTest
 * @package Tests\Unit\Validators
 */
class OwnedByValidatorTest extends TestCase
{
    public function testOwnedByValidatorFalse(): void
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->id = 2345;
        $organization = new Organization([
            'paymentMethods' => collect(),
        ]);
        $organization->id = 234;

        $request = mock(Request::class);
        $request->shouldReceive('route')->with('organization')->andReturn($organization);

        $ownedBy = new OwnedByValidator($request);

        $params = ['organization','paymentMethods'];

        $this->assertFalse($ownedBy->validate('payment_method.1', 2345, $params));
    }

    public function testOwnedByValidatorTrue(): void
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->id = 2345;
        $organization = new Organization([
            'paymentMethods' => collect([
                $paymentMethod,
            ]),
        ]);
        $organization->id = 234;

        $request = mock(Request::class);
        $request->shouldReceive('route')->with('organization')->andReturn($organization);

        $ownedBy = new OwnedByValidator($request);

        $params = ['organization','paymentMethods'];

        $this->assertTrue($ownedBy->validate('payment_method.1', 2345, $params));
    }
}
