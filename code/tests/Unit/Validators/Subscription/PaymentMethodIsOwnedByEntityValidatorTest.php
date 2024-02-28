<?php
declare(strict_types=1);

namespace Tests\Unit\Validators\Subscription;

use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Models\Organization\Organization;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Validators\Subscription\PaymentMethodIsOwnedByEntityValidator;
use Cartalyst\Stripe\Exception\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

/**
 * Class PaymentMethodIsOwnedByEntityValidatorTest
 * @package Tests\Unit\Validators\Subscription
 */
class PaymentMethodIsOwnedByEntityValidatorTest extends TestCase
{
    public function testValidateFailsWithNonExistingRate(): void
    {
        $repository = mock(PaymentMethodRepositoryContract::class);
        $request = mock(Request::class);
        $validator = new PaymentMethodIsOwnedByEntityValidator($repository, $request);

        $repository->shouldReceive('findOrFail')->andThrow(new NotFoundException());

        $this->assertFalse($validator->validate('payment_method_id', 214));
    }

    public function testValidateFailsWithMismatchedUser(): void
    {
        $repository = mock(PaymentMethodRepositoryContract::class);
        $request = mock(Request::class);
        $validator = new PaymentMethodIsOwnedByEntityValidator($repository, $request);

        $paymentMethod = new PaymentMethod([
            'owner_id' => 3242,
            'owner_type' => 'user',
        ]);
        $repository->shouldReceive('findOrFail')->andReturn($paymentMethod);
        $user = new User();
        $user->id = 324;
        $request->shouldReceive('route')->with('user')->andReturn($user);
        $route = mock(Route::class);
        $route->shouldReceive('parameterNames')->andReturn([
            'user'
        ]);
        $request->shouldReceive('route')->andReturn($route);

        $this->assertFalse($validator->validate('payment_method_id', 214));
    }

    public function testValidateFailsWithMismatchedOwnerType(): void
    {
        $repository = mock(PaymentMethodRepositoryContract::class);
        $request = mock(Request::class);
        $validator = new PaymentMethodIsOwnedByEntityValidator($repository, $request);

        $paymentMethod = new PaymentMethod([
            'owner_id' => 3242,
            'owner_type' => 'organization',
        ]);
        $repository->shouldReceive('findOrFail')->andReturn($paymentMethod);
        $user = new User();
        $user->id = 3242;
        $request->shouldReceive('route')->with('user')->andReturn($user);
        $route = mock(Route::class);
        $route->shouldReceive('parameterNames')->andReturn([
            'user'
        ]);
        $request->shouldReceive('route')->andReturn($route);

        $this->assertFalse($validator->validate('payment_method_id', 214));
    }

    public function testValidatePassesWithUser(): void
    {
        $repository = mock(PaymentMethodRepositoryContract::class);
        $request = mock(Request::class);
        $validator = new PaymentMethodIsOwnedByEntityValidator($repository, $request);

        $paymentMethod = new PaymentMethod([
            'owner_id' => 3242,
            'owner_type' => 'user',
        ]);
        $repository->shouldReceive('findOrFail')->andReturn($paymentMethod);
        $user = new User();
        $user->id = 3242;
        $request->shouldReceive('route')->with('user')->andReturn($user);
        $route = mock(Route::class);
        $route->shouldReceive('parameterNames')->andReturn([
            'user'
        ]);
        $request->shouldReceive('route')->andReturn($route);

        $this->assertTrue($validator->validate('payment_method_id', 214));
    }

    public function testValidatePassesWithOrganization(): void
    {
        $repository = mock(PaymentMethodRepositoryContract::class);
        $request = mock(Request::class);
        $validator = new PaymentMethodIsOwnedByEntityValidator($repository, $request);

        $paymentMethod = new PaymentMethod([
            'owner_id' => 3242,
            'owner_type' => 'organization',
        ]);
        $repository->shouldReceive('findOrFail')->andReturn($paymentMethod);
        $user = new Organization();
        $user->id = 3242;
        $request->shouldReceive('route')->with('organization')->andReturn($user);
        $route = mock(Route::class);
        $route->shouldReceive('parameterNames')->andReturn([
            'organization'
        ]);
        $request->shouldReceive('route')->andReturn($route);

        $this->assertTrue($validator->validate('payment_method_id', 214));
    }
}