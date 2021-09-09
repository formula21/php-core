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

class HavingExpression
{
    /** @var  SQLStatement */
    protected $sql;

    /** @var    Having */
    protected $having;

    /** @var    string */
    protected $column;

    /** @var    string */
    protected $separator;

    /**
     * AggregateExpression constructor.
     * @param SQLStatement $statement
     */
    public function __construct(SQLStatement $statement)
    {
        $this->sql = $statement;
        $this->having = new Having($statement);
    }


    /**
     * @param string $column
     * @param string $separator
     * @return HavingExpression
     */
    public function init(string $column, string $separator): self
    {
        $this->column = $column;
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param bool $distinct
     * @return Having
     */
    public function count(bool $distinct = false): Having
    {
        $value = (new Expression())->count($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    /**
     * @param bool $distinct
     * @return Having
     */
    public function avg(bool $distinct = false): Having
    {
        $value = (new Expression())->avg($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    /**
     * @param bool $distinct
     * @return Having
     */
    public function sum(bool $distinct = false): Having
    {
        $value = (new Expression())->sum($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    /**
     * @param bool $distinct
     * @return Having
     */
    public function min(bool $distinct = false): Having
    {
        $value = (new Expression())->min($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    /**
     * @param bool $distinct
     * @return Having
     */
    public function max(bool $distinct = false): Having
    {
        $value = (new Expression())->max($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
        $this->having = new Having($this->sql);
    }
}
