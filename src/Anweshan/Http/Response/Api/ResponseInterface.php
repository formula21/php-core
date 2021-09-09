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
namespace Anweshan\Http\Response\Api;

use Anweshan\Exception\InvalidArgumentException;
use Anweshan\Filesystem\File\FileInterface;
use Anweshan\Filesystem\Stream\StreamInterface;
use Anweshan\Http\HttpInterface;
use Anweshan\Http\Response\ResponseException;
use Anweshan\Http\Response\Headers\AbstractResponse;
use Anweshan\Http\HTTPCode;

/**
 * The interface defines all the methods to run and get a response to the browser.
 *
 * @package Anweshan\Http
 * @subpackage Response\Api
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
interface ResponseInterface
{
    /**
     * Runs the Response Headers, to get an output.
     * @param FileInterface|StreamInterface $file The file/stream to work with.
     * @throws InvalidArgumentException If the parameter is invalid.
     * @throws ResponseException The exception is thrown if there are any other exceptions raised.
     * @return bool Returns `true` if the response is successfull, `false` otherwise.
     */
    public function run(&$file);

    /**
     * Get the output.
     * @param FileInterface|StreamInterface $file The file/stream to work with.
     * @param int[] $responseCodes The response code array for the output.
     * @throws InvalidArgumentException ``$file` parameter is invalid.
     * @throws ResponseException If the response code is not set.
     * @return bool Returns `true` if the headers setting is successfull, `false` otherwise.
     */
    public function output($file, array $responseCodes = [HttpCode::HTTP_OK, HttpCode::HTTP_PARTIAL_CONTENT]);

    /**
     * Sets the response header(s)
     * @param array $array An array of response headers.
     * @return \Anweshan\Http\Response\Api\ResponseInterface The `instanceof` of itself.
     * @throws InvalidArgumentException If header(s) is/are invalid.
     */
    public function setResponseHeaders(array $array) : ResponseInterface;

    /**
     * Set the HTTP interface.
     * @param HttpInterface $response The response handler.
     * @return \Anweshan\Http\Response\Api\ResponseInterface The instance of ResponseInterface is returned for chaining.
     */
    public function setHTTPInterface(HttpInterface $response) : ResponseInterface;

    /**
     * Get the HTTP interface
     * @return HttpInterface The instance of `HttpInterface` is returned.
     */
    public function getHTTPInterface() : HttpInterface;

    /**
     * Get the array of response headers.
     * @return AbstractResponse[] The array of response headers.
     */
    public function getResponseHeaders() : array;

    /**
     * Gets the response code.
     * @return int The response code.
     */
    public function getResponseCode() : int;

    /**
     * Sets the response code.
     * @param int $code The response code.
     * @throws ResponseException If the response code is invalid,
     *                           we raise this exception.
     * @return \Anweshan\Http\Response\Api\ResponseInterface The instanceof itself for chaining.
     */
     public function setResponseCode(int $code) : ResponseInterface;

}
