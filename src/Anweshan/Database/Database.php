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

namespace Anweshan\Database;

use Anweshan\Database\SQL\InsertStatement;
use Anweshan\Database\SQL\Query as QueryCommand;
use Anweshan\Database\SQL\Insert as InsertCommand;
use Anweshan\Database\SQL\Update as UpdateCommand;

class Database
{
    /** @var   Connection   Connection instance. */
    protected $connection;
    
    /** @var    Schema       Schema instance. */
    protected $schema;
    
    /**
     * Constructor
     *
     * @param   Connection $connection Connection instance.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * Database connection
     *
     * @return   Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }
    
    /**
     * Returns the query log for this database.
     *
     * @return array
     */
    public function getLog()
    {
        return $this->connection->getLog();
    }
    
    /**
     * Execute a query in order to fetch or to delete records.
     *
     * @param   string|array $tables Table name or an array of tables
     *
     * @return  QueryCommand
     */
    public function from($tables): QueryCommand
    {
        return new QueryCommand($this->connection, $tables);
    }
    
    /**
     * Insert new records into a table.
     *
     * @param   array $values An array of values.
     *
     * @return  InsertCommand|InsertStatement
     */
    public function insert(array $values): InsertCommand
    {
        return (new InsertCommand($this->connection))->insert($values);
    }
    
    /**
     * Update records.
     *
     * @param   string $table Table name
     *
     * @return  UpdateCommand
     */
    public function update($table): UpdateCommand
    {
        return new UpdateCommand($this->connection, $table);
    }
    
    /**
     * The associated schema instance.
     *
     * @return  Schema
     */
    public function schema(): Schema
    {
        if ($this->schema === null) {
            $this->schema = $this->connection->getSchema();
        }
        
        return $this->schema;
    }
    
    /**
     * Performs a transaction
     *
     * @param callable $query
     * @param mixed|null $default
     * @return mixed|null
     * @throws \PDOException
     */
    public function transaction(callable $query, $default = null)
    {
        return $this->connection->transaction($query, $this, $default);
    }
}
