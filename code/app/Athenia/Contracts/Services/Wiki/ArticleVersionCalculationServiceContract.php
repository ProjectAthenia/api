<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Wiki;

/**
 * Interface ArticleVersionCalculationServiceContract
 * @package App\Contracts\Services
 */
interface ArticleVersionCalculationServiceContract
{
    /**
     * Figures out whether or not the new version is a major version
     *
     * @param string $new
     * @param string $old
     * @return bool
     */
    public function determineIfMajor(string $new, string $old): bool;

    /**
     * Figures out whether or not the new version is a minor version
     *
     * @param string $new
     * @param string $old
     * @return bool
     */
    public function determineIfMinor(string $new, string $old): bool;
}
