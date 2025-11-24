<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\User;

use App\Models\User\InvitationToken;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

/**
 * Class InvitationTokenTest
 * @package Tests\Athenia\Unit\Models\User
 */
final class InvitationTokenTest extends TestCase
{
    public function testRole(): void
    {
        $model = new InvitationToken();

        $relation = $model->role();

        $this->assertInstanceOf(BelongsTo::class, $relation);

        $this->assertEquals('invitation_tokens.role_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('roles.id', $relation->getQualifiedOwnerKeyName());
    }

    public function testIsUsedReturnsTrueWhenUsedAtIsSet(): void
    {
        $model = new InvitationToken();
        $model->used_at = now();

        $this->assertTrue($model->isUsed());
    }

    public function testIsUsedReturnsFalseWhenUsedAtIsNull(): void
    {
        $model = new InvitationToken();
        $model->used_at = null;

        $this->assertFalse($model->isUsed());
    }
}
