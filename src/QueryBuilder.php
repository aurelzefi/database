<?php

namespace Database;

use stdClass;

class QueryBuilder
{
    /**
     * The database connection instance.
     *
     * @var \Database\Connection
     */
    protected $connection;

    /**
     * The class, instances of which the results will be retrieved.
     *
     * @var string
     */
    protected $class = stdClass::class;

    /**
     * The database table.
     *
     * @var string
     */
    protected $from;

    /**
     * The columns to be selected.
     *
     * @var array
     */
    protected $columns = ['*'];

    /**
     * The "join" statements.
     *
     * @var array
     */
    protected $joins = [];

    /**
     * The "where" statements.
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * The "group by" statements.
     *
     * @var array
     */
    protected $groups = [];

    /**
     * The "order by" statements.
     *
     * @var array
     */
    protected $orders = [];

    /**
     * The "limit" option.
     *
     * @var int
     */
    protected $limit;

    /**
     * The "offset" option.
     *
     * @var int
     */
    protected $offset;

    /**
     * The parameters passed into the query.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Create a new query builder instance.
     *
     * @param \Database\Connection $connection
     * @return void
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Set the database table for the query.
     *
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        $this->from = $table;

        return $this;
    }

    /**
     * Set the columns to be selected for the query.
     *
     * @param array $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    /**
     * Run the query as a "select" statement.
     *
     * @return array
     */
    public function get()
    {
        return $this->connection->select(
            $this->getSelectQuery(), $this->parameters, $this->class
        );
    }

    /**
     * Run the query as a "select" statement and get a single result.
     *
     * @return mixed
     */
    public function first()
    {
        return $this->connection->selectOne(
            $this->getSelectQuery(), $this->parameters, $this->class
        );
    }

    /**
     * Insert a new record into the database.
     *
     * @param array $attributes
     * @return bool
     */
    public function insert(array $attributes)
    {
        return $this->connection->insert($this->getInsertQuery($attributes), $attributes);
    }

    /**
     * Insert a new record into the database and get the last inserted ID.
     *
     * @param array $attributes
     * @return int
     */
    public function insertGetId(array $attributes)
    {
        $this->insert($attributes);

        return (int) $this->connection->getPdo()->lastInsertId();
    }

    /**
     * Update a record in the database.
     *
     * @param array $attributes
     * @return bool
     */
    public function update(array $attributes)
    {
        return $this->connection->update(
            $this->getUpdateQuery($attributes), array_merge($this->parameters, $attributes)
        );
    }

    /**
     * Delete a record from the database.
     *
     * @return bool
     */
    public function delete()
    {
        return $this->connection->delete(
            $this->getDeleteQuery(), $this->parameters
        );
    }

    /**
     * Get the select query.
     *
     * @return string
     */
    protected function getSelectQuery()
    {
        return sprintf(
            'select %s from %s%s%s', implode(', ', $this->columns), $this->from, $this->getJoins(), $this->getAdditionalQuery()
        );
    }

    /**
     * Get the insert query.
     *
     * @param array $attributes
     * @return string
     */
    protected function getInsertQuery(array $attributes)
    {
        $query = '';

        foreach ($attributes as $key => $value) {
            $query .= sprintf(':%s, ', $key);
        }

        return sprintf(
            'insert into %s (%s) values (%s)', $this->from, implode(', ', array_keys($attributes)), rtrim($query, ', ')
        );
    }

    /**
     * Get the update query.
     *
     * @param array $attributes
     * @return string
     */
    protected function getUpdateQuery(array $attributes)
    {
        $query = '';

        foreach ($attributes as $key => $value) {
            $query .= sprintf("%1\$s = :%1\$s, ", $key);
        }

        return sprintf(
            'update %s set %s%s', $this->from, rtrim($query, ', '), $this->getAdditionalQuery()
        );
    }

    /**
     * Get the delete query.
     *
     * @return string
     */
    protected function getDeleteQuery()
    {
        return sprintf('delete from %s%s', $this->from, $this->getAdditionalQuery());
    }

    /**
     * Get the additional parts of the query.
     *
     * @return string
     */
    protected function getAdditionalQuery()
    {
        $query = '';

        if ($this->wheres) {
            $query .= ' where';

            foreach ($this->wheres as $key => $where) {
                $query .= ($key !== 0 ? ' '.$where['type'] : '').' '.$where['statement'];
            }
        }

        if ($this->groups) {
            $query .= ' group by '.implode(', ', $this->groups);
        }

        if ($this->orders) {
            $query .= ' order by ';

            foreach ($this->orders as $orderBy) {
                $query .= "{$orderBy['column']} {$orderBy['direction']}, ";
            }

            $query = rtrim($query, ', ');
        }

        if ($this->limit) {
            $query .= ' limit '.$this->limit;
        }

        if ($this->offset) {
            $query .= ' offset '.$this->offset;
        }

        return $query;
    }

    /**
     * Get the "joins" part of the query.
     *
     * @return string
     */
    protected function getJoins()
    {
        $query = '';

        foreach ($this->joins as $join) {
            $query .= " {$join['type']} join {$join['join']}";
        }

        return $query;
    }

    /**
     * Add a "where" statement in the query.
     *
     * @param string $where
     * @param array $parameters
     * @param string $type
     * @return $this
     */
    public function where($where, array $parameters = [], $type = 'and')
    {
        $this->wheres[] = ['statement' => $where, 'type' => $type];

        foreach ($parameters as $key => $value) {
            $this->parameters[$key] = $value;
        }

        return $this;
    }

    /**
     * Add an "or where" statement in the query.
     *
     * @param string $where
     * @param array $parameters
     * @return $this
     */
    public function orWhere($where, array $parameters = [])
    {
        return $this->where($where, $parameters, 'or');
    }

    /**
     * Add an "inner join" statement in the query.
     *
     * @param string $join
     * @return $this
     */
    public function innerJoin($join)
    {
        return $this->join($join, 'inner');
    }

    /**
     * Add a "left join" statement in the query.
     *
     * @param string $join
     * @return $this
     */
    public function leftJoin($join)
    {
        return $this->join($join, 'left');
    }

    /**
     * Add a "cross join" statement in the query.
     *
     * @param string $join
     * @return $this
     */
    public function crossJoin($join)
    {
        return $this->join($join, 'cross');
    }

    /**
     * Add a "join" statement of the given type in the query.
     *
     * @param string $join
     * @param string $type
     * @return $this
     */
    protected function join($join, $type)
    {
        $this->joins[] = ['type' => $type, 'join' => $join];

        return $this;
    }

    /**
     * Add an "order by" statement in the query.
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->orders[] = ['column' => $column, 'direction' => $direction];

        return $this;
    }

    /**
     * Add a "group by" statement in the query.
     *
     * @param string $column
     * @return $this
     */
    public function groupBy($column)
    {
        $this->groups[] = $column;

        return $this;
    }

    /**
     * Add the "limit" option in the query.
     *
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Add the "offset" option in the query.
     *
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get the count of the results in the query.
     *
     * @return int
     */
    public function count()
    {
        $results = $this->select('count(*) count')->get();

        if ($this->groups) {
            return count($results);
        }

        return $results[0]->count;
    }

    /**
     * Set the class, instances of which the results will be retrieved.
     *
     * @param string $class
     * @return $this
     */
    public function asInstancesOf($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Paginate the query builder results.
     *
     * @param int $perPage
     * @param string $pageName
     * @return \Database\Paginator
     */
    public function paginate($perPage = 15, $pageName = 'page')
    {
        $page = isset($_GET[$pageName]) ? (int) $_GET[$pageName] : 1;

        $this->limit($perPage)->offset($perPage * ($page - 1));

        return new Paginator(
            $this->get(), $this->getClone()->count(), $perPage, $page, $pageName
        );
    }

    /**
     * Get a clone for the query builder.
     *
     * @return \Database\QueryBuilder
     */
    protected function getClone()
    {
        $clone = clone $this;

        $clone->columns = [];
        $clone->orders = [];
        $clone->limit = null;
        $clone->offset = null;

        return $clone;
    }

    /**
     * Get the SQL statement for the query builder.
     *
     * @return string
     */
    public function toSql()
    {
        return $this->getSelectQuery();
    }
}
