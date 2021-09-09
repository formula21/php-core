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

class Where
{
    /** @var    string */
    protected $column;

    /** @var    string */
    protected $separator;

    /** @var  SQLStatement */
    protected $sql;

    /** @var  WhereStatement */
    protected $statement;

    public function __construct(WhereStatement $statement, SQLStatement $sql)
    {
        $this->sql = $sql;
        $this->statement = $statement;
    }

    /**
     * @param   string $column
     * @param   string $separator
     * @return  Where
     */
    public function init(string $column, string $separator): self
    {
        $this->column = $column;
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param   mixed $value
     * @param   string $operator
     * @param   bool $isColumn (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addCondition($value, string $operator, bool $isColumn = false): WhereStatement
    {
        if ($isColumn && is_string($value)) {
            $value = function (Expression $expr) use ($value) {
                $expr->column($value);
            };
        }
        $this->sql->addWhereCondition($this->column, $value, $operator, $this->separator);
        return $this->statement;
    }

    /**
     * @param   int|float|string $value1
     * @param   int|float|string $value2
     * @param   bool $not
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addBetweenCondition($value1, $value2, bool $not): WhereStatement
    {
        $this->sql->addWhereBetweenCondition($this->column, $value1, $value2, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   string $pattern
     * @param   bool $not
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addLikeCondition(string $pattern, bool $not): WhereStatement
    {
        $this->sql->addWhereLikeCondition($this->column, $pattern, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   mixed $value
     * @param   bool $not
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addInCondition($value, bool $not): WhereStatement
    {
        $this->sql->addWhereInCondition($this->column, $value, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   bool $not
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addNullCondition(bool $not): WhereStatement
    {
        $this->sql->addWhereNullCondition($this->column, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function is($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function isNot($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '!=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function lessThan($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '<', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function greaterThan($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '>', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function atLeast($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '>=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function atMost($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '<=', $is_column);
    }

    /**
     * @param   int|float|string $value1
     * @param   int|float|string $value2
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function between($value1, $value2): WhereStatement
    {
        return $this->addBetweenCondition($value1, $value2, false);
    }

    /**
     * @param   int|float|string $value1
     * @param   int|float|string $value2
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function notBetween($value1, $value2): WhereStatement
    {
        return $this->addBetweenCondition($value1, $value2, true);
    }

    /**
     * @param   string $value
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function like(string $value): WhereStatement
    {
        return $this->addLikeCondition($value, false);
    }

    /**
     * @param   string $value
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function notLike(string $value): WhereStatement
    {
        return $this->addLikeCondition($value, true);
    }

    /**
     * @param   array|Closure $value
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function in($value): WhereStatement
    {
        return $this->addInCondition($value, false);
    }

    /**
     * @param   array|Closure $value
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function notIn($value): WhereStatement
    {
        return $this->addInCondition($value, true);
    }

    /**
     * @return  WhereStatement|Select|Delete|Update
     */
    public function isNull(): WhereStatement
    {
        return $this->addNullCondition(false);
    }

    /**
     * @return  WhereStatement|Select|Delete|Update
     */
    public function notNull(): WhereStatement
    {
        return $this->addNullCondition(true);
    }
    //Aliases

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function eq($value, bool $is_column = false): WhereStatement
    {
        return $this->is($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function ne($value, bool $is_column = false): WhereStatement
    {
        return $this->isNot($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function lt($value, bool $is_column = false): WhereStatement
    {
        return $this->lessThan($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function gt($value, bool $is_column = false): WhereStatement
    {
        return $this->greaterThan($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function gte($value, bool $is_column = false): WhereStatement
    {
        return $this->atLeast($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function lte($value, bool $is_column = false): WhereStatement
    {
        return $this->atMost($value, $is_column);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
        $this->statement = new WhereStatement($this->sql);
    }
}
