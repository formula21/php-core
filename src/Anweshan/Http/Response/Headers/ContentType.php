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
use Anweshan\Http\Response\{
  Response, ResponseException
};

/**
 * The class ContentType is an implementation of the Content-Type header sent at response by the server to the browser.
 *
 * An example of the same would be **Content-Type:** image/png.
 *
 * @package Anweshan\Http
 * @subpackage Response\Headers
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class ContentType extends AbstractResponse
{
    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::HEADER_NAME
     */
    protected const HEADER_NAME = 'Content-Type';

    private $mime = NULL;

    /**
     * Initializes the members.
     * @param Response $response The response to be set.
     * @param string $mime The mime of the stream, is specified if required.
     */
    public function __construct($response = NULL, ?string $mime = NULL){
       parent::__construct($response);
       $this->setMime($mime);

    }

    /**
     * Set the mime.
     * @param string|null $mime The mime of the object to be specified.
     * @return \Anweshan\Http\Response\Headers\ContentType Instance of the class itself is returned for chaining.
     */
    public function setMime(?string $mime){
      if(!empty($mime) && is_string($mime) && strlen($mime) > 0){
          $this->mime = $mime;
      }
      return $this;
    }

    /**
     * Get the mime.
     * @return string|null
     */
    public function getMime(){
       return $this->mime;
    }

    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::run()
     */
    public function run(FilesystemInterface $file) : ?FilesystemInterface
    {
        $headername = self::HEADER_NAME;

        if(($file = parent::run($file)) && false !== Http::toFileInterface($file)){
            $mime = $this->getMime() ?? $file->getMime();
            if(!is_null($mime)){
                $this->response->$headername = $mime;
            }else{
               throw new ResponseException("Unexpected null in mime");
            }
        }
        return $file;
    }
}
