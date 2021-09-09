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

class WhereStatement
{
    /** @var SQLStatement */
    protected $sql;

    /** @var Where */
    protected $where;

    /**
     * WhereStatement constructor.
     * @param SQLStatement|null $statement
     */
    public function __construct(SQLStatement $statement = null)
    {
        if ($statement === null) {
            $statement = new SQLStatement();
        }

        $this->sql = $statement;
        $this->where = new Where($this, $statement);
    }

    /**
     * @param $column
     * @param string $separator
     * @return WhereStatement|Where
     */
    protected function addWhereCondition($column, string $separator = 'AND')
    {
        if ($column instanceof Closure) {
            $this->sql->addWhereConditionGroup($column, $separator);
            return $this;
        }
        return $this->where->init($column, $separator);
    }

    /**
     * @param Closure $select
     * @param string $separator
     * @param bool $not
     * @return WhereStatement
     */
    protected function addWhereExistCondition(Closure $select, string $separator = 'AND', bool $not = false): self
    {
        $this->sql->addWhereExistsCondition($select, $separator, $not);
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
     * @param $column
     * @return Where|Delete|Select|Update
     */
    public function where($column)
    {
        return $this->addWhereCondition($column);
    }

    /**
     * @param $column
     * @return Where|Delete|Select|Update
     */
    public function andWhere($column)
    {
        return $this->addWhereCondition($column);
    }

    /**
     * @param $column
     * @return Where|Delete|Select|Update
     */
    public function orWhere($column)
    {
        return $this->addWhereCondition($column, 'OR');
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function whereExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select);
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function andWhereExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select);
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function orWhereExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'OR');
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function whereNotExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function andWhereNotExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function orWhereNotExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'OR', true);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
        $this->where = new Where($this, $this->sql);
    }
}