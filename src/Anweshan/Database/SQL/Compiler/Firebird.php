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

class Firebird extends Compiler
{

    /**
     * Handle limits
     * @param int|null $limit
     * @param null $offset
     * @return string
     */
    protected function handleLimit($limit, $offset = null)
    {
        return ($limit <= 0) ? '' : ' TO ' . ($limit + (($offset < 0) ? 0 : $offset));
    }

    /**
     * Compiles OFFSET clause.
     *
     * @access  protected
     * @param   int $limit Offset
     * @param   int $offset Limit
     * @return  string
     */
    protected function handleOffset($offset, $limit = null)
    {
        return ($offset < 0) ? (($limit <= 0) ? '' : ' ROWS 1 ') : ' ROWS ' . ($offset + 1);
    }

    /**
     * Compiles a SELECT query.
     *
     * @access  public
     * @param   SQLStatement $select
     * @return  string
     */
    public function select(SQLStatement $select): string
    {
        $sql = $select->getDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= $this->handleInto($select->getIntoTable(), $select->getIntoDatabase());
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoins());
        $sql .= $this->handleWheres($select->getWheres());
        $sql .= $this->handleGroupings($select->getGroupBy());
        $sql .= $this->handleOrderings($select->getOrder());
        $sql .= $this->handleHavings($select->getHaving());
        $sql .= $this->handleOffset($select->getOffset(), $select->getLimit());
        $sql .= $this->handleLimit($select->getLimit(), $select->getOffset());

        return $sql;
    }
}
