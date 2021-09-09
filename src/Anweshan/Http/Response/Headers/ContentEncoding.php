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

use Anweshan\Filesystem\{
    FilesystemInterface,Stream\Stream
};
use Anweshan\Http\{
    Http, Response\Response
};
use Mimey\MimeTypes;
use Anweshan\Util\Argument;

/**
 * The class ContentEncoding is an implementation of the Content-Encoding header sent at response by the server to the browser.
 *
 * An example of the same would be **Content-Encoding:** gzip.
 *
 * By default this class would only send `gzip` content encoding. On overriding one can encode in other methods as well.
 *
 * @package Anweshan\Http
 * @subpackage Response\Headers
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class ContentEncoding extends AbstractResponse
{
    /**
     * @var string
     */
    private $accept_encoding = array();

    /**
     * @var array
     */
    private $exclude_extension;

    /**
     *
     * @var boolean
     */
    private $zipped = false;

    /**
     * The default gzip compression level.
     * @var integer
     */
    public const DEFAULT_GZIP_COMPRESSION_LEVEL = 9;


    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::HEADER_NAME
     */
    protected const HEADER_NAME = 'Content-Encoding';

    /**
     * Overrides the parent.
     * @param Response $response
     * @param string $accept_encoding
     * @param string|array|null $exclude_extension
     */
    public function __construct($response = null, ?string $accept_encoding = null, $exclude_extension = NULL){
        parent::__construct($response);
        $this->setAcceptEncoding($accept_encoding)->setExcludeExtension($exclude_extension);
    }


    /**
     * Sets the encoding.
     * @param string $accept_encoding
     * @return \Anweshan\Http\Response\Headers\ContentEncoding
     */
    public function setAcceptEncoding(?string $accept_encoding){
        if(is_null($accept_encoding) || strlen($accept_encoding) == 0){
            $accept_encoding = $_SERVER['HTTP_ACCEPT_ENCODING'];
            // Also reset the $this->accept_encoding property.
            $this->accept_encoding = array();
        }

        if(strlen($accept_encoding) > 0)
        {
            $accept_encoding = explode(',', $accept_encoding);
        }

        if(is_array($accept_encoding) && count($accept_encoding) > 0){

            if(is_string($accept_encoding)){
                $accept_encoding = array($accept_encoding);
            }

            if(!is_array($this->accept_encoding)){
                $this->accept_encoding = array();
            }

            $this->accept_encoding = array_unique(array_merge($this->accept_encoding, $accept_encoding));

        }

        if(is_array($this->accept_encoding)){
            $this->accept_encoding = array_filter($this->accept_encoding, function($v){
                if(!is_string($v) || strlen($v) == 0){
                    return false;
                }
                return true;
            });
        }

        return $this;
    }


    public function setExcludeExtension($exclude_extension){

        if(is_null($exclude_extension) && !is_null($this->exclude_extension)){
            $this->exclude_extension = array();
        }

        if(is_string($exclude_extension)){
            $exclude_extension = explode(',', $exclude_extension);
        }

        if(is_array($exclude_extension) && count($exclude_extension) > 0){

            if(is_string($this->exclude_extension)){
                $this->exclude_extension = array($exclude_extension);
            }

            if(!is_array($this->exclude_extension)){
                $this->exclude_extension = array();
            }

            $this->exclude_extension = array_values(array_unique(array_merge($exclude_extension, $this->exclude_extension)));
        }

        if(is_array($this->exclude_extension)){
            $this->exclude_extension = array_filter($this->exclude_extension, function($v){
                if(!is_string($v) || strlen($v) == 0){
                    return false;
                }
                return true;
            });
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::run()
     */
    public function run(FilesystemInterface $file): ?FilesystemInterface
    {
        if(($file = parent::run($file)) && false !== Http::toFileInterface($file) && $this->isGzip($file)){
            // We can gzip
            $headername = self::getHeaderName();
            // Now let's encode
            if($this->modifyFile($file)->zipped){
               $this->response->$headername = 'gzip';
            }
        }
        return $file;
    }


    /**
     * Checks if a browser accepts a 'gzip' encoding & the file can be gzipped.
     *
     * @param FilesystemInterface $file The instance to check the gzip.
     * @return bool Returns true is conditions satify, false otherwise
     */
    private function isGzip(FilesystemInterface $file) : bool{
        if(false !== Http::toFileInterface($file) && in_array('gzip', $this->accept_encoding)){
            // But we need to check the file extension
            if(is_array($this->exclude_extension) && count($this->exclude_extension) > 0){
                $mimes = new MimeTypes();
                $exclude_extension = array_flip($this->exclude_extension);
                foreach(array_keys($exclude_extension) as $k){
                    $exclude_extension[$k] = $mimes->getAllMimeTypes($k);
                    if(in_array($file->getMime(), $exclude_extension[$k])){
                        return false;
                    }
                }
            }
           return true;
        }
        return false;
    }

    /**
     *
     * @param FilesystemInterface $file
     * @return ContentEncoding An instanceof
     */
    private function modifyFile(FilesystemInterface &$file): ContentEncoding{
        $args = new Argument();
        if(false !== Http::toFileInterface($file) && $file->exists()){
            $args->data = $file->read();
            $args->mime = $file->getMime();
            $args->filename = $file->getFilename();
            $args->extension = $file->getExtension();
            $args->path = $file->getPath();
            $args->modified = $file->getLastModifiedTimestamp() ?? time();
            $args->accessed = $file->getLastAccessedTimestamp() ?? time();
            $args->created = $file->getFileCreationTimestamp() ?? time();
            // GZENCODING
            if(function_exists('gzencode')){
                $args->encode = gzencode($args->data, self::DEFAULT_GZIP_COMPRESSION_LEVEL);
                if($args->encode !== false){
                    $stream = new Stream($args->encode);
                    $stream->setFilename($args->filename)
                           ->setExtension($args->extension)
                           ->setCanonicalPath($args->path)
                           ->setTimestamp(FilesystemInterface::FILESYSTEM_LAST_MODIFIED, $args->modified)
                           ->setTimestamp(FilesystemInterface::FILESYSTEM_LAST_ACCESSED, $args->accessed)
                           ->setTimestamp(FilesystemInterface::FILESYSTEM_CREATED, $args->created);
                   $tmp = $stream->toFile(true);
                    // Change of Content-Length Header required.
                   $headername = ContentLength::getHeaderName();
                    if(isset($this->response->$headername)){
                        unset($this->response->$headername);
                        $obj = new ContentLength();
                        if($obj){
                            $file = $obj->setResponse($this->response)->run($tmp);
                            $this->zipped = true;
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Returns the header name.
     * @return string
     */
    public static function getHeaderName(){
        return self::HEADER_NAME;
    }
}
