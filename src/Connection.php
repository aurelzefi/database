<?php

namespace Aurel\Database;

use PDO;
use stdClass;

class Connection
{
    /**
     * The PDO connection.
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * Create a new connection instance.
     *
     * @param  \PDO  $pdo
     * @return void
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get the PDO connection.
     *
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Set the database table for the query builder.
     *
     * @param  string  $table
     * @return \Aurel\Database\QueryBuilder
     */
    public function table($table)
    {
        return $this->query()->from($table);
    }

    /**
     * Get a new query builder instance.
     *
     * @return \Aurel\Database\QueryBuilder
     */
    public function query()
    {
        return new QueryBuilder($this);
    }

    /**
     * Run a select statement against the database.
     *
     * @param  string  $query
     * @param  array  $parameters
     * @param  string  $class
     * @return array
     */
    public function select($query, array $parameters = [], $class = stdClass::class)
    {
        $statement = $this->pdo->prepare($query);

        $statement->execute($parameters);

        return $statement->fetchAll(PDO::FETCH_CLASS, $class);
    }

    /**
     * Run a select statement against the database and get a single result.
     *
     * @param  string  $query
     * @param  array  $parameters
     * @param  string  $class
     * @return mixed
     */
    public function selectOne($query, array $parameters = [], $class = stdClass::class)
    {
        $statement = $this->pdo->prepare($query);

        $statement->execute($parameters);

        return $statement->fetchObject($class);
    }

    /**
     * Run an insert statement against the database.
     *
     * @param  string  $query
     * @param  array  $parameters
     * @return bool
     */
    public function insert($query, array $parameters = [])
    {
        return $this->persist($query, $parameters);
    }

    /**
     * Run an update statement against the database.
     *
     * @param  string  $query
     * @param  array  $parameters
     * @return bool
     */
    public function update($query, array $parameters = [])
    {
        return $this->persist($query, $parameters);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param  string  $query
     * @param  array  $parameters
     * @return bool
     */
    public function delete($query, array $parameters = [])
    {
        return $this->persist($query, $parameters);
    }

    /**
     * Execute a given SQL statement.
     *
     * @param  string  $query
     * @param  array  $parameters
     * @return bool
     */
    protected function persist($query, array $parameters)
    {
        $statement = $this->pdo->prepare($query);

        return $statement->execute($parameters);
    }
}
