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

use Anweshan\Exception\DriverException;
use Anweshan\Exception\InvalidArgumentException;
use Anweshan\Filesystem\Directory\DirectoryNotFoundException;
use Anweshan\Filesystem\File\FileNotFoundException;
use Anweshan\Filesystem\File\UnreadableFileException;
use Anweshan\Filesystem\Stream\StreamException;
use Anweshan\Http\HTTPCode;
use Anweshan\Http\Http;
use Anweshan\Http\HttpInterface;
use Anweshan\Http\Response\ResponseException;
use Anweshan\Http\Response\Headers\AbstractResponse;
use Anweshan\Util\Argument;
use Exception;

/**
 * The class Api, runs all the Response-Headers and sends them to the browser.
 *
 * With sending & resolving HTTP Response Headers, it can also send and analyze headers based-on error codes, outputs the same to the browser. It implements (and thus is an instanceof) `Anweshan\Http\Response\Api\ResponseInterface`.
 *
 * @package Anweshan\Http
 * @subpackage Response\Api
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Api implements ResponseInterface
{
    /**
     * Contains the all the error handlers.
     * @var Argument $error_handlers All the error handlers.
     */
    private $error;

    /**
     * An array of response headers to be sent at response.
     * @var AbstractResponse[] $headers
     */
    private $headers;

    /**
     * The response controller.
     * @var HttpInterface $response The `instanceof` HttpInterface.
     */
    private $response;


    /**
     * The Response code.
     * @var integer
     */
    private $response_code = NULL;

    /**
     * Default constructor
     * @return void
     */
    public function __construct(array $headers, HttpInterface $response){
        // Error as Arguments.
        $this->error = new Argument();
        // Chained setting...
        $this->setResponseHeaders($headers)->setHTTPInterface($response);
    }

    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Api\ResponseInterface::setResponseHeaders()
     */
    public function setResponseHeaders(array $array) : ResponseInterface{
        try{
            foreach($array as $v){
                if($v instanceof AbstractResponse){
                    $this->headers[] = $v;
                }else{
                    throw new InvalidArgumentException("Not a valid response header");
                }
            }
        }catch(ResponseException $e){
            // Suppress
        }finally{
            return $this;
        }
    }

    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Api\ResponseInterface::setHTTPInterface()
     */
    public function setHTTPInterface(HttpInterface $response) : ResponseInterface{
        if($response && $response instanceof HttpInterface && $response->isResponseHeader()){
            $this->response = $response;
        }else{
            throw new ResponseException("Not a valid response interface (adapter)");
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Api\ResponseInterface::getHTTPInterface()
     */
    public function getHTTPInterface(): HttpInterface
    {
        return $this->response;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\Response\Api\ResponseInterface::getResponseHeaders()
     */
    public function getResponseHeaders(): array
    {
        return $this->headers;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\Response\Api\ResponseInterface::getResponseCode()
     */
    public function getResponseCode() : int {
        if(!is_int($this->response_code) || !in_array($this->response_code, HTTPCode::getConstants())){
            return HTTPCode::HTTP_OK;
        }
        return (int)$this->response_code;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\Response\Api\ResponseInterface::setResponseCode()
     */
    public function setResponseCode(int $code) : ResponseInterface{
       if(!is_int($code) || !in_array($code, HTTPCode::getConstants())){
           throw new ResponseException("The response code is invalid");
       }
       $this->response_code = $code;
       return $this;
    }

    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Api\ResponseInterface::run()
     */
    public function run(&$file){
        if(false !== Http::toFileInterface($file)){
            try{
                // $this->response_code = HTTPCode::HTTP_SERVICE_UNAVAILABLE;

                if($this->response == NULL || count($this->headers) == 0){
                    throw new ResponseException("Headers are not found");
                }

                foreach($this->headers as $v){
                    $file = $v->setResponse($this->response)->run($file);
                }
                return $file;
            }catch(
                    FileNotFoundException|
                    DirectoryNotFoundException|
                    UnreadableFileException|
                    StreamException
            $e){
                $this->response_code = HTTPCode::HTTP_NOT_FOUND;
            }catch(DriverException $e){
                $this->response_code = HTTPCode::HTTP_BAD_REQUEST;
            }catch(ResponseException $e){
                $this->response_code = HTTPCode::HTTP_NOT_IMPLEMENTED;
            }catch(Exception $e){
                $this->response_code = HTTPCode::HTTP_SERVICE_UNAVAILABLE;
            }
        }else{
            throw new InvalidArgumentException("The file parameter is not valid");
        }
        return false;
    }

    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Api\ResponseInterface::output()
     */
     public function output($file, array $responseCodes = [HttpCode::HTTP_OK, HttpCode::HTTP_PARTIAL_CONTENT]){
         if(true !== Http::toFileInterface($file)){
            throw new InvalidArgumentException("File is invalid");
         }

         if(!$this->response_code){
            throw new ResponseException("Response Code invalid");
         }

         if(!is_array($responseCodes) || count($responseCodes) == 0){
            $responseCodes = array($this->response_code);
         }

         if(!in_array($this->response_code, $responseCodes)){
             throw new ResponseException("Response code missing from array");
         }

         foreach($this->response->getHeaders() as $k=>$v){
             if($v != NULL && isset($v) && is_string($k) && strlen($k) != NULL){
                header("${k}: ${v}", true);
             }
         }

         http_response_code($this->getResponseCode());
         
         ob_start();
         echo $file->read();
         $contents = ob_get_contents();
         ob_end_clean();
         
         echo $contents;
         
         return true;
     }
}
