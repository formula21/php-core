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

class ColumnExpression
{
    /** @var  SQLStatement */
    protected $sql;

    /**
     * ColumnExpression constructor.
     * @param SQLStatement $statement
     */
    public function __construct(SQLStatement $statement)
    {
        $this->sql = $statement;
    }

    /**
     * Add a column
     *
     * @param   string|Closure $name Column's name
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function column($name, string $alias = null): self
    {
        if ($name instanceof Closure) {
            $expression = new Expression();
            $name($expression);
            $name = $expression;
        }
        $this->sql->addColumn($name, $alias);
        return $this;
    }

    /**
     * Add multiple columns at once
     *
     * @param   array $columns Columns
     *
     * @return  $this
     */
    public function columns(array $columns): self
    {
        foreach ($columns as $name => $alias) {
            if (is_string($name)) {
                $this->column($name, $alias);
            } else {
                $this->column($alias, null);
            }
        }
        return $this;
    }

    /**
     * Add a `COUNT` expression
     *
     * @param   string|array $column Column
     * @param   string $alias (optional) Column's alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function count($column = '*', string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->count($column, $distinct), $alias);
    }

    /**
     * Add an `AVG` expression
     *
     * @param   string $column Column
     * @param   string $alias (optional) Alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function avg($column, string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->avg($column, $distinct), $alias);
    }

    /**
     * Add a `SUM` expression
     *
     * @param   string $column Column
     * @param   string $alias (optional) Alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function sum($column, string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->sum($column, $distinct), $alias);
    }

    /**
     * Add a `MIN` expression
     *
     * @param   string $column Column
     * @param   string $alias (optional) Alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function min($column, string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->min($column, $distinct), $alias);
    }

    /**
     * Add a `MAX` expression
     *
     * @param   string $column Column
     * @param   string $alias (optional) Alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function max($column, string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->max($column, $distinct), $alias);
    }

    /**
     * Add a `UCASE` expression
     *
     * @param   string $column Column
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function ucase($column, string $alias = null): self
    {
        return $this->column((new Expression())->ucase($column), $alias);
    }

    /**
     * Add a `LCASE` expression
     *
     * @param   string $column Column
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function lcase($column, string $alias = null): self
    {
        return $this->column((new Expression())->lcase($column), $alias);
    }

    /**
     * Add a `MID` expression
     *
     * @param   string $column Column
     * @param   int $start (optional) Substring start
     * @param   string $alias (optional) Alias
     * @param   int $length (optional) Substring length
     *
     * @return  $this
     */
    public function mid($column, int $start = 1, string $alias = null, int $length = 0): self
    {
        return $this->column((new Expression())->mid($column, $start, $length), $alias);
    }

    /**
     * Add a `LEN` expression
     *
     * @param   string $column Column
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function len($column, string $alias = null): self
    {
        return $this->column((new Expression())->len($column), $alias);
    }

    /**
     * Add a `FORMAT` expression
     *
     * @param   string $column Column
     * @param   int $decimals (optional) Decimals
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function round($column, int $decimals = 0, string $alias = null): self
    {
        return $this->column((new Expression())->format($column, $decimals), $alias);
    }

    /**
     * Add a `FORMAT` expression
     *
     * @param   string $column Column
     * @param   int $format Decimals
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function format($column, int $format, string $alias = null): self
    {
        return $this->column((new Expression())->format($column, $format), $alias);
    }

    /**
     * Add a `NOW` expression
     *
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function now($alias = null): self
    {
        return $this->column((new Expression())->now(), $alias);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
    }
}
