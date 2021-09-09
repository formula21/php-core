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

use Anweshan\Filesystem\FilesystemInterface;
use Anweshan\Http\Http;

/**
 * The class ContentLength is an implementation of the Content-Length header sent at response by the server to the browser.
 * 
 * An example of the same would be **Content-Length:** 14478.
 * 
 * @package Anweshan\Http
 * @subpackage Response\Headers
 * 
 * @author Anweshan
 * @since 2021
 * @version 3
 * @license MIT
 */
class ContentLength extends AbstractResponse
{
    
    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::HEADER_NAME
     */
    protected const HEADER_NAME = 'Content-Length';
    
    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::run()
     */
    public function run(FilesystemInterface $file) : ?FilesystemInterface
    {
        $headername = self::HEADER_NAME;
        
        if(($file = parent::run($file)) && false !== Http::toFileInterface($file))      
        {
            $size = $file->getSize();
            if($size){
                $this->response->$headername = $size;
            }
        }
        return $file;
    }
    
    /**
     * Converts the value to given prefix.
     * 
     * The given prefixes:
     * - K or k: Kilo-bytes
     * - M or m: Mega-bytes
     * 
     * @param mixed $val Reference variable where the value is stored after conversion.
     * @param string $prefix [optional] The prefix to convert.
     * @return ContentLength The instanceof the ContentLength.
     */
    public function convert(&$val, string $prefix = 'K') : ContentLength{
        $val = null;
        if(false == ($val = $this->get())){
            $val = null;
        }
        if(!is_null($val) && strlen($prefix) == 1){
            $p = ord($prefix);
            switch($p){
                case ord('K'):
                case ord('k'):
                    $val = (float)($val / 1024.0);
                    break;
                case ord('M'):
                case ord('m'):
                    $val = $this->convert('K');
                    $val = (float)($val / 1024.0);
                    break;
                default:
                    $val = null;
                    break;
            }
        }
        return $this;
    }
   
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::get()
     */
    public function get(string $key = self::HEADER_NAME, bool $value = true){
        $get = parent::get($key, $value);
        if($value && !settype($get, 'int')){
            return false;
        }
        return $get;
            
    }
    
    /**
     * Returns the header name.
     * @return string
     */
    public static function getHeaderName(){
        return self::HEADER_NAME;
    }
}

