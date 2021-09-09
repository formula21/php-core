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
use Anweshan\Http\{ 
    Http, Response\Response 
};
use Anweshan\Util\Argument;

/**
 * The class Expires is an implementation of the Expires header sent at response by the server to the browser.
 * 
 * The Expires header controls the cache of a file at the client side.
 * 
 * An example of the same would be **Expires:** Thu, 01 Jan 1970 00:00:00 GMT.
 * 
 * Any time in the past would signify a forced REVALIDATION and non-storage of the file in the cache.
 *
 * @package Anweshan\Http
 * @subpackage Response\Headers
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Expires extends AbstractResponse
{
    /**
     * Default expiry time if no timestamp is found.
     * @var integer
     */
    public const DEFAULT_EXPIRY = 86400;
    
    /**
     * {@inheritdoc}
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::HEADER_NAME
     */
    protected const HEADER_NAME = 'Expires';
    
    
    private $star = "*";
    
    /**
     * The expiry parameters.
     * @var Argument
     */
    private $expiry = NULL;
    
    /**
     * Do we use the global timestamp i.e. $_SERVER['REQUEST_TIME'].
     */
    private $expiry_global = false;
    
    /**
     * Initializes the members.
     * @param Response $response The response to be set.
     * @param string|array|null|int $expiry The expiry timestamp. 
     * @param bool|NULL $expiry_global Usage of the global timestamp i.e. $_SERVER['REQUEST_TIME'].
     * @return void
     */
    public function __construct($response = null, $expiry = null, ?bool $expiry_global = false)
    {
        parent::__construct($response);
        // Instance of Arguments.
        $this->expiry = new Argument(['*'=>self::DEFAULT_EXPIRY]);
        $this->setExpiry($expiry);
        if(is_null($expiry_global)){
            $expiry_global = false;
        }
        $this->expiry_global = $expiry_global;      
    }
    
    /**
     * Sets the expiry.
     * @param string|array|null|int $expiry
     * @return \Anweshan\Http\Response\Headers\Expires The instanceof itself.
     */
    public function setExpiry($expiry = null) : Expires{
        if(is_int($expiry) && ($expiry = intval($expiry, 10)) > 0){
            // Will replace star.
            $this->expiry->{$this->star} = $expiry;
        }
        
        if(is_array($expiry) && count($expiry) > 0){
            if(array_key_exists($this->star, $expiry) && is_int($expiry[$this->star] = intval($expiry[$this->star], 10)) && $expiry[$this->star] > 0){
                // A star is followed as a default expiry.
                $this->expiry->{$this->star} = $expiry[$this->star];
                unset($expiry[$this->star]);
            }
            foreach($expiry as $k=>$v){
                // For groups 5200 => ['css','js','json'] or 5500 => css
                if(is_int($k) && ($k = intval($k)) > 0 && (is_array($v) || is_string($v))){
                    if(is_string($v)){
                        $v = [$v?:NULL];
                    }
                    
                    foreach($v as $ex){
                        $time = $k;
                        if(!is_null($ex) && is_string($ex = strtolower($ex)) && strlen($ex) > 0 && $ex !== $this->star){
                            if(isset($this->expiry->$ex))
                                $time = max($k, $this->expiry->$ex);
                            $this->expiry->$ex = $time;
                        }
                    }
                }else
                    // For group: css => 25, or css => NULL or css => '*'
                    if(((is_int($v) && ($v = intval($v, 10)) >= 0) || is_null($v) || is_string($v) && ($v == '*' || $v == '') && is_null(($v = NULL))) && is_string($k = strlower($k)) && strlen($k) > 0 ){
                    if(isset($this->expiry->$k) && !is_null($v)){
                        // That is expiry
                        $time = max($v, $this->expiry->$k);
                    }
                    $this->expiry->$k = $v;
                }else{
                    # Do Nothing
                    # End if
                }
            }
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     * @param bool $use_global To use the global time of request.
     * @see \Anweshan\Http\Response\Headers\AbstractResponse::run()
     */
    public function run(FilesystemInterface $file) : ?FilesystemInterface
    {
        $headername = self::HEADER_NAME;
        if(($file = parent::run($file)) && false !== Http::toFileInterface($file))
        {
            $extension = strtolower($file->getExtension());
            if($extension){
                $time = $this->expiry->$extension;
                if(is_null($time) || (is_string($time) && $time === $this->star) 
                    || !is_int($time) || ($time = intval($time, 10)) <= 0 )
                {
                    $time = $this->expiry->{$this->star};
                }
                
                $now = time();
                if($this->expiry_global 
                    && is_int($_SERVER['REQUEST_TIME']) 
                    && intval($_SERVER['REQUEST_TIME'], 10) > 0 
                    && strtotime($_SERVER['REQUEST_TIME']) !== false)
                {
                    $now = intval($_SERVER['REQUEST_TIME'], 10); 
                }
                
                // The time to use in future.
                if( ($time = $now + $time) < time() ){
                    $time = ($time - $now) + time();
                }
                
                // The GMT specified time of expiry.
                $time = Http::getGMT($time);
                
                if(is_string($time) && strtotime($time) !== false){
                    $headername = self::HEADER_NAME;
                    $this->response->$headername = $time;
                    
                    if(class_exists(__NAMESPACE__.'\\ETag')){
                        $etag = new Etag($this->response);
                        $headername = Etag::getHeaderName();
                        if(isset($this->response->$headername)){
                            unset($this->response->$headername);
                            $file = $etag->run($file);
                        }
                    }
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

