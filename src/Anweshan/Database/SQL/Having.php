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

class Having
{
    /** @var  SQLStatement */
    protected $sql;

    /** @var    string */
    protected $aggregate;

    /** @var    string */
    protected $separator;

    /**
     * Having constructor.
     * @param SQLStatement $statement
     */
    public function __construct(SQLStatement $statement)
    {
        $this->sql = $statement;
    }

    /**
     * @param   mixed $value
     * @param   string $operator
     * @param   boolean $is_column
     */
    protected function addCondition($value, string $operator, bool $is_column)
    {
        if ($is_column && is_string($value)) {
            $expr = new Expression();
            $value = $expr->column($value);
        }

        $this->sql->addHavingCondition($this->aggregate, $value, $operator, $this->separator);
    }

    /**
     * @param   string $aggregate
     * @param   string $separator
     *
     * @return  $this
     */
    public function init(string $aggregate, string $separator): self
    {
        $this->aggregate = $aggregate;
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function eq($value, bool $is_column = false)
    {
        $this->addCondition($value, '=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function ne($value, bool $is_column = false)
    {
        $this->addCondition($value, '!=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function lt($value, bool $is_column = false)
    {
        $this->addCondition($value, '<', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function gt($value, bool $is_column = false)
    {
        $this->addCondition($value, '>', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function lte($value, bool $is_column = false)
    {
        $this->addCondition($value, '<=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function gte($value, bool $is_column = false)
    {
        $this->addCondition($value, '>=', $is_column);
    }

    /**
     * @param   array|Closure $value
     */
    public function in($value)
    {
        $this->sql->addHavingInCondition($this->aggregate, $value, $this->separator, false);
    }

    /**
     * @param   array|Closure $value
     */
    public function notIn($value)
    {
        $this->sql->addHavingInCondition($this->aggregate, $value, $this->separator, true);
    }

    /**
     * @param   string|float|int $value1
     * @param   string|float|int $value2
     */
    public function between($value1, $value2)
    {
        $this->sql->addHavingBetweenCondition($this->aggregate, $value1, $value2, $this->separator, false);
    }

    /**
     * @param   string|float|int $value1
     * @param   string|float|int $value2
     */
    public function notBetween($value1, $value2)
    {
        $this->sql->addHavingBetweenCondition($this->aggregate, $value1, $value2, $this->separator, true);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
    }
}
