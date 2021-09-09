<?php
/*
 MIT License

 Copyright (c) 2021 Anweshan Roy Chowdhury

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.

 */
namespace Anweshan\Database\SQL;

use Closure;
use Anweshan\Database\Connection;

class Query extends BaseStatement
{
    /** @var    Connection */
    protected $connection;

    /** @var    array */
    protected $tables;

    /**
     * Query constructor.
     * @param Connection $connection
     * @param $tables
     * @param SQLStatement|null $statement
     */
    public function __construct(Connection $connection, $tables, SQLStatement $statement = null)
    {
        parent::__construct($statement);
        $this->tables = $tables;
        $this->connection = $connection;
    }

    /**
     * @return  Select
     */
    protected function buildSelect(): Select
    {
        return new Select($this->connection, $this->tables, $this->sql);
    }

    /**
     * @return  Delete
     */
    protected function buildDelete(): Delete
    {
        return new Delete($this->connection, $this->tables, $this->sql);
    }

    /**
     * @param   bool $value (optional)
     *
     * @return  Select|SelectStatement
     */
    public function distinct($value = true)
    {
        return $this->buildSelect()->distinct($value);
    }

    /**
     * @param   string|array $columns
     *
     * @return  Select
     */
    public function groupBy($columns)
    {
        return $this->buildSelect()->groupBy($columns);
    }

    /**
     * @param   string $column
     * @param   Closure $value (optional)
     *
     * @return  Select
     */
    public function having($column, Closure $value = null)
    {
        return $this->buildSelect()->having($column, $value);
    }

    /**
     * @param   string $column
     * @param   Closure $value
     *
     * @return  Select
     */
    public function andHaving($column, Closure $value = null)
    {
        return $this->buildSelect()->andHaving($column, $value);
    }

    /**
     * @param   string $column
     * @param   Closure $value
     *
     * @return  Select
     */
    public function orHaving($column, Closure $value = null)
    {
        return $this->buildSelect()->orHaving($column, $value);
    }

    /**
     * @param   string|array $columns
     * @param   string $order (optional)
     * @param   string $nulls (optional)
     *
     * @return  Select|SelectStatement
     */
    public function orderBy($columns, $order = 'ASC', $nulls = null)
    {
        return $this->buildSelect()->orderBy($columns, $order, $nulls);
    }

    /**
     * @param   int $value
     *
     * @return  Select|SelectStatement
     */
    public function limit($value)
    {
        return $this->buildSelect()->limit($value);
    }

    /**
     * @param   int $value
     *
     * @return  Select|SelectStatement
     */
    public function offset($value)
    {
        return $this->buildSelect()->offset($value);
    }

    /**
     * @param   string $table
     * @param   string $database (optional)
     *
     * @return  Select|SelectStatement
     */
    public function into($table, $database = null)
    {
        return $this->buildSelect()->into($table, $database);
    }

    /**
     * @param   array $columns (optional)
     *
     * @return  \Anweshan\Database\ResultSet
     */
    public function select($columns = [])
    {
        return $this->buildSelect()->select($columns);
    }

    /**
     * @param   string $name
     *
     * @return  mixed|false
     */
    public function column($name)
    {
        return $this->buildSelect()->column($name);
    }

    /**
     * @param   string $column (optional)
     * @param   bool $distinct (optional)
     *
     * @return  int
     */
    public function count($column = '*', $distinct = false)
    {
        return $this->buildSelect()->count($column, $distinct);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  int|float
     */
    public function avg($column, $distinct = false)
    {
        return $this->buildSelect()->avg($column, $distinct);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  int|float
     */
    public function sum($column, $distinct = false)
    {
        return $this->buildSelect()->sum($column, $distinct);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  int|float
     */
    public function min($column, $distinct = false)
    {
        return $this->buildSelect()->min($column, $distinct);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  int|float
     */
    public function max($column, $distinct = false)
    {
        return $this->buildSelect()->max($column, $distinct);
    }

    /**
     * @param   array $tables (optional)
     *
     * @return  int
     */
    public function delete($tables = [])
    {
        return $this->buildDelete()->delete($tables);
    }
}
