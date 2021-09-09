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
    Compiler, BaseColumn, AlterTable
};

class MySQL extends Compiler
{
    /** @var string */
    protected $wrapper = '`%s`';

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
                return 'MEDIUMINT';
            case 'big':
                return 'BIGINT';
        }

        return 'INT';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeDecimal(BaseColumn $column): string
    {
        if (null !== $l = $column->get('length')) {
            if (null === $p = $column->get('precision')) {
                return 'DECIMAL(' . $this->value($l) . ')';
            }
            return 'DECIMAL(' . $this->value($l) . ', ' . $this->value($p) . ')';
        }
        return 'DECIMAL';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeBoolean(BaseColumn $column): string
    {
        return 'TINYINT(1)';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeText(BaseColumn $column): string
    {
        switch ($column->get('size', 'normal')) {
            case 'tiny':
            case 'small':
                return 'TINYTEXT';
            case 'medium':
                return 'MEDIUMTEXT';
            case 'big':
                return 'LONGTEXT';
        }

        return 'TEXT';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeBinary(BaseColumn $column): string
    {
        switch ($column->get('size', 'normal')) {
            case 'tiny':
            case 'small':
                return 'TINYBLOB';
            case 'medium':
                return 'MEDIUMBLOB';
            case 'big':
                return 'LONGBLOB';
        }

        return 'BLOB';
    }

    /**
     * @inheritdoc
     */
    protected function handleDropPrimaryKey(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP PRIMARY KEY';
    }

    /**
     * @inheritdoc
     */
    protected function handleDropUniqueKey(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP INDEX ' . $this->wrap($data);
    }

    /**
     * @inheritdoc
     */
    protected function handleDropIndex(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP INDEX ' . $this->wrap($data);
    }

    /**
     * @inheritdoc
     */
    protected function handleDropForeignKey(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP FOREIGN KEY ' . $this->wrap($data);
    }

    /**
     * @inheritdoc
     */
    protected function handleSetDefaultValue(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER '
            . $this->wrap($data['column']) . ' SET DEFAULT ' . $this->value($data['value']);
    }

    /**
     * @inheritdoc
     */
    protected function handleDropDefaultValue(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER ' . $this->wrap($data) . ' DROP DEFAULT';
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function handleRenameColumn(AlterTable $table, $data): string
    {
        $table_name = $table->getTableName();
        $column_name = $data['from'];
        /** @var BaseColumn $column */
        $column = $data['column'];
        $new_name = $column->getName();
        $columns = $this->connection->getSchema()->getColumns($table_name, false, false);
        $column_type = isset($columns[$column_name]) ? $columns[$column_name]['type'] : 'integer';

        return 'ALTER TABLE ' . $this->wrap($table_name) . ' CHANGE ' . $this->wrap($column_name)
            . ' ' . $this->wrap($new_name) . ' ' . $column_type;
    }
}
