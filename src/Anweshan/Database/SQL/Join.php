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

class Join
{
    /** @var    array */
    protected $conditions = [];

    /**
     * @param   string $column1
     * @param   string $column2
     * @param   string $operator
     * @param   string $separator
     *
     * @return $this
     */
    protected function addJoinCondition($column1, $column2, $operator, $separator)
    {
        if ($column1 instanceof Closure) {
            $join = new Join();
            $column1($join);
            $this->conditions[] = [
                'type' => 'joinNested',
                'join' => $join,
                'separator' => $separator,
            ];
        } else {
            $this->conditions[] = [
                'type' => 'joinColumn',
                'column1' => $column1,
                'column2' => $column2,
                'operator' => $operator,
                'separator' => $separator,
            ];
        }

        return $this;
    }

    /**
     * @return  array
     */
    public function getJoinConditions()
    {
        return $this->conditions;
    }

    /**
     * @param   string $column1
     * @param   string $column2 (optional)
     * @param   string $operator (optional)
     *
     * @return  $this
     */
    public function on($column1, $column2 = null, $operator = '=')
    {
        return $this->addJoinCondition($column1, $column2, $operator, 'AND');
    }

    /**
     * @param   string $column1
     * @param   string $column2 (optional)
     * @param   string $operator (optional)
     *
     * @return  $this
     */
    public function andOn($column1, $column2 = null, $operator = '=')
    {
        return $this->on($column1, $column2, $operator);
    }

    /**
     * @param   string $column1
     * @param   string $column2 (optional)
     * @param   string $operator (optional)
     *
     * @return  $this
     */
    public function orOn($column1, $column2 = null, $operator = '=')
    {
        return $this->addJoinCondition($column1, $column2, $operator, 'OR');
    }
}
