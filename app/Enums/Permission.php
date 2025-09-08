<?php

namespace App\Enums;

enum Permission: string
{
    case ManageApplicationSettings = 'manage application settings';
    case ManageApplicationUsers = 'manage application users';

    public function getLabel(): string
    {
        return match ($this) {
            self::ManageApplicationSettings => __('settings.manage_application_settings'),
            self::ManageApplicationUsers => __('settings.manage_application_users'),
        };
    }
}
