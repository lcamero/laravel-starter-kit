<?php

namespace App\Enums;

enum Permission: string
{
    case ManageApplicationSettings = 'manage application settings';
    case ManageApplicationUsers = 'manage application users';

    public function getLabel(): string
    {
        return match ($this) {
            self::ManageApplicationSettings => __('Manage Application Settings'),
            self::ManageApplicationUsers => __('Manage Application Users'),
        };
    }
}
