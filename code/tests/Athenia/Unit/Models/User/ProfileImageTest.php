<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\User;

use App\Models\User\ProfileImage;
use Tests\TestCase;

/**
 * Class ProfileImageTest
 * @package Tests\Athenia\Unit\Models\User
 */
final class ProfileImageTest extends TestCase
{
    public function testOrganization(): void
    {
        $model = new ProfileImage();
        $relation = $model->organization();

        $this->assertEquals('assets.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('organizations.profile_image_id', $relation->getQualifiedForeignKeyName());
    }

    public function testUser(): void
    {
        $model = new ProfileImage();
        $relation = $model->user();

        $this->assertEquals('assets.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('users.profile_image_id', $relation->getQualifiedForeignKeyName());
    }
}