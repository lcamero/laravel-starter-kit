<?php

namespace App\Enums;

enum Role: string
{
    case Administrator = 'administrator';

    public function getLabel(): string
    {
        return match ($this) {
            self::Administrator => __('users.administrator'),
        };
    }
}
