<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services;

/**
 * Interface StringHelperServiceContract
 * @package App\Contracts\Services
 */
interface StringHelperServiceContract
{
    /**
     * Handles a multibyte string replace
     *
     * @param $original
     * @param $replacement
     * @param $position
     * @param $length
     * @return mixed
     */
    public function mbSubstrReplace($original, $replacement, $position, $length);

    /**
     * Checks whether or not the passed in string contains a domain name within it
     *
     * @param string $needle
     * @return bool
     */
    public function hasDomainName(string $needle) : bool;
}
