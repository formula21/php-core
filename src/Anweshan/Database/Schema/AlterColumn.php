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

class AlterColumn extends BaseColumn
{
    /** @var string */
    protected $table;

    /**
     * AlterColumn constructor.
     * @param AlterTable $table
     * @param string $name
     * @param string|null $type
     */
    public function __construct(AlterTable $table, string $name, string $type = null)
    {
        $this->table = $table;
        parent::__construct($name, $type);
    }

    /**
     * @return  string
     */
    public function getTable(): string
    {
        return $this->table;
    }


    /**
     * @inheritdoc
     */
    public function defaultValue($value): BaseColumn
    {
        if ($this->get('handleDefault', true)) {
            return parent::defaultValue($value);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function autoincrement(): self
    {
        return $this->set('autoincrement', true);
    }
}
