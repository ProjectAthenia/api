<?php
declare(strict_types=1);

namespace App\Services\Wiki;

use App\Contracts\Services\Wiki\ArticleVersionCalculationServiceContract;
use SebastianBergmann\Diff\Differ;

/**
 * Class ArticleVersionCalculationService
 * @package App\Services
 */
class ArticleVersionCalculationService implements ArticleVersionCalculationServiceContract
{
    /**
     * @var array[]
     */
    private $diff;

    /**
     * @var int Total amount of lines that were removed from the content
     */
    private $removedLinesOfContent = 0;

    /**
     * @var int Total amount of lines that were added to the content
     */
    private $addedLinesOfContent = 0;

    /**
     * @var array An array of our line matches with our removal first then our addition
     */
    private $matches = [];

    /**
     * ArticleVersionCalculationService constructor.
     * @param string $newContent
     * @param string $oldContent
     */
    public function parseDiff(string $newContent, string $oldContent)
    {
        $differ = new Differ();
        $this->diff = $differ->diffToArray($newContent, $oldContent);

        $lineNumber = 0;
        foreach ($this->diff as $line) {
            if ($line[0]) {
                $isAddition = $line[1] == Differ::ADDED;
                if (!$isAddition) {
                    $lineNumber++;
                }

                if ($line[1] == Differ::REMOVED) {
                    $this->removedLinesOfContent++;
                }
                if ($isAddition) {
                    $this->addedLinesOfContent++;
                }

                if ($line[1] != Differ::OLD) {
                    if (!isset($this->matches[$lineNumber])) {
                        $this->matches[$lineNumber] = [
                            'addition' => null,
                            'removal' => null,
                        ];
                    }

                    $key = $isAddition ? 'addition' : 'removal';

                    $this->matches[$lineNumber][$key] = $line[0];
                }
            }
        }
    }

    /**
     * Calculates a percentage of changed characters between two strings
     *
     * @param $new
     * @param $old
     * @return float
     */
    public function calculateTextDiffPercentage($new, $old): float
    {
        $originalLength = strlen($old);

        $newProcessed = implode("\n", str_split($new));
        $oldProcessed = implode("\n", str_split($old));

        $differ = new Differ();
        $diff = $differ->diffToArray($oldProcessed . "\n", $newProcessed . "\n");

        $charactersChanged = 0;

        foreach ($diff as $line) {
            if ($line[1] != Differ::OLD) {
                $charactersChanged++;
            }
        }

        return $charactersChanged / $originalLength;
    }

    /**
     * Figures out whether or not the new version is a major version
     *
     * @param string $new
     * @param string $old
     * @return bool
     */
    public function determineIfMajor(string $new, string $old): bool
    {
        if (!$this->diff) {
            $this->parseDiff($new, $old);
        }

            // Whenever a line of content is removed it means that we have a major version
        if ($this->removedLinesOfContent > $this->addedLinesOfContent) {
            return true;
        }

        foreach ($this->matches as $match) {

            /** @var string|null $removal */
            $removal = $match['removal'];
            /** @var string|null $addition */
            $addition = $match['addition'];

            // If a header is removed we need to a analyze this a bit more
            if ($removal && strpos($removal, '#') === 0) {
                // A header was completely removed
                if (!$addition || strpos($addition, '#') !== 0) {
                    return true;
                }

                // now they are both headers, so we will compare the percentage change to see if it was a large change
                if ($this->calculateTextDiffPercentage($addition, $removal) > 0.33) {
                    return true;
                }
            }

            // If there is a section that is now found with a match then it is a major version
            if ($removal && !$addition) {
                return true;
            }

            if ($removal && $addition) {
                return $this->calculateTextDiffPercentage($addition, $removal) > .5;
            }
        }
        return false;
    }

    /**
     * Figures out whether or not the new version is a minor version
     *
     * @param string $new
     * @param string $old
     * @return bool
     */
    public function determineIfMinor(string $new, string $old): bool
    {
        if (!$this->diff) {
            $this->parseDiff($new, $old);
        }

        // Any time there is new content, it is a minor bump
        if ($this->addedLinesOfContent > $this->removedLinesOfContent) {
            return true;
        }

        foreach ($this->matches as $match) {

            /** @var string|null $removal */
            $removal = $match['removal'];
            /** @var string|null $addition */
            $addition = $match['addition'];

            // If either of these are not here, then we can be 100% certain that there was a pretty big change
            if (!$removal || !$addition) {
                return true;
            }

            return $this->calculateTextDiffPercentage($addition, $removal) > .2;
        }

        return false;
    }
}
