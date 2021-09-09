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
namespace Anweshan\Http\Response;

use Anweshan\Http\HttpInterface;
use Anweshan\Util\Argument;

/**
 * The class Response is the independent handler of all Header Api, as an instanceof this class or a child class is required for handling the api.
 * 
 * The class is an implementation (and thus an `instanceof`) of HttpInterface, and defines all methods and properties to handle, store, and send the response headers to the browser. It is used as a parameter and a starting point of the ResponseInterface. Though the class itself cannot be initiated
 * 
 * @package Anweshan\Http
 * @subpackage Response
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Response extends Argument implements HttpInterface{
    
    
    /**
     * @param array|NULL $headers The headers to be pre-initialized as response.
     * @param array|NULL $array The array of properties (other than headers) have a value.
     */
    public function __construct(?array $headers = NULL, ?array $array = NULL){
        parent::__construct($array);
        $this->setHeaders($headers);
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\HttpInterface::setHeaders()
     */
    public function setHeaders(?array $headers) : HttpInterface{
        if($headers && is_array($headers) && count($headers) > 0){
            foreach(array_keys($headers) as $k){
                $this->setHeader($k, $headers[$k], true);
            }
        }
        return $this;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\HttpInterface::setHeader()
     */
    public function setHeader(string $key, $value){
        if(strlen($key) > 0){
            $this->$key = $value;
            return $this;
        }
        return false;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\HttpInterface::getHeaders()
     */
    public function getHeaders(bool $keys = true) : array{
       
        $h = self::get_object_vars($this);
        if( ($l = count($h)) > 0){
            foreach(array_keys($h) as $k){
                if(is_string($k) && strlen($k) > 0){
                    $h[] = $this->getHeader($k);
                    if(!is_array($h[array_key_last($h)])){
                        unset($h[array_key_last($h)]);
                    }
                }
            }
          
            if(count($h) != $l){
                $h = array_splice($h, $l, count($h));
            }else{
                $h = [];
            }
            
            $h = array_values($h);
            
            if($keys){
                // Transforming key=>value
                $l = count($h);
                foreach($h as $k=>$v){
                    if((is_string($v[0]) || is_int($v[0])) && !is_null($v[1])){
                        $h[$v[0]] = $v[1];
                    }else{
                        unset($h[$v[0]]);
                    }
                }
                if($l != count($h))
                    $h = array_splice($h, $l, count($h));
            }
        }
        
        return $h;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\HttpInterface::getHeader()
     */
    public function getHeader(string $key, bool $force_array = false) : ?array
    {
        $header = null;
        if($force_array){
            $header = [null, null];
        }
        
        if(array_key_exists($key, self::get_object_vars($this))){
            $header = [$key, $this->$key];   
        }
        
        
        return $header;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\HttpInterface::isResponseHeader()
     */
    public final function isResponseHeader() : bool{
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\HttpInterface::isRequestHeader()
     */
    public final function isRequestHeader() : bool{
        return false;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\HttpInterface::getHeaderType()
     */
    public final function getHeaderType() : int{
        return HttpInterface::HEADER_TYPE_RESPONSE;
    }
    
    /**
     * Alias of {@link https://www.php.net/manual/function.get-object-vars.php get_object_vars()}.
     * 
     * The sole property of the function is to "exclude" the properties, whose references are given in an `exclude` (if any). 
     * 
     * @param Response $response The `instanceof` of Response.
     * @return array Returns an array of properties of the object.
     */
    private static function get_object_vars(Response $response){
        $h = \get_object_vars($response?:($response = new static()));
        if(isset($response->exclude) && is_array($response->exclude)){
            foreach($response->exclude as $v){
                if(array_key_exists($v, $h)){
                    unset($h[$v]);
                }
            }
        }
        return $h;
    }
    
    
}

