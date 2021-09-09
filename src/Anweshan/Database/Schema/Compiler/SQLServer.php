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

class SQLServer extends Compiler
{
    /** @var string */
    protected $wrapper = '[%s]';

    /** @var string[] */
    protected $modifiers = ['nullable', 'default', 'autoincrement'];

    /** @var string */
    protected $autoincrement = 'IDENTITY';

    /**
     * @inheritdoc
     */
    protected function handleTypeInteger(BaseColumn $column): string
    {
        switch ($column->get('size', 'normal')) {
            case 'tiny':
                return 'TINYINT';
            case 'small':
                return 'SMALLINT';
            case 'medium':
                return 'INTEGER';
            case 'big':
                return 'BIGINT';
        }

        return 'INTEGER';
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
    protected function handleTypeBoolean(BaseColumn $column): string
    {
        return 'BIT';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeString(BaseColumn $column): string
    {
        return 'NVARCHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeFixed(BaseColumn $column): string
    {
        return 'NCHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeText(BaseColumn $column): string
    {
        return 'NVARCHAR(max)';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeBinary(BaseColumn $column): string
    {
        return 'VARBINARY(max)';
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
    protected function handleRenameColumn(AlterTable $table, $data): string
    {
        /** @var BaseColumn $column */
        $column = $data['column'];
        return 'sp_rename ' . $this->wrap($table->getTableName()) . '.' . $this->wrap($data['from']) . ', '
            . $this->wrap($column->getName()) . ', COLUMN';
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
    public function renameTable(string $current, string $new): array
    {
        return [
            'sql' => 'sp_rename ' . $this->wrap($current) . ', ' . $this->wrap($new),
            'params' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function currentDatabase(string $dsn): array
    {
        return [
            'sql' => 'SELECT SCHEMA_NAME()',
            'params' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getColumns(string $database, string $table): array
    {
        $sql = 'SELECT ' . $this->wrap('column_name') . ' AS ' . $this->wrap('name')
            . ', ' . $this->wrap('data_type') . ' AS ' . $this->wrap('type')
            . ' FROM ' . $this->wrap('information_schema') . '.' . $this->wrap('columns')
            . ' WHERE ' . $this->wrap('table_schema') . ' = ? AND ' . $this->wrap('table_name') . ' = ? '
            . ' ORDER BY ' . $this->wrap('ordinal_position') . ' ASC';

        return [
            'sql' => $sql,
            'params' => [$database, $table],
        ];
    }
}
