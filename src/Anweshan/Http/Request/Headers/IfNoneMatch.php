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
namespace Anweshan\Http\Request\Headers;

use Anweshan\Http\Http;
use Anweshan\Http\Response\Headers\ETag;
use Anweshan\Http\Response\Headers\Expires;
use Anweshan\Util\Argument;
use Anweshan\Http\Response\Headers\LastModified;
use Anweshan\Filesystem\FilesystemInterface;
use Anweshan\Http\HTTPCode;

/**
 * The class IfNoneMatch is an implementation of the If-None-Match header sent at response by the server to the browser.
 * 
 * An example of the same would be **ETag:** <Some random string>.
 * 
 * @package Anweshan\Http
 * @subpackage Request\Headers
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class IfNoneMatch
{
    
    public function decrypt(string $etag){
        if(!$etag || !is_string($etag) || strlen($etag) == 0){
            return false;
        }
        /**
         * @var \Anweshan\Util\Argument $args
         */
        $args = new Argument();
        
        $args->encrypt = explode(ETag::DISTINCT, base64_decode($etag));
        if(is_array($args->encrypt) && count($args->encrypt) > 0){
            
            $args->key = str_rot13($args->encrypt[array_key_last($args->encrypt)]);
            
            unset($args->encrypt[array_key_last($args->encrypt)]);
            $args->encrypt = implode(ETag::DISTINCT, $args->encrypt);
            
            $args->encrypt = base64_decode($args->encrypt);
            
            $args->method = strtolower(ETag::DEFAULT_CIPHER_METHOD);
            $args->opts = defined('OPENSSL_RAW_DATA') ? OPENSSL_RAW_DATA : true;
            
            $args->iv_size = openssl_cipher_iv_length($args->method);
            
            $args->iv = substr($args->encrypt, 0, $args->iv_size);
            
            // Corrupted 
            if (strlen($args->iv) != $args->iv_size) {
               return false;
            }
            
            $args->encrypt = substr($args->encrypt, $args->iv_size);
            
            $args->clear = openssl_decrypt($args->encrypt, $args->method, $args->key, $args->opts, $args->iv );
            
            $tmp = explode(ETag::BAR_DISTINCT, $args->clear);
           
            if(is_array($tmp) && count($tmp) >=1 ){
                $headers = new Argument();
                if(count($tmp) == 2){
                    $headername = LastModified::getHeaderName();
                    $headers->$headername = Http::getGMT(intval($tmp[0]));                
                }
                
                $headername = Expires::getHeaderName();
                $tmp = $tmp[array_key_last($tmp)];
                $headers->$headername = Http::getGMT(intval($tmp));
                
                $headername = ETag::getHeaderName();
                $headers->$headername = $etag;
                
                return $headers;
            }
            
        }
        
        return false;
    }
    
    
    public function setHeaders(Argument $headers, FilesystemInterface $file = null){
        if($headers){
            $headers = get_object_vars($headers);
            if($file && false !== Http::toFileInterface($file)){
                // LEAVE AS WE DO NOT KNOW THIS YET. 
            }
            foreach($headers as $k=>$v){
                header("$k:$v");
            }
            http_response_code(HTTPCode::HTTP_NOT_MODIFIED);
            exit;
        }else{
            return false;
        }
    }
    
}

