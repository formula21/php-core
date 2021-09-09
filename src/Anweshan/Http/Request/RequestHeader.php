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

namespace Anweshan\Http\Request;

use Anweshan\Util\Argument;
use Anweshan\Exception\InvalidArgumentException;
/**
 * The class RequestHeader is used to make an "argument" like for all request headers.
 *
 * @package Anweshan\Http
 * @subpackage Request
 *
 * @author Anweshan
 * @since 2021
 * @version 3
 * @license MIT
 */
final class RequestHeader{
    
    private $args;
    
    /**
     * Generalized Constructor.
     * @param array $headers
     */
    public function __construct(array $headers){
        $this->args = new Argument();
        $this->setHeaders($headers);
    }
    
    /**
     * Set the header.
     * @param string $headerName The name of header.
     * @param int|float|string $headerValue The value of the header.
     * @throws InvalidArgumentException If the expected `$headerValue` is not of the valid type.
     * @return \Anweshan\Http\Request\RequestHeader The instanceof itself for chaining.
     */
    public function setHeader(string $headerName, $headerValue){
        if(!is_numeric($headerValue) && !is_string($headerValue)){
            throw new InvalidArgumentException("The header value \"{$headerValue}\" is invalid.");
        }
        ($this->args)->{$headerName} = $headerValue;
        return $this;
    }
    
    /**
     * Get the value of the header.
     * @param string $headerName The name of the header.
     * @return string|int|float|NULL The value of the header.
     */
    public function getHeader(string $headerName){
        return ($this->args)->{$headerName};
    }
    
    /**
     * Sets the array of headers.
     * 
     * **Note:** For an invalid header-value, the exception is suppressed.  
     * 
     * @param array $headers The array of headers.
     * @return \Anweshan\Http\Request\RequestHeader
     */
    public function setHeaders(array $headers){
        foreach($headers as $k=>$v){
            try{
                $this->setHeader($k, $v);
            }catch(InvalidArgumentException $exp){
                // Suppress
            }
        }
        return $this;
    }
    
    /**
     * The array of headers.
     * @return array The array of headers.
     */
    public function getHeaders(){
       $args = get_object_vars($this->args);
       return $args;
    }
    
}