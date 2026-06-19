<?php

namespace App\Support;

final class StockQuantity
{
    public const SCALE = 2;

    public static function normalize(float|string|int|null $quantity): float
    {
        return round((float) $quantity, self::SCALE);
    }

    public static function isLessThan(float|string|int|null $left, float|string|int|null $right): bool
    {
        return self::normalize($left) < self::normalize($right);
    }

    public static function isGreaterThan(float|string|int|null $left, float|string|int|null $right): bool
    {
        return self::normalize($left) > self::normalize($right);
    }

    public static function isZero(float|string|int|null $quantity): bool
    {
        return abs(self::normalize($quantity)) < pow(10, -self::SCALE);
    }

    public static function format(float|string|int|null $quantity, bool $withSign = false): string
    {
        $normalized = self::normalize((float) ($quantity ?? 0));

        if (self::isZero($normalized)) {
            return number_format(0, self::SCALE);
        }

        $formatted = number_format(abs($normalized), self::SCALE);

        if ($withSign && $normalized > 0) {
            return '+' . $formatted;
        }

        if ($normalized < 0) {
            return '-' . $formatted;
        }

        return $formatted;
    }
}
