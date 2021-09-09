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

use Anweshan\Database\Schema\{
  CreateTable, AlterTable};
use Anweshan\Util\Argument;

class Schema
{
    /** @var    \Anweshan\Database\Connection   Connection. */
    protected $connection;

    /** @var    array   Table list. */
    protected $tableList;

    /** @var    string  Currently used database name. */
    protected $currentDatabase;

    /** @var    array   Column list */
    protected $columns = [];

    /**
     * Constructor
     *
     * @param   \Anweshan\Database\Connection $connection Connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the name of the currently used database
     *
     * @return  string
     * @throws \Exception
     */
    public function getCurrentDatabase()
    {
        if ($this->currentDatabase === null) {
            $compiler = $this->connection->schemaCompiler();
            $result = $compiler->currentDatabase($this->connection->getDSN());

            if (is_array($result)) {
                $this->currentDatabase = $this->connection->column($result['sql'], $result['params']);
            } else {
                $this->currentDatabase = $result;
            }
        }

        return $this->currentDatabase;
    }

    /**
     * Check if the specified table exists
     *
     * @param   string $table Table name
     * @param   boolean $clear (optional) Refresh table list
     *
     * @return  boolean
     * @throws \Exception
     */
    public function hasTable(string $table, bool $clear = false): bool
    {
        $list = $this->getTables($clear);
        return isset($list[strtolower($table)]);
    }

    /**
     * Get a list with all tables that belong to the currently used database
     *
     * @param   boolean $clear (optional) Refresh table list
     *
     * @return  string[]
     * @throws \Exception
     */
    public function getTables(bool $clear = false): array
    {
        if ($clear) {
            $this->tableList = null;
        }

        if ($this->tableList === null) {
            $compiler = $this->connection->schemaCompiler();

            $database = $this->getCurrentDatabase();

            $sql = $compiler->getTables($database);

            $results = $this->connection
            ->query($sql['sql'], $sql['params'])
            ->fetchNum()
            ->all();

            $this->tableList = [];

            foreach ($results as $result) {
                $this->tableList[strtolower($result[0])] = $result[0];
            }
        }

        return $this->tableList;
    }

    /**
     * Get a list with all columns that belong to the specified table
     *
     * @param   string $table
     * @param   boolean $clear (optional) Refresh column list
     * @param   boolean $names (optional) Return only the column names
     *
     * @return false|string[]
     * @throws \Exception
     */
    public function getColumns(string $table, bool $clear = false, bool $names = true)
    {
        if ($clear) {
            unset($this->columns[$table]);
        }

        if (!$this->hasTable($table, $clear)) {
            return false;
        }

        if (!isset($this->columns[$table])) {
            $compiler = $this->connection->schemaCompiler();

            $database = $this->getCurrentDatabase();

            $sql = $compiler->getColumns($database, $table);

            $results = $this->connection
            ->query($sql['sql'], $sql['params'])
            ->fetchAssoc()
            ->all();

            $columns = [];

            foreach ($results as &$col) {
                $columns[$col['name']] = [
                    'name' => $col['name'],
                    'type' => $col['type'],
                ];
            }

            $this->columns[$table] = $columns;
        }

        return $names ? array_keys($this->columns[$table]) : $this->columns[$table];
    }

    /**
     * Checks if the specified table has that column.
     * @param string $table The name of the table.
     * @param string $column_name The name of the column.
     * @param bool $clear If the name is to obtained from a clear cache.
     * @return bool
     */
    public function hasColumn(string $table, string $column_name, bool $clear = false){
        $columns = $this->getColumns($table, $clear, true);
        return ($columns && is_array($columns))?in_array($column_name, $columns):false;
    }

    /**
     * Gets the column and/or its properties.
     * @param string $table The name of the table.
     * @param string $column_name The name of the column.
     * @param bool $clear If the name is to obtained from a clear cache.
     * @param bool $arg
     * @return array|Argument
     */
    public function getColumn(string $table, string $column_name, bool $clear = false, bool $arg = false){
        if($this->hasColumn($table, $column_name, $clear)){
           $columns = $this->getColumns($table, $clear, false);
           return ($arg==false)?$columns[$column_name]:(new Argument($columns[$column_name]));
        }
        return null;
    }

    /**
     * Creates a new table
     *
     * @param   string $table Table name
     * @param   callable $callback A callback that will define table's fields and indexes
     * @throws \Exception
     */
    public function create(string $table, callable $callback)
    {
        $compiler = $this->connection->schemaCompiler();

        $schema = new CreateTable($table);

        $callback($schema);

        foreach ($compiler->create($schema) as $result) {
            $this->connection->command($result['sql'], $result['params']);
        }

        //clear table list
        $this->tableList = null;
    }

    /**
     * Alters a table's definition
     *
     * @param   string $table Table name
     * @param   callable $callback A callback that will add or remove fields or indexes
     * @throws \Exception
     */
    public function alter(string $table, callable $callback)
    {
        $compiler = $this->connection->schemaCompiler();

        $schema = new AlterTable($table);

        $callback($schema);

        unset($this->columns[strtolower($table)]);

        foreach ($compiler->alter($schema) as $result) {
            $this->connection->command($result['sql'], $result['params']);
        }
    }

    /**
     * Change a table's name
     *
     * @param   string $table The table
     * @param   string $name The new name of the table
     * @throws \Exception
     */
    public function renameTable(string $table, string $name)
    {
        $result = $this->connection->schemaCompiler()->renameTable($table, $name);
        $this->connection->command($result['sql'], $result['params']);
        $this->tableList = null;
        unset($this->columns[strtolower($table)]);
    }

    /**
     * Deletes a table
     *
     * @param   string $table Table name
     * @throws \Exception
     */
    public function drop(string $table)
    {
        $compiler = $this->connection->schemaCompiler();

        $result = $compiler->drop($table);

        $this->connection->command($result['sql'], $result['params']);

        //clear table list
        $this->tableList = null;
        unset($this->columns[strtolower($table)]);
    }

    /**
     * Deletes all records from a table
     *
     * @param   string $table Table name
     * @throws \Exception
     */
    public function truncate(string $table)
    {
        $compiler = $this->connection->schemaCompiler();

        $result = $compiler->truncate($table);

        $this->connection->command($result['sql'], $result['params']);
    }
}
