<?php

namespace Constants;

enum MediaEnum: int
{
    case FaceBook = 1;

    case KuaiShou = 2;

    case TikTok = 3;

    case SMS = 4;

    case Google = 5;

    case Other = 6;

    public function getName(): string
    {
        return match ($this) {
            self::FaceBook => 'FaceBook',
            self::KuaiShou => '快手',
            self::TikTok => 'TikTok',
            self::SMS => '短信',
            self::Google => 'Google',
            self::Other => '其他',
        };
    }

    /**
     * @param string $name
     * @return int|null
     * @author Caleb 2025/5/10
     */
    public static function fromName(string $name): int|null
    {
        return match ($name) {
            'FaceBook' => self::FaceBook->value,
            '快手' => self::KuaiShou->value,
            'TikTok' => self::TikTok->value,
            '短信' => self::SMS->value,
            'Google' => self::Google->value,
            '其他' => self::Other->value,
            default => null
        };
    }
}
