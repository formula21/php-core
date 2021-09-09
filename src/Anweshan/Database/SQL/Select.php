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
use Anweshan\Database\ResultSet;

class Select extends SelectStatement
{
    protected $connection;

    /**
     * Select constructor.
     * @param Connection $connection
     * @param array|string $tables
     * @param SQLStatement|null $statement
     */
    public function __construct(Connection $connection, $tables, SQLStatement $statement = null)
    {
        parent::__construct($tables, $statement);
        $this->connection = $connection;
    }

    /**
     * @param   string|array|Closure $columns (optional)
     *
     * @return  ResultSet
     */
    public function select($columns = [])
    {
        parent::select($columns);
        $compiler = $this->connection->getCompiler();
        return $this->connection->query($compiler->select($this->sql), $compiler->getParams());
    }

    /**
     * @param   string $name
     *
     * @return  mixed|false
     */
    public function column(string $name)
    {
        parent::column($name);
        return $this->getColumnResult();
    }

    /**
     * @param   string $column (optional)
     * @param   bool $distinct (optional)
     *
     * @return  int
     */
    public function count($column = '*', bool $distinct = false)
    {
        parent::count($column, $distinct);
        return $this->getColumnResult();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  int|float
     */
    public function avg(string $column, bool $distinct = false)
    {
        parent::avg($column, $distinct);
        return $this->getColumnResult();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  int|float
     */
    public function sum(string $column, bool $distinct = false)
    {
        parent::sum($column, $distinct);
        return $this->getColumnResult();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  int|float
     */
    public function min(string $column, bool $distinct = false)
    {
        parent::min($column, $distinct);
        return $this->getColumnResult();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  int|float
     */
    public function max(string $column, bool $distinct = false)
    {
        parent::max($column, $distinct);
        return $this->getColumnResult();
    }

    protected function getColumnResult()
    {
        $compiler = $this->connection->getCompiler();
        return $this->connection->column($compiler->select($this->sql), $compiler->getParams());
    }
}
