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

class HavingStatement
{
    /** @var    SQLStatement */
    protected $sql;

    /** @var    HavingExpression */
    protected $expression;

    /**
     * HavingStatement constructor.
     * @param SQLStatement|null $statement
     */
    public function __construct(SQLStatement $statement = null)
    {
        if ($statement === null) {
            $statement = new SQLStatement();
        }
        $this->sql = $statement;
        $this->expression = new HavingExpression($statement);
    }

    /**
     * @param   string|Closure $column
     * @param   Closure $value
     * @param   string $separator
     *
     * @return  $this
     */
    protected function addCondition($column, Closure $value = null, $separator): self
    {
        if ($column instanceof Closure) {
            $this->sql->addHavingGroupCondition($column, $separator);
        } else {
            $value($this->expression->init($column, $separator));
        }
        return $this;
    }

    /**
     * @return SQLStatement
     */
    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    /**
     * @param   string $column
     * @param   Closure $value (optional)
     *
     * @return  $this
     */
    public function having($column, Closure $value = null): self
    {
        return $this->addCondition($column, $value, 'AND');
    }

    /**
     * @param   string $column
     * @param   Closure $value (optional)
     *
     * @return  $this
     */
    public function andHaving($column, Closure $value = null): self
    {
        return $this->having($column, $value);
    }

    /**
     * @param   string $column
     * @param   Closure $value (optional)
     *
     * @return  $this
     */
    public function orHaving($column, Closure $value = null): self
    {
        return $this->addCondition($column, $value, 'OR');
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
        $this->expression = new HavingExpression($this->sql);
    }
}
