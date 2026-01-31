<?php

namespace App\Service;

class Environment
{
    /**
     * -------------------------------------------------------------------------
     * Get an environment variable
     * -------------------------------------------------------------------------
     *
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function get(string $key, string $default = ''): mixed
    {
        $value = getenv($key);

        // Se não encontrou com getenv, tenta $_ENV e $_SERVER
        if ($value === false) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        }

        // Se ainda é false ou null, usa o default
        if ($value === false || $value === null) {
            return $default;
        }

        // Converter strings booleanas
        if ($value === 'true' || $value === '(true)') {
            return true;
        }

        if ($value === 'false' || $value === '(false)') {
            return false;
        }

        // Converter strings vazias em null
        if ($value === '') {
            return null;
        }

        return $value;
    }

    /**
     * -------------------------------------------------------------------------
     * Check if is production
     * -------------------------------------------------------------------------
     *
     * @return boolean
     */
    public static function production(): bool
    {
        return (self::get('APP_ENV') === 'production'
            || self::get('APP_ENV') === 'prod'
        );
    }

    /**
     * -------------------------------------------------------------------------
     * Check if is staging
     * -------------------------------------------------------------------------
     *
     * @return boolean
     */
    public static function staging(): bool
    {
        return self::get('APP_ENV') === 'staging';
    }

    /**
     * -------------------------------------------------------------------------
     * Check if is development or local
     * -------------------------------------------------------------------------
     *
     * @return boolean
     */
    public static function local(): bool
    {
        return (self::get('APP_ENV') === 'loc'
            || self::get('APP_ENV') === 'local'
            || self::get('APP_ENV') === 'dev'
            || self::get('APP_ENV') === 'development'
        );
    }

    /**
     * -------------------------------------------------------------------------
     * Check if is development or local
     * -------------------------------------------------------------------------
     *
     * @return boolean
     */
    public static function dev(): bool
    {
        return self::local();
    }

    /**
     * -------------------------------------------------------------------------
     * Check if is development or local
     * -------------------------------------------------------------------------
     *
     * @return boolean
     */
    public static function development(): bool
    {
        return self::local();
    }
}
