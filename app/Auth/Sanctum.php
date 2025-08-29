<?php

namespace App\Auth;

class Sanctum
{
    /**
     * Indicates if the Sanctum API token services are enabled.
     */
    public static bool $apiTokensEnabled = true;

    /**
     * The default permissions that should be selected for new tokens.
     *
     * @var array
     */
    public static $defaultPermissions = [];

    /**
     * The permissions that are available to all API tokens.
     *
     * @var array<int, string>
     */
    public static array $permissions = [];

    /**
     * Enable the Sanctum API token services.
     */
    public static function enableApiTokens(bool $apiTokensEnabled = true): static
    {
        static::$apiTokensEnabled = $apiTokensEnabled;

        return new static;
    }

    /**
     * Determine if the Sanctum API token services are enabled.
     */
    public static function apiTokensEnabled(): bool
    {
        return static::$apiTokensEnabled;
    }

    /**
     * Define the permissions that are available to all API tokens.
     *
     * @param  array<int, string>  $permissions
     */
    public static function permissions(array $permissions): static
    {
        static::$permissions = $permissions;

        return new static;
    }

    /**
     * Get the permissions that are available to all API tokens.
     *
     * @return array<int, string>
     */
    public static function getPermissions(): array
    {
        return static::$permissions;
    }

    /**
     * Define the default permissions that shoul be selected for new tokens.
     *
     * @param  array<int, string>  $permissions
     */
    public static function defaultPermissions(array $permissions): static
    {
        static::$defaultPermissions = $permissions;

        return new static;
    }

    /**
     * Get the default permissions that should be selected for new tokens.
     *
     * @return array<int, string>
     */
    public static function getDefaultPermissions(): array
    {
        return static::$defaultPermissions;
    }
}
