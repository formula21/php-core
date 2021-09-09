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
namespace Anweshan\Database\Schema;

class ForeignKey
{
    /** @var string */
    protected $refTable;

    /** @var string[] */
    protected $refColumns;

    /** @var array */
    protected $actions = [];

    /** @var string[] */
    protected $columns;

    /**
     * ForeignKey constructor.
     * @param string[] $columns
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @param string $on
     * @param string $action
     * @return $this
     */
    protected function addAction(string $on, string $action): self
    {
        $action = strtoupper($action);

        if (!in_array($action, ['RESTRICT', 'CASCADE', 'NO ACTION', 'SET NULL'])) {
            return $this;
        }

        $this->actions[$on] = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferencedTable(): string
    {
        return $this->refTable;
    }

    /**
     * @return string[]
     */
    public function getReferencedColumns(): array
    {
        return $this->refColumns;
    }

    /**
     * @return string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param string $table
     * @param string ...$columns
     * @return $this
     */
    public function references(string $table, string ...$columns): self
    {
        $this->refTable = $table;
        $this->refColumns = $columns;
        return $this;
    }

    /**
     * @param string $action
     * @return $this
     */
    public function onDelete(string $action): self
    {
        return $this->addAction('ON DELETE', $action);
    }

    /**
     * @param string $action
     * @return $this
     */
    public function onUpdate(string $action): self
    {
        return $this->addAction('ON UPDATE', $action);
    }
}
