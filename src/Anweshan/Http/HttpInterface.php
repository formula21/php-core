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
namespace Anweshan\Http;

/**
 * The interface HttpInterface is a parent interface of all futher HTTP-Header.
 * 
 * The interface defines fewer properties which lets us know the nature of the HTTP-Header and/or the API which is running the same.
 * 
 * @package Anweshan\Http
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
interface HttpInterface
{
    /**
     * The constant which signifies the http header was sent at RESPONSE.
     * @var integer
     */
    public const HEADER_TYPE_RESPONSE = 0;
    
    /**
     * The constant which signifies the http header was recieved at REQUEST.
     * @var integer
     */
    public const HEADER_TYPE_REQUEST = 1;
    
    /**
     * Check's if the header is of type RESPONSE.
     * @return bool Returns `true` is header is sent at RESPONSE.
     */
    public function isResponseHeader() : bool;
    
    /**
     * Check's if the header is of type REQUEST.
     * @return bool Returns `true` is header is recieved at REQUEST.
     */
    public function isRequestHeader() : bool;
    
    /**
     * Get's the header type as an integer based on the interface constants.
     * @return int The header type based on the interface constants.
     */
    public function getHeaderType() : int;
    
    
    /**
     * Get's an array of headers.
     * @param bool $keys Return a key:value header array. Else just return the values.
     * @return array The array of headers.
     */
    public function getHeaders(bool $keys = true) : array;
    
    
    /**
     * Gets a header
     * @param string $key The header to get/search.$this
     * @param bool $force_array Force the return to be array type if the `$key` is not found.
     * @return array|null Array `[header-name, header-value]` is returned. The return is null if the header is not found. The return is a null-array if forced.
     */
    public function getHeader(string $key, bool $force_array = false) : ?array;
    
    /**
     * Sets an array of headers.
     * @param array $headers
     * @return \Anweshan\Http\HttpInterface The instanceof the interface `HttpInterface`
     */
    public function setHeaders(?array $headers) : HttpInterface;
    
    
    /**
     * Sets a header.
     * @param string $key The name of the header.
     * @param mixed $value A value to the header.
     * @return bool|HttpInterface If the header was set, an instanceof the interface is given, otherwise false is returned.
     */
    public function setHeader(string $key, $value);
    
    
    /**
     * Sets the array of properties.
     * @param array $properties The array of properties.
     */
    public function set(?array $properties);
    
    
    /* MAGIC METHODS (Arguments) */
    
    /**
     * PHP Magic method to set a variable.
     * @param string $name The name of the variable
     * @param mixed $value The value to pass to the variable.
     * @link https://www.php.net/manual/language.oop5.overloading.php#object.set __set()
     */
    public function __set(string $name, $value);
    
    
    /**
     * PHP Magic method to get a variable.
     * @param string $name The name of the variable.
     * @return mixed The value of the variable.
     * @link https://www.php.net/manual/language.oop5.overloading.php#object.set __get()
     */
    public function __get(string $name);
}

