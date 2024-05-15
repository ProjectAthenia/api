<?php
declare(strict_types=1);

namespace App\Athenia\Services;

use App\Athenia\Contracts\Services\StringHelperServiceContract;

/**
 * Class StringHelperService
 * @package App\Providers
 */
class StringHelperService implements StringHelperServiceContract
{
    /**
     * Handles a multibyte string replacement properly
     *
     * @param $original
     * @param $replacement
     * @param $position
     * @param $length
     * @return mixed|string
     */
    public function mbSubstrReplace($original, $replacement, $position, $length)
    {
        $startString = mb_substr($original, 0, $position, "UTF-8");
        $endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");

        $out = $startString . $replacement . $endString;

        return $out;
    }

    /**
     * Checks whether or not the passed in string contains a domain name within it
     *
     * @source https://gist.github.com/egulhan/4b2495499cc229b8e6426621993d11b5#file-check-if-contain-domain-name-php
     *
     * @param string $needle
     * @return bool
     */
    public function hasDomainName(string $needle): bool
    {
        $pattern = '/(http[s]?\:\/\/)?(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}/';
        return !!preg_match($pattern, $needle);
    }
}
