<?php
declare(strict_types=1);

namespace App\Contracts\Services\Wiki;

use App\Models\Wiki\ArticleVersion;

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
