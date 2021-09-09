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

use Anweshan\Database\Connection;

class Update extends UpdateStatement
{
    /** @var    Connection */
    protected $connection;

    /**
     * Update constructor.
     * @param Connection $connection
     * @param string|array $table
     * @param SQLStatement|null $statement
     */
    public function __construct(Connection $connection, $table, SQLStatement $statement = null)
    {
        parent::__construct($table, $statement);
        $this->connection = $connection;
    }

    /**
     * @param   string $sign
     * @param   string|array $columns
     * @param   int $value
     *
     * @return  int
     */
    protected function incrementOrDecrement(string $sign, $columns, $value)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $values = [];

        foreach ($columns as $k => $v) {
            if (is_numeric($k)) {
                $values[$v] = function (Expression $expr) use ($sign, $v, $value) {
                    $expr->column($v)->{$sign}->value($value);
                };
            } else {
                $values[$k] = function (Expression $expr) use ($sign, $k, $v) {
                    $expr->column($k)->{$sign}->value($v);
                };
            }
        }

        return $this->set($values);
    }

    /**
     * @param   string|array $column
     * @param   int $value (optional)
     *
     * @return  int
     */
    public function increment($column, $value = 1)
    {
        return $this->incrementOrDecrement('+', $column, $value);
    }

    /**
     * @param   string|array $column
     * @param   int $value (optional)
     *
     * @return  int
     */
    public function decrement($column, $value = 1)
    {
        return $this->incrementOrDecrement('-', $column, $value);
    }

    /**
     * @param   array $columns
     *
     * @return  int
     */
    public function set(array $columns)
    {
        parent::set($columns);
        $compiler = $this->connection->getCompiler();
        return $this->connection->count($compiler->update($this->sql), $compiler->getParams());
    }
}
