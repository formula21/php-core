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

class Expression
{
    /** @var    array */
    protected $expressions = [];

    /**
     * Returns an array of expressions
     *
     * @return  array
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * @param   string $type
     * @param   mixed $value
     *
     * @return  $this
     */
    protected function addExpression(string $type, $value)
    {
        $this->expressions[] = [
            'type' => $type,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * @param   string $type
     * @param   string $name
     * @param   Closure|string $column
     * @param   array $arguments (optional)
     *
     * @return  $this
     */
    protected function addFunction(string $type, string $name, $column, array $arguments = []): self
    {
        if ($column instanceof Closure) {
            $expression = new Expression();
            $column($expression);
            $column = $expression;
        }

        $func = array_merge(['type' => $type, 'name' => $name, 'column' => $column], $arguments);

        return $this->addExpression('function', $func);
    }

    /**
     * @param   mixed $value
     *
     * @return  $this
     */
    public function column($value): self
    {
        return $this->addExpression('column', $value);
    }

    /**
     * @param   mixed $value
     *
     * @return  $this
     */
    public function op($value): self
    {
        return $this->addExpression('op', $value);
    }

    /**
     * @param   mixed $value
     * @return  $this
     */
    public function value($value): self
    {
        return $this->addExpression('value', $value);
    }

    /**
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function group(Closure $closure): self
    {
        $expression = new Expression();
        $closure($expression);
        return $this->addExpression('group', $expression);
    }

    /**
     * @param   array|string $tables
     *
     * @return  SelectStatement
     */
    public function from($tables): SelectStatement
    {
        $subquery = new Subquery();
        $this->addExpression('subquery', $subquery);
        return $subquery->from($tables);
    }

    /**
     * @param   string|array $column (optional)
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function count($column = '*', bool $distinct = false): self
    {
        if (!is_array($column)) {
            $column = [$column];
        }
        $distinct = $distinct || (count($column) > 1);
        return $this->addFunction('aggregateFunction', 'COUNT', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function sum($column, bool $distinct = false): self
    {
        return $this->addFunction('aggregateFunction', 'SUM', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function avg($column, bool $distinct = false): self
    {
        return $this->addFunction('aggregateFunction', 'AVG', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function max($column, bool $distinct = false): self
    {
        return $this->addFunction('aggregateFunction', 'MAX', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function min($column, bool $distinct = false): self
    {
        return $this->addFunction('aggregateFunction', 'MIN', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     *
     * @return  $this
     */
    public function ucase($column): self
    {
        return $this->addFunction('sqlFunction', 'UCASE', $column);
    }

    /**
     * @param   string $column
     *
     * @return  $this
     */
    public function lcase($column): self
    {
        return $this->addFunction('sqlFunction', 'LCASE', $column);
    }

    /**
     * @param   string $column
     * @param   int $start (optional)
     * @param   int $length (optional)
     *
     * @return  $this
     */
    public function mid($column, int $start = 1, int $length = 0): self
    {
        return $this->addFunction('sqlFunction', 'MID', $column, ['start' => $start, 'length' => $length]);
    }

    /**
     * @param   string $column
     *
     * @return  $this
     */
    public function len($column): self
    {
        return $this->addFunction('sqlFunction', 'LEN', $column);
    }

    /**
     * @param   string $column
     * @param   int $decimals (optional)
     *
     * @return  $this
     */
    public function round($column, int $decimals = 0): self
    {
        return $this->addFunction('sqlFunction', 'ROUND', $column, ['decimals' => $decimals]);
    }

    /**
     * @return  $this
     */
    public function now(): self
    {
        return $this->addFunction('sqlFunction', 'NOW', '');
    }

    /**
     * @param $column
     * @param $format
     * @return Expression
     */
    public function format($column, $format): self
    {
        return $this->addFunction('sqlFunction', 'FORMAT', $column, ['format' => $format]);
    }

    /**
     * @param   mixed $value
     *
     * @return  $this
     */
    public function __get($value)
    {
        return $this->addExpression('op', $value);
    }
}
