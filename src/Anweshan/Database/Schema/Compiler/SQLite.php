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
namespace Anweshan\Database\Schema\Compiler;

use Anweshan\Database\Schema\{
    AlterTable, Compiler, BaseColumn, CreateTable
};

class SQLite extends Compiler
{
    /** @var string[] */
    protected $modifiers = ['nullable', 'default', 'autoincrement'];

    /** @var string */
    protected $autoincrement = 'AUTOINCREMENT';

    /** @var bool No primary key */
    private $nopk = false;

    /**
     * @inheritdoc
     */
    protected function handleTypeInteger(BaseColumn $column): string
    {
        return 'INTEGER';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeTime(BaseColumn $column): string
    {
        return 'DATETIME';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeTimestamp(BaseColumn $column): string
    {
        return 'DATETIME';
    }

    /**
     * @inheritdoc
     */
    public function handleModifierAutoincrement(BaseColumn $column): string
    {
        $modifier = parent::handleModifierAutoincrement($column);

        if ($modifier !== '') {
            $this->nopk = true;
            $modifier = 'PRIMARY KEY ' . $modifier;
        }

        return $modifier;
    }

    /**
     * @inheritdoc
     */
    public function handlePrimaryKey(CreateTable $schema): string
    {
        if ($this->nopk) {
            return '';
        }

        return parent::handlePrimaryKey($schema);
    }

    /**
     * @inheritdoc
     */
    protected function handleEngine(CreateTable $schema): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    protected function handleAddUnique(AlterTable $table, $data): string
    {
        return 'CREATE UNIQUE INDEX ' . $this->wrap($data['name']) . ' ON '
            . $this->wrap($table->getTableName()) . '(' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @inheritdoc
     */
    protected function handleAddIndex(AlterTable $table, $data): string
    {
        return 'CREATE INDEX ' . $this->wrap($data['name']) . ' ON '
            . $this->wrap($table->getTableName()) . '(' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @inheritdoc
     */
    public function currentDatabase(string $dsn): array
    {
        return substr($dsn, strpos($dsn, ':') + 1);
    }

    /**
     * @inheritdoc
     */
    public function getTables(string $database): array
    {
        $sql = 'SELECT ' . $this->wrap('name') . ' FROM ' . $this->wrap('sqlite_master')
            . ' WHERE type = ? ORDER BY ' . $this->wrap('name') . ' ASC';

        return [
            'sql' => $sql,
            'params' => ['table'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getColumns(string $database, string $table): array
    {
        return [
            'sql' => 'PRAGMA table_info(' . $this->wrap($table) . ')',
            'params' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function renameTable(string $current, string $new): array
    {
        return [
            'sql' => 'ALTER TABLE ' . $this->wrap($current) . ' RENAME TO ' . $this->wrap($new),
            'params' => [],
        ];
    }
}
