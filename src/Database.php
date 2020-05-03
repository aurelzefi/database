<?php

namespace Aurel\Database;

use PDO;

class Database
{
    /**
     * The configuration values for the database connection.
     *
     * @var array
     */
    protected static $config = [];

    /**
     * The database connection instance.
     *
     * @var \Aurel\Database\Connection
     */
    protected static $connection;

    /**
     * The options for the database connection.
     *
     * @var array
     */
    protected static $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * Set the configuration values for the database connection.
     *
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        static::$config = $config;
    }

    /**
     * Set the options for the database connection.
     *
     * @param array $options
     */
    public static function setOptions(array $options)
    {
        static::$options = $options;
    }

    /**
     * Get the database connection instance.
     *
     * @return \Aurel\Database\Connection
     */
    public static function connection()
    {
        $config = static::$config;

        if (is_null(static::$connection)) {
            static::$connection = new Connection(new PDO(
                "mysql:dbname={$config['database']};host={$config['host']}", $config['username'], $config['password'], static::$options
            ));
        }

        return static::$connection;
    }

    /**
     * Get a query builder instance.
     *
     * @param  string  $table
     * @return \Aurel\Database\QueryBuilder
     */
    public static function table($table)
    {
        return static::connection()->table($table);
    }

    /**
     * Dynamically pass methods to the database connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return static::connection()->{$method}(...$parameters);
    }
}
