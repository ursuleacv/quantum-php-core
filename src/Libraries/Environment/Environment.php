<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 1.0.0
 */

namespace Quantum\Libraries\Environment;

use Dotenv\Dotenv;

/**
 * Class Environment
 * @package Quantum\Libraries\Environment
 * @uses Dotenv
 */
class Environment
{

    /**
     * Path to .env file
     *
     * @var string
     */
    public static $envPath = BASE_DIR . DS . '.env';

    /**
     * Load
     * 
     * Loads the environment variables from .env file
     */
    public static function load()
    {
        $env = require_once BASE_DIR . DS . 'config' . DS . 'env.php';

        $envFile = '.env';

        if ($env['app_env'] != 'production') {
            $envFile .= '.' . $env['app_env'];
        }

        $dotenv = new Dotenv(BASE_DIR, $envFile);
        $dotenv->load();
    }

    /**
     * Get Value
     * 
     * Gets the environment variable value
     *
     * @param string $key
     * @param mixed $default
     * @return array|false|mixed|null|string
     */
    public static function getValue($key, $default = null)
    {
        $val = getenv($key);

        if ($val === false && $default) {
            return $default;
        } else {
            return $val;
        }
    }

    /**
     * Update Row
     * 
     * Creates or updates the row in .env
     *
     * @param string $keyName
     * @param string $value
     * @return void
     */
    public static function updateRow($keyName, $value)
    {
        $oldRow = self::getRow($keyName);
        
        if ($oldRow) {
            file_put_contents(self::$envPath, preg_replace(
                '/^' . $oldRow . '/m',
                $keyName . "=" . $value . "\r\n",
                file_get_contents(self::$envPath)
            ));

        } else {
            file_put_contents(self::$envPath, $keyName . "=" . $value . "\r\n", FILE_APPEND);
        }
    }

    /**
     * Gets the row of .env file by given key
     *
     * @param $keyName
     * @return string
     */
    private static function getRow($keyName)
    {
        $envKeys = file(self::$envPath);

        foreach ($envKeys as $envKey) {
            if (preg_match('/^' . $keyName . '=/', $envKey)) {
                return preg_quote($envKey, '/');
            }
        }
    }

}
