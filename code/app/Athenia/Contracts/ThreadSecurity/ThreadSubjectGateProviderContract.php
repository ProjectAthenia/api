<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\ThreadSecurity;

/**
 * Interface ThreadSubjectGateProviderContract
 * @package App\Contracts\ThreadSecurity
 */
interface ThreadSubjectGateProviderContract
{
    /**
     * Creates the gate for the passed in subject type
     *
     * @param $subjectType
     * @return ThreadSubjectGateContract|null
     */
    public function createGate($subjectType): ?ThreadSubjectGateContract;
}