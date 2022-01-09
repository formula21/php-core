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

use Anweshan\Http\Http;

use Anweshan\Exception\{
  OutOfRangeException, NegativeNumberException};
use Anweshan\Filesystem\{
  FilesystemInterface, Stream\Stream, Stream\File};

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
class ContentRange extends AbstractResponse{
    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::HEADER_NAME
     */
    protected const HEADER_NAME = 'Content-Range';

    /**
     * Defines the upper limit (in bytes) of a range.
     * @var integer
     */
    private $upperLimit;

    /**
     * Defines the upper limit (in bytes) of a range.
     * @var integer
     */
    private $lowerLimit;

    /**
     * The percentage increase.
     */
    public const PERCENT_INCREASE = 10;

    /**
     * The limit where we send the whole file.
     */
     public const LIMIT = 1000;

    /**
     * Set the lower limit, in bytes, of a range.
     * @param int $lowerLimit The lower limit of a range.
     * @param int $size The size in bytes.
     * @return \Anweshan\Http\Response\Headers\ContentRange Returns an instanceof itself to continue chaining.
     */
    public function setLowerLimit(int $lowerLimit, int $size = -1){
        if($lowerLimit < 0){
           throw new NegativeNumberException("lower limit must be positive");
        }

        if($size != -1 && $lowerLimit > $size){
           throw new OutOfRangeException("lower limit is out of range");
        }

        $this->lowerLimit = $lowerLimit;
        return $this;
    }

    /**
     * Set the upper limit, in bytes, of a range.
     * @param int $upperLimit The upper limit of a range.
     * @param int $size The size in bytes.
     * @return \Anweshan\Http\Response\Headers\ContentRange Returns an instanceof itself to continue chaining.
     */
    public function setUpperLimit(int $upperLimit, int $size = -1){
      if($upperLimit < 0){
         throw new NegativeNumberException("upper limit must be positive");
      }

      if($size != -1 && $upperLimit > $size){
         throw new OutOfRangeException("upper limit is out of range");
      }

      $this->upperLimit = $upperLimit;
      return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::run()
     */
    public function run(FilesystemInterface $file): ?FilesystemInterface{
        $headername = self::HEADER_NAME;
        $range = "bytes=0-";
        $size = $file->getSize();

        if(isset($_SERVER['HTTP_RANGE']) && is_string($_SERVER['HTTP_RANGE']) && strlen($_SERVER['HTTP_RANGE']) > 0){
           $range = $_SERVER['HTTP_RANGE'];
        }

        if($range){
           $arr = explode("=", $range);

           if(count($arr) > 1){
              $arr = explode('-', $arr[1]);
           }

           if(count($arr) >= 1){
              if(empty($arr[1]) || !is_numeric($arr[1])){
                 unset($arr[1]);
              }
              $arr[] = null;
              $lowerLimit = intval($arr[0]);
              # $percent = floatval(self::PERCENT_INCREASE / 100);
              $upperLimit = $arr[1] ?? intval(($size * $lowerLimit) + $lowerLimit) - 1;
              if($upperLimit >= $size){
                 $upperLimit = $size - 1;
              }
              $this->setLowerLimit($lowerLimit, $size)->setUpperLimit($upperLimit, $size);
           }

        }

        if(($file = parent::run($file)) && false !== Http::toFileInterface($file))
        {
            // Till here we get the original file size.
            $header = "bytes {$this->lowerLimit}-{$this->upperLimit}/{$size}";
            // It will be the responsibility of Content-Length also to see if
            // the integers are correctly subtracted also.
            // To protect this issue here we will seek and cut-short a file
            // to a stream procuring such a length.

            if(file_exists($file->getPath())){
               $contents = file_get_contents($file->getPath(), 0, NULL, intval($this->lowerLimit), intval($this->upperLimit - $this->lowerLimit));
               if($contents){
                  $file_1 = new File(new Stream($contents, $file->getFilename(), $file->getExtension(), $file->getPath()));
                  if($file_1->exists()){
                     $this->response->$headername = $header;
                     $file = $file_1;
                  }
               }
            }
        }
        
        return $file;
    }
}
