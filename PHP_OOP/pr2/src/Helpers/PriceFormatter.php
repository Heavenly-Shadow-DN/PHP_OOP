<?php
declare(strict_types=1);

namespace Shop\Helpers;

class PriceFormatter
{
    public static function format(float $price): string
    {
        // 1 250,00 ₽ — пробел как разделитель тысяч, запятая, 2 знака
        return number_format($price, 2, ',', ' ') . ' ₽';
    }
}
