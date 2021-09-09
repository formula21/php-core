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
use Anweshan\Util\
    {Argument, Util};

/**
 * The class ETag is an implementation of the ETag header sent at response by the server to the browser.
 * 
 * An example of the same would be **ETag:** <Some random string>.
 * 
 * @package Anweshan\Http
 * @subpackage Response\Headers
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class ETag extends AbstractResponse
{
    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::HEADER_NAME
     */
    protected const HEADER_NAME = 'ETag';
    
    /**
     * The default cipher method.
     * @var string
     */
    public const DEFAULT_CIPHER_METHOD = "DES-EDE3-CBC";
    
    /**
     * The distinct seperator.
     * @var string
     */
    public const DISTINCT = '_';
    
    /**
     * The bar-distince seperator.
     */
    public const BAR_DISTINCT = '|';
    
    /**
     * The cipher method.
     * @var string|null
     */
    private $cipher_method;
    
    /**
     * Set's the cipher method.
     * @param string $cipher_method The cipher method to be used to encrypt.
     * @throws InvalidArgumentException If the `cipher_method` is not a valid algorithm.
     * @return \Anweshan\Http\Response\Headers\Etag The instanceof \Anweshan\Http\Response\Headers\Etag.
     */
    public function setCipherMethod(string $cipher_method): Etag{
        if(!in_array($cipher_method, openssl_get_cipher_methods())){
            throw new InvalidArgumentException("{$cipher_method} is not a valid cipher method");
        }
        
        $this->cipher_method = $cipher_method;
        return $this;
    }
    
    /**
     * Get's the cipher method.
     * @return string|NULL
     */
    public function getCipherMethod() :?string{
        return $this->cipher_method;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::run()
     */
    public function run(FilesystemInterface $file): ?FilesystemInterface
    {
        if(($file = parent::run($file)) && false !== Http::toFileInterface($file))
        {
            // echo "HI";
            $tmp = $this->encrypt($file);
            $headername = self::HEADER_NAME;
            $this->response->$headername = $tmp;
        }
        
        return $file;
    }
    
    /**
     * Encrypts the etag portion of timestamps.
     * @param FilesystemInterface $file An instanceof FileSystemInterface.
     * @return string|NULL The encrypted etag or none.
     */
    public function encrypt(FilesystemInterface $file) : ?string{ 
        if(false !== Http::toFileInterface($file)){
            
            /**
             * The arguments used for encryption
             * @var Argument $args
             */
            $args = new Argument();
            
            if(!$this->cipher_method || !in_array($this->cipher_method, openssl_get_cipher_methods())){
                $this->cipher_method = self::DEFAULT_CIPHER_METHOD;
            }
               
            //if(array_search)
            
            $args->method = strtolower($this->cipher_method);
            $args->key = $file->getHash('md5',null, FilesystemInterface::HASH_FILE); 
            $args->opts = defined('OPENSSL_RAW_DATA') ? OPENSSL_RAW_DATA : true;
            $args->iv = Util::random_bytes(openssl_cipher_iv_length($args->method), true);
            $args->clear = "";
            // Set boolean
            $args->lmset = false;
            $args->eset = false;

            // That ETag Alone will then do fine.
            
            $this->getLastModified($args->clear, $args->lmset)->getExpires($args->clear, $args->eset);
            
            if(!$args->eset && !$args->lmset){
                // Expires is not there, lets set it.
                return $this->setExpires($file)->encrypt($file);
            }
            
            if($args->lmset || $args->eset){
                // Either one is true.
                $args->clear = trim($args->clear, self::BAR_DISTINCT);
                $args->encrypt = openssl_encrypt($args->clear, $args->method, $args->key, $args->opts, $args->iv);
                if($args->encrypt !== false){
                    $args->encrypt = implode(self::DISTINCT,[base64_encode($args->iv.$args->encrypt),str_rot13($args->key),]);
                    return base64_encode($args->encrypt);
                }
            }
            
        }
        return NULL;
    }
    
    
    /**
     * Gets the last modified header.
     * @param string $clear The variable to store the header value.
     * @param bool $set The flag to set to true if the header value is found.
     * @return \Anweshan\Http\Response\Headers\ETag The instanceof the class itself is returned to chain the methods.
     */
    public function getLastModified(string &$clear, ?bool &$set = NULL) : ETag
    {
        $tmp = LastModified::getHeaderName();
        if(!is_null($this->response->$tmp) && is_string($this->response->$tmp) && strlen($this->response->$tmp) > 0 && strtotime($this->response->$tmp) !== false){
            $clear .= self::BAR_DISTINCT.strval(strtotime($this->response->$tmp));
            if(!is_null($set)){
                $set = true;
            }
        }
        
        if(!is_null($set) && $set != true){
            $set = false;
        }
        
        return $this;
    }
    
    
    /**
     * Gets the Expires header.
     * @param string $clear The variable to store the header value.
     * @param bool $set The flag to set to true if the header value is found.
     * @return \Anweshan\Http\Response\Headers\ETag The instanceof the class itself is returned to chain the methods.
     */
    public function getExpires(string &$clear, ?bool &$set = NULL) : ETag 
    {
        $tmp = Expires::getHeaderName();
               
        if(!is_null($this->response->$tmp) && is_string($this->response->$tmp) && strlen($this->response->$tmp) > 0 && strtotime($this->response->$tmp) !== false){
            $clear .= self::BAR_DISTINCT.strval(strtotime($this->response->$tmp));
            if(!is_null($set)){
                $set = true;
            }
        }
        
        if(!is_null($set) && $set != true){
            $set = false;
        }
        return $this;
    }
    
    
    /**
     * Sets the expires header.
     * @param FilesystemInterface $file The instanceof FilesystemInterface over which the header is to be generated.
     * @return \Anweshan\Http\Response\Headers\ETag The chainned instance of the class.
     */
    public function setExpires(?FilesystemInterface &$file = NULL) : ETag{
        
        $expires = new Expires($this->response);
        $headername = (string) $expires;
        
        if(!isset($this->response->$headername)){
            $tmp = $expires->run($file);
            
            if(false !== Http::toFileInterface($file) && strtotime($this->response->$headername) !== false){
                
                $file = $tmp;
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

