<?php

namespace SnBH\Common\Helper;

class NumberFuncs
{
    public static function calcPercent(float $value, float $total): float
    {
        if ($total == 0 || $value == 0) {
            return 0;
        }
        return round(($value / $total) * 100, 2);
    }

    public static function calcDiffPercent(float $valueA, float $valueB, bool $format = true, int $decimals = 2): float|string
    {
        if ($valueA == 0 || $valueB == 0) {
            $res = 0;
        } else {
            $res = ($valueA - $valueB) / $valueB * 100;
        }

        if ($format) {
            return StringFuncs::nF($res, $decimals);
        }

        return $res;
    }
}
