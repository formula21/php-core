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
    Compiler, BaseColumn, AlterTable, CreateTable
};

class PostgreSQL extends Compiler
{
    /** @var string[] */
    protected $modifiers = ['nullable', 'default'];

    /**
     * @inheritdoc
     */
    protected function handleTypeInteger(BaseColumn $column): string
    {
        $autoincrement = $column->get('autoincrement', false);

        switch ($column->get('size', 'normal')) {
            case 'tiny':
            case 'small':
                return $autoincrement ? 'SMALLSERIAL' : 'SMALLINT';
            case 'medium':
                return $autoincrement ? 'SERIAL' : 'INTEGER';
            case 'big':
                return $autoincrement ? 'BIGSERIAL' : 'BIGINT';
        }

        return $autoincrement ? 'SERIAL' : 'INTEGER';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeFloat(BaseColumn $column): string
    {
        return 'REAL';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeDouble(BaseColumn $column): string
    {
        return 'DOUBLE PRECISION';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeDecimal(BaseColumn $column): string
    {
        if (null !== $l = $column->get('length')) {
            if (null === $p = $column->get('precision')) {
                return 'DECIMAL (' . $this->value($l) . ')';
            }
            return 'DECIMAL (' . $this->value($l) . ', ' . $this->value($p) . ')';
        }
        return 'DECIMAL';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeBinary(BaseColumn $column): string
    {
        return 'BYTEA';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeTime(BaseColumn $column): string
    {
        return 'TIME(0) WITHOUT TIME ZONE';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeTimestamp(BaseColumn $column): string
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeDateTime(BaseColumn $column): string
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }

    /**
     * @inheritdoc
     */
    protected function handleIndexKeys(CreateTable $schema): array
    {
        $indexes = $schema->getIndexes();

        if (empty($indexes)) {
            return [];
        }

        $sql = [];

        $table = $schema->getTableName();

        foreach ($indexes as $name => $columns) {
            $sql[] = 'CREATE INDEX ' . $this->wrap($table . '_' . $name) . ' ON ' . $this->wrap($table) . '(' . $this->wrapArray($columns) . ')';
        }

        return $sql;
    }

    /**
     * @inheritdoc
     */
    protected function handleRenameColumn(AlterTable $table, $data): string
    {
        /** @var BaseColumn $column */
        $column = $data['column'];
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' RENAME COLUMN '
            . $this->wrap($data['from']) . ' TO ' . $this->wrap($column->getName());
    }

    /**
     * @inheritdoc
     */
    protected function handleAddIndex(AlterTable $table, $data): string
    {
        return 'CREATE INDEX ' . $this->wrap($table->getTableName() . '_' . $data['name']) . ' ON ' . $this->wrap($table->getTableName()) . ' (' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @inheritdoc
     */
    protected function handleDropIndex(AlterTable $table, $data): string
    {
        return 'DROP INDEX ' . $this->wrap($table->getTableName() . '_' . $data);
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
    public function getColumns(string $database, string $table): array
    {
        $sql = 'SELECT ' . $this->wrap('column_name') . ' AS ' . $this->wrap('name')
            . ', ' . $this->wrap('udt_name') . ' AS ' . $this->wrap('type')
            . ' FROM ' . $this->wrap('information_schema') . '.' . $this->wrap('columns')
            . ' WHERE ' . $this->wrap('table_schema') . ' = ? AND ' . $this->wrap('table_name') . ' = ? '
            . ' ORDER BY ' . $this->wrap('ordinal_position') . ' ASC';

        return [
            'sql' => $sql,
            'params' => [$database, $table],
        ];
    }

    /**
     * @inheritdoc
     */
    public function currentDatabase(string $dsn): array
    {
        return [
            'sql' => 'SELECT current_schema()',
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
