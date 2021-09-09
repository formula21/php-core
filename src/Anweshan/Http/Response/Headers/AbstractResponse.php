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
namespace Anweshan\Http\Response\Headers;

use Anweshan\Exception\InvalidArgumentException;
use Anweshan\Filesystem\FilesystemInterface;
use Anweshan\Http\HttpInterface;
use Anweshan\Http\Response\{
    Response, ResponseException
};

/**
 * The AbstractResponse class is the base class of all other Response Headers to be sent as Response by the server to the browser.
 * 
 * The class accepts an instanceof Response to generate the correct Response-Headers as required by the API.
 * 
 * @package Anweshan\Http
 * @subpackage Response\Headers
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
abstract class AbstractResponse
{
    /**
     * The instanceof Response.
     * @var \Anweshan\Http\Response\Response The instanceof Response.
     */
    protected $response = NULL;
       
    /**
     * The name of the header.
     * @var string Signifies the name of the header as set by specification.
     */
    protected const HEADER_NAME = '';
        
    /**
     * Initializes the members.
     * @param Response $response The response to be set.
     * @return void
     */
    public function __construct($response = null)
    {
        if($response != NULL){
            $this->setResponse($response);
        }
    }

    /**
     * Sets the instanceof Response.
     * @param Response $response The instanceof Response.
     * @return \Anweshan\Http\Response\Headers\AbstractResponse The instanceof itself.
     */
    public function setResponse($response) : AbstractResponse
    {        
        if(!($response instanceof HttpInterface)){
            throw new InvalidArgumentException("Invalid Response");
        }
        $this->response = $response;
        return $this;
    }
    
   
    /**
     * Get's the value of a key (or header).
     * @param mixed $key The key to the header or Header Name (any one).
     * @return mixed The value|key of the header.
     */
    public function get(string $key = self::HEADER_NAME, bool $value = true){
        if( strlen($key) == 0){
            return NULL;
        }
        
        if($value){
            return $this->response->$key;
        }
        
        return $key;
    }
    
    /**
     * Gets the instanceof Response.
     * @return Response|null The instanceof Response.
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * Gets the name of the headers.
     * @return string The name of the standard header.
     */
    public function __toString() : string
    {
        return self::HEADER_NAME;
    }
    
    /**
     * Runs the Response Headers API.
     * @param FilesystemInterface $file The instanceof {@link \Anweshan\Filesystem\FilesystemInterface FilesystemInterface} is passed as an argument.
     * @return FilesystemInterface|NULL The instanceof {@link \Anweshan\Filesystem\FilesystemInterface FilesystemInterface} is returned.
     */
    public function run(FilesystemInterface $file): ?FilesystemInterface{
        if($this->response == NULL || !($this->response instanceof HttpInterface)){
            throw new ResponseException("Response is invalid");
        }
        return $file;
    }
}

