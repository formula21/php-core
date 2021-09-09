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
use Anweshan\Http\Response\Response;
    
/**
 * The class ContentDisposition is an implementation of the Content-Disposition header sent at response by the server to the browser.
 *
 * An example of the same would be: **Content-Disposition:** inline; filename='name.extension'.
 *
 *
 * @package Anweshan\Http
 * @subpackage Response\Headers
 *
 * @author Anweshan
 * @since 2021
 * @version 3
 * @license MIT
 */

 class ContentDisposition extends AbstractResponse
 {
     /**
      * {@inheritdoc}
      * @see \Anweshan\Http\Response\Headers\AbstractResponse::HEADER_NAME
      */
     protected const HEADER_NAME = 'Content-Disposition';

     /**
      * The inline variable is the flag used to determine how the file is disposed to the browser.
      * @var bool
      */
     private $inline = true;

     /**
      * The basename of the file which is to be used.
      * @var string|NULL
      */
     private $basename = NULL;

     /**
      * Initializes the members.
      * @param Response $response The response to be set.
      * @param bool $inline If set true, the resource is all set to be disposed inline, or forced as an attachment (i.e. downloaded).
      * @param string $basename The name of the the resource while being disposed.
      * @return void
      */
     public function __construct($response = null, bool $inline = true, string $basename = NULL){
         parent::__construct($response);
         $this->setInline($inline)->setBaseName($basename);

     }

     /**
      * Set the basename of the file, which is used to identify the resource to the browser when being disposed to the browser.
      * @param string|null $basename The name of the file.
      * @return \Anweshan\Http\Response\Headers\ContentDisposition The instanceof itself for chaining the resources.
      */
     public function setBaseName(?string $basename){
         if($basename == NULL){
            $this->basename = NULL;
         }
         if(is_string($basename)){
            list($name, $ext) = array(pathinfo($basename, PATHINFO_FILENAME), pathinfo($basename, PATHINFO_EXTENSION));
            if(is_string($name) && is_string($ext) && !empty($name) && !empty($name)){
                $this->basename = "{$name}.{$ext}";
            }else{
                $this->basename = NULL;
            }
         }
         return $this;
     }

     /**
      * Get the basename of the resource.
      * @return string|null
      */
     public function getBasename(){
        return $this->basename;
     }

     /**
      * Set the resource to be disposed inline or as an attachment (forced download).
      * @param bool $inline
      * @return \Anweshan\Http\Response\Headers\ContentDisposition An instance of the class for chaining.
      */
     public function setInline(bool $inline){
         $this->inline = $inline;
         return $this;
     }

     /**
      * Checks if the resource is set to be disposed "inline" or not.
      * @return bool true is returned if the resource is disposed inline, otherwise false.
      */
     public function isInline(){
         return $this->inline;
     }

     /**
      * Returns the header name.
      * @return string
      */
     public static function getHeaderName(){
         return self::HEADER_NAME;
     }

     /**
      * {@inheritdoc}
      * @see \Anweshan\Http\Response\Headers\AbstractResponse::run()
      */
     public function run(FilesystemInterface $file) : ?FilesystemInterface
     {
         $headername = self::HEADER_NAME;

         if(($file = parent::run($file)) && false !== Http::toFileInterface($file))
         {
             $basename = $this->basename ?? $file->getBasename();
             $inline = ($this->inline)?'inline':'attachment';
             $this->response->$headername = "{$inline};filename=\"{$basename}\"";
         }
         return $file;
     }
 }
