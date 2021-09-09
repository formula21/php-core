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

class BaseColumn
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var array */
    protected $properties = [];

    /**
     * BaseColumn constructor.
     * @param string $name
     * @param string|null $type
     */
    public function __construct(string $name, string $type = null)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     */
    public function set(string $name, $value): self
    {
        $this->properties[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param string $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function size(string $value): self
    {
        $value = strtolower($value);

        if (!in_array($value, ['tiny', 'small', 'normal', 'medium', 'big'])) {
            return $this;
        }

        return $this->set('size', $value);
    }

    /**
     * @return $this
     */
    public function notNull(): self
    {
        return $this->set('nullable', false);
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function description(string $comment): self
    {
        return $this->set('description', $comment);
    }

    /**
     * @param $value
     * @return $this
     */
    public function defaultValue($value): self
    {
        return $this->set('default', $value);
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function unsigned(bool $value = true): self
    {
        return $this->set('unsigned', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function length($value): self
    {
        return $this->set('length', $value);
    }
}
