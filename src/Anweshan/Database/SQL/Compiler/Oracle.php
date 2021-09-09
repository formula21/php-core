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
namespace Anweshan\Database\SQL\Compiler;

use Anweshan\Database\SQL\Compiler;
use Anweshan\Database\SQL\Expression;
use Anweshan\Database\SQL\SQLStatement;

class Oracle extends Compiler
{

    /**
     * Compiles a SELECT query.
     *
     * @param   SQLStatement $select
     *
     * @return  string
     */
    public function select(SQLStatement $select): string
    {
        $limit = $select->getLimit();

        if ($limit <= 0) {
            return parent::select($select);
        }

        $sql = $select->getDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoins());
        $sql .= $this->handleWheres($select->getWheres());
        $sql .= $this->handleGroupings($select->getGroupBy());
        $sql .= $this->handleOrderings($select->getOrder());
        $sql .= $this->handleHavings($select->getHaving());

        $offset = $select->getOffset();

        if ($offset < 0) {
            return 'SELECT * FROM (' . $sql . ') M1 WHERE ROWNUM <= ' . $limit;
        }

        $limit += $offset;
        $offset++;

        return 'SELECT * FROM (SELECT M1.*, ROWNUM AS Anweshan_ROWNUM FROM (' . $sql . ') M1 WHERE ROWNUM <= ' . $limit . ') WHERE Anweshan_ROWNUM >= ' . $offset;
    }

    /**
     * @param   mixed $value
     *
     * @return  string
     */
    protected function wrap($value)
    {
        if ($value instanceof Expression) {
            return $this->handleExpressions($value->getExpressions());
        }

        $wrapped = [];

        foreach (explode('.', $value) as $segment) {
            if ($segment == '*') {
                $wrapped[] = $segment;
            } else {
                $wrapped[] = sprintf($this->wrapper, strtoupper($segment));
            }
        }

        return implode('.', $wrapped);
    }

    /**
     * @param   array $ordering
     *
     * @return  string
     */
    protected function handleOrderings(array $ordering)
    {
        if (empty($ordering)) {
            return '';
        }

        $sql = [];

        foreach ($ordering as $order) {
            if ($order['nulls'] !== null) {
                $sql[] = $this->columns($order['columns']) . ' ' . $order['order'] . ' ' . $order['nulls'];
            } else {
                $sql[] = $this->columns($order['columns']) . ' ' . $order['order'];
            }
        }

        return ' ORDER BY ' . implode(', ', $sql);
    }
}
