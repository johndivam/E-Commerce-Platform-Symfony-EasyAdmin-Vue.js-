<?php

namespace App\Enum;

enum ProductStatus: string
{
    case AVAILABLE     = 'available';
    case OUT_OF_STOCK  = 'out_of_stock';
    case DISCONTINUED  = 'discontinued';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE    => 'Available',
            self::OUT_OF_STOCK => 'Out of Stock',
            self::DISCONTINUED => 'Discontinued',
        };
    }

    public function badgeStyle(): string
    {
        return match($this) {
            self::AVAILABLE    => 'success',
            self::OUT_OF_STOCK => 'warning',
            self::DISCONTINUED => 'danger',
        };
    }

    public static function choices(): array
    {
        return array_combine(
            array_map(fn(self $case) => $case->label(), self::cases()),
            array_map(fn(self $case) => $case->value, self::cases())
        );
    }

    public static function badgeStyles(): array
    {
        return array_combine(
            array_map(fn(self $case) => $case->value, self::cases()),
            array_map(fn(self $case) => $case->badgeStyle(), self::cases())
        );
    }
}