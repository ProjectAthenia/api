<?php
declare(strict_types=1);

namespace App\Athenia\Events\Organization;

use App\Models\Organization\OrganizationManager;

/**
 * Class OrganizationManagerCreatedEvent
 * @package App\Events\Organization
 */
class OrganizationManagerCreatedEvent
{
    /**
     * @var OrganizationManager
     */
    private $organizationManager;

    /**
     * @var string|null
     */
    private $tempPassword;

    /**
     * OrganizationManagerCreatedEvent constructor.
     * @param OrganizationManager $organizationManager
     * @param string|null $tempPassword
     */
    public function __construct(OrganizationManager $organizationManager, string $tempPassword = null)
    {
        $this->organizationManager = $organizationManager;
        $this->tempPassword = $tempPassword;
    }

    /**
     * @return OrganizationManager
     */
    public function getOrganizationManager(): OrganizationManager
    {
        return $this->organizationManager;
    }

    /**
     * @return string|null
     */
    public function getTempPassword(): ?string
    {
        return $this->tempPassword;
    }
}