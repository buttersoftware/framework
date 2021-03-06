<?php
/**
 * Database - A Facade to the Database Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Config\LoaderManager;
use Config\Repository;
use Database\Connection;


class Config
{
    /**
     * The Configuration Repository instance being handled.
     *
     * @var \Config\Repository|null
     */
    protected static $repository;


    /**
     * Return the default Repository instance.
     *
     * @return \Config\Repository
     * @throw \InvalidArgumentException
     */
    protected static function getRepository()
    {
        if (isset(static::$repository)) {
            return static::$repository;
        }

        // Get a LoaderManager instance
        $loader = new LoaderManager();

        if(APPCONFIG_STORE == 'database') {
            // Get a Database Connection instance.
            $connection = Connection::getInstance();

            $loader->setConnection($connection);
        } else if(APPCONFIG_STORE != 'files') {
            throw new \InvalidArgumentException('Invalid Config Store type.');
        }

        return static::$repository = new Repository($loader);
    }

    /**
     * Return the default Repository instance.
     *
     * @return \Config\Repository
     */
    public static function instance()
    {
        return static::getRepository();
    }

    /**
     * Magic Method for calling the methods on the default Repository instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = static::getRepository();

        // Call the non-static method from the Connection instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
