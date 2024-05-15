<?php
declare(strict_types=1);

namespace App\Athenia\Traits;

/**
 * Trait CanGetAndUnset
 * @package App\Repositories\Traits
 */
trait CanGetAndUnset
{
    /**
     * Retrieves the needle from the haystack, removes the needle from the haystack, and returns the result
     *
     * @param array $haystack
     * @param string $needle
     * @param mixed $default
     * @return mixed
     */
    public function getAndUnset(array &$haystack, string $needle, $default = null)
    {
        $value = $haystack[$needle] ?? $default;
        unset($haystack[$needle]);

        return $value;
    }
}