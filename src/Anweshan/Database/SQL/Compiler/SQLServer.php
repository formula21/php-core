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
use Anweshan\Database\SQL\SQLStatement;

class SQLServer extends Compiler
{
    /** @var string Date format. */
    protected $dateFormat = 'Y-m-d H:i:s.0000000';

    /** @var string Wrapper used to escape table and column names. */
    protected $wrapper = '[%s]';

    /**
     * Compiles a SELECT query
     *
     * @param SQLStatement $select
     * @return string
     */
    public function select(SQLStatement $select): string
    {
        $limit = $select->getLimit();

        if ($limit <= 0) {
            return parent::select($select);
        }

        $offset = $select->getOffset();

        if ($offset < 0) {
            $sql = $select->getDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
            $sql .= 'TOP ' . $limit . ' ';
            $sql .= $this->handleColumns($select->getColumns());
            $sql .= $this->handleInto($select->getIntoTable(), $select->getIntoDatabase());
            $sql .= ' FROM ';
            $sql .= $this->handleTables($select->getTables());
            $sql .= $this->handleJoins($select->getJoins());
            $sql .= $this->handleWheres($select->getWheres());
            $sql .= $this->handleGroupings($select->getGroupBy());
            $sql .= $this->handleOrderings($select->getOrder());
            $sql .= $this->handleHavings($select->getHaving());

            return $sql;
        }

        $order = trim($this->handleOrderings($select->getOrder()));

        if (empty($order)) {
            $order = 'ORDER BY (SELECT 0)';
        }

        $sql = $select->getDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= ', ROW_NUMBER() OVER (' . $order . ') AS Anweshan_rownum';
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoins());
        $sql .= $this->handleWheres($select->getWheres());
        $sql .= $this->handleGroupings($select->getGroupBy());
        $sql .= $this->handleHavings($select->getHaving());

        $limit += $offset;
        $offset++;

        return 'SELECT * FROM (' . $sql . ') AS m1 WHERE Anweshan_rownum BETWEEN ' . $offset . ' AND ' . $limit;
    }

    /**
     * @param   SQLStatement $update
     *
     * @return  string
     */
    public function update(SQLStatement $update): string
    {
        $joins = $this->handleJoins($update->getJoins());
        $tables = $update->getTables();

        if ($joins !== '') {
            $joins = ' FROM ' . $this->handleTables($tables) . ' ' . $joins;
            $tables = array_values($tables);
        }

        $sql = 'UPDATE ';
        $sql .= $this->handleTables($tables);
        $sql .= $this->handleSetColumns($update->getColumns());
        $sql .= $joins;
        $sql .= $this->handleWheres($update->getWheres());

        return $sql;
    }
}
