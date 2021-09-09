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
use Anweshan\Http\Http;

/**
 * The class AcceptRanges is an implementation of the Accept-Ranges header sent at response by the server to the browser.
 *
 * An example of the same would be **Accept-Ranges:** bytes.
 *
 * @package Anweshan\Http
 * @subpackage Response\Headers
 *
 * @author Anweshan
 * @since 2021
 * @version 3
 * @license MIT
 */
class AcceptRanges extends AbstractResponse{
    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::HEADER_NAME
     */
    protected const HEADER_NAME = 'Accept-Ranges';

    private $accept_string = 'bytes';

    /**
     * Sets the accept-ranges value.
     * @param string $accept_string The value of "Accept-Ranges"
     * @throws InvalidArgumentException If the parameter is empty or not a string.
     * @return AcceptRanges The instance of the class itself for chaining.
     */
    public function setAcceptString(string $accept_string = 'bytes') : AcceptRanges{
        if(!is_string($accept_string) || empty($accept_string)){
            throw new InvalidArgumentException("Accept Ranges must be a string");
        }
        $this->accept_string = $accept_string;
        return $this;
    }

    /**
     * Gets the value of "Accept-Ranges" header.
     * @return string
     */
    public function getAcceptString() : string {
        return $this->accept_string;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::run()
     */
    public function run(FilesystemInterface $file) : ?FilesystemInterface{
        $headername = self::HEADER_NAME;
        if(($file = parent::run($file)) && false !== Http::toFileInterface($file))
        {
            $this->response->$headername = $this->accept_string;
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
