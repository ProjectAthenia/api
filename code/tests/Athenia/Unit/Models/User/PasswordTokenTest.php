<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\PasswordToken;
use Tests\TestCase;

/**
 * Class PasswordTokenTest
 * @package Tests\Athenia\Unit\Models\User
 */
final class PasswordTokenTest extends TestCase
{
    public function testUser(): void
    {
        $model = new PasswordToken();

        $relation = $model->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);

        $this->assertEquals('password_tokens.user_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
    }
}