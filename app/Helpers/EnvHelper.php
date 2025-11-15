<?php

namespace App\Helpers;

class EnvHelper
{
    /**
     * Get the environment variable prefix from .env
     * This prefix is randomly generated and stored in .env as ENV_PREFIX
     *
     * @return string
     */
    public static function getPrefix(): string
    {
        return env('ENV_PREFIX', '');
    }

    /**
     * Get an environment variable with the prefix automatically applied
     *
     * Usage:
     *   EnvHelper::get('DB_PASSWORD')
     *
     * Will look for: {PREFIX}_DB_PASSWORD in .env
     * Example: If ENV_PREFIX=XK92, looks for XK92_DB_PASSWORD
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $prefix = self::getPrefix();

        // If no prefix is set, use normal env
        if (empty($prefix)) {
            return env($key, $default);
        }

        // Try prefixed version first
        $prefixedKey = $prefix . '_' . $key;
        $value = env($prefixedKey);

        // If prefixed version doesn't exist, fall back to non-prefixed
        if ($value === null) {
            return env($key, $default);
        }

        return $value;
    }

    /**
     * Check if a prefixed environment variable exists
     *
     * @param  string  $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        $prefix = self::getPrefix();

        if (empty($prefix)) {
            return env($key) !== null;
        }

        $prefixedKey = $prefix . '_' . $key;
        return env($prefixedKey) !== null || env($key) !== null;
    }

    /**
     * Generate a random prefix for environment variables
     *
     * @param  int  $length
     * @return string
     */
    public static function generatePrefix(int $length = 6): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $prefix = '';

        for ($i = 0; $i < $length; $i++) {
            $prefix .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $prefix;
    }

    /**
     * Get all environment variables with the prefix
     *
     * @return array
     */
    public static function getAllPrefixed(): array
    {
        $prefix = self::getPrefix();

        if (empty($prefix)) {
            return [];
        }

        $prefixedVars = [];
        foreach ($_ENV as $key => $value) {
            if (strpos($key, $prefix . '_') === 0) {
                $originalKey = substr($key, strlen($prefix) + 1);
                $prefixedVars[$originalKey] = $value;
            }
        }

        return $prefixedVars;
    }
}
