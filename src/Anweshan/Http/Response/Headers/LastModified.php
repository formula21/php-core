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
 * The class LastModified is an implementation of the Last-Modified header sent at response by the server to the browser.
 * 
 * An example of the same would be **Last-Modified:** Thu, 01 Jan 1970 00:00:00 GMT.
 * 
 * @package Anweshan\Http
 * @subpackage Response\Headers
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class LastModified extends AbstractResponse
{
    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::HEADER_NAME
     */
    protected const HEADER_NAME = 'Last-Modified';
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::run()
     */
    public function run(FilesystemInterface $file) : ?FilesystemInterface
    {
        if(($file = parent::run($file)) && false !== Http::toFileInterface($file))
        {
            $time = $file->getLastModifiedTimestamp();
            if($time && $time > 0){
                $time = Http::getGMT($time);
                if(is_string($time) && strtotime($time) !== false){
                    $this->response->setHeader(self::HEADER_NAME, $time);
                }
            }
        }
        return $file;
    }
    
    /**
     * Returns the header name.
     * @return string
     */
    public static function getHeaderName(){
        return self::HEADER_NAME;
    }
}

