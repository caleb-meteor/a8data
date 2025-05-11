<?php

namespace App\Constants;


enum DepartmentEnum: int
{
    case SelfPlacement = 1;

    case SMS = 2;

    case ProxyPlacement = 3;

    case Promotion = 4;

    case External = 5;

    public function getName(): string
    {
        return match ($this) {
            self::SelfPlacement => 'A8自投',
            self::SMS => 'A8短信',
            self::ProxyPlacement => 'A8代投',
            self::Promotion => 'A8推广',
            self::External => 'A8外接',
        };
    }

    /**
     * @param string $name
     * @return int|null
     * @author Caleb 2025/5/10
     */
    public static function fromName(string $name): int|null
    {
        $name = strtoupper($name);

        return match ($name) {
            'A8自投' => self::SelfPlacement->value,
            'A8短信' => self::SMS->value,
            'A8代投' => self::ProxyPlacement->value,
            'A8推广' => self::Promotion->value,
            'A8外接' => self::External->value,
            default => null
        };
    }
}
