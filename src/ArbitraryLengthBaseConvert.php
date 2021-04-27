<?php
declare(strict_types=1);

namespace PhiSYS\BaseConvert;

final class ArbitraryLengthBaseConvert
{
    /**
     * @author Clifford dot ct at gmail dot com
     * @link https://www.php.net/manual/es/function.base-convert.php#109660
     */
    public static function baseConvert(string $string, int $fromBase, int $toBase): string
    {
        $string = \trim($string);

        if (10 !== $fromBase) {
            $len = \strlen($string);
            $q = '0';

            for ($i=0; $i<$len; $i++) {
                $r = \base_convert($string[$i], $fromBase, 10);
                $q = \bcadd(\bcmul($q, (string)$fromBase), $r);
            }
        } else {
            $q = $string;
        }

        if (10 !== $toBase) {
            $result = '';

            while (\bccomp($q, '0', 0) > 0) {
                $r = \intval(\bcmod($q, (string)$toBase));
                $result = \base_convert((string)$r, 10, $toBase) . $result;
                $q = \bcdiv($q, (string)$toBase, 0);
            }
        } else {
            $result = $q;
        }

        return $result;
    }
}
