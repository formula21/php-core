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

class SelectStatement extends BaseStatement
{
    /** @var    HavingStatement */
    protected $have;

    /**
     * SelectStatement constructor.
     * @param string|array $tables
     * @param SQLStatement|null $statement
     */
    public function __construct($tables, SQLStatement $statement = null)
    {
        parent::__construct($statement);

        if (!is_array($tables)) {
            $tables = [$tables];
        }

        $this->sql->addTables($tables);
        $this->have = new HavingStatement($this->sql);
    }

    /**
     * @param string $table
     * @param string|null $database
     * @return SelectStatement
     */
    public function into(string $table, string $database = null): self
    {
        $this->sql->setInto($table, $database);
        return $this;
    }


    /**
     * @param bool $value
     * @return SelectStatement
     */
    public function distinct(bool $value = true): self
    {
        $this->sql->setDistinct($value);
        return $this;
    }

    /**
     * @param   string|array $columns
     *
     * @return  $this
     */
    public function groupBy($columns): self
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->sql->addGroupBy($columns);
        return $this;
    }

    /**
     * @param   string $column
     * @param   Closure $value (optional)
     *
     * @return  $this
     */
    public function having($column, Closure $value = null): self
    {
        $this->have->having($column, $value);
        return $this;
    }

    /**
     * @param   string $column
     * @param   Closure $value (optional)
     *
     * @return  $this
     */
    public function andHaving($column, Closure $value = null): self
    {
        $this->have->andHaving($column, $value);
        return $this;
    }

    /**
     * @param   string $column
     * @param   Closure $value (optional)
     *
     * @return  $this
     */
    public function orHaving($column, Closure $value = null): self
    {
        $this->have->orHaving($column, $value);
        return $this;
    }

    /**
     * @param $columns
     * @param string $order
     * @param string|null $nulls
     * @return SelectStatement
     */
    public function orderBy($columns, string $order = 'ASC', string $nulls = null): self
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->sql->addOrder($columns, $order, $nulls);
        return $this;
    }

    /**
     * @param int $value
     * @return SelectStatement
     */
    public function limit(int $value): self
    {
        $this->sql->setLimit($value);
        return $this;
    }

    /**
     * @param int $value
     * @return SelectStatement
     */
    public function offset(int $value): self
    {
        $this->sql->setOffset($value);
        return $this;
    }

    /**
     * @param   string|array|Closure $columns
     *
     */
    public function select($columns = [])
    {
        $expr = new ColumnExpression($this->sql);

        if ($columns instanceof Closure) {
            $columns($expr);
        } else {
            if (!is_array($columns)) {
                $columns = [$columns];
            }
            $expr->columns($columns);
        }
    }

    /**
     * @param   string $name
     */
    public function column(string $name)
    {
        (new ColumnExpression($this->sql))->column($name);
    }

    /**
     * @param   string $column (optional)
     * @param   bool $distinct (optional)
     */
    public function count($column = '*', bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->count($column, null, $distinct);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     */
    public function avg(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->avg($column, null, $distinct);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     */
    public function sum(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->sum($column, null, $distinct);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     */
    public function min(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->min($column, null, $distinct);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     */
    public function max(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->max($column, null, $distinct);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        parent::__clone();
        $this->have = new HavingStatement($this->sql);
    }
}
