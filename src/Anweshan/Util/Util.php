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

namespace Anweshan\Util;

use Anweshan\Exception\InvalidArgumentException;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * The class Util has few utility functions used frequently.
 * 
 * @package Anweshan\Util
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Util
{
    const URL_SEPARATOR = '/';
    
    public static function removeGet(array $get, string $remove) : array{
        if($get == NULL){
            throw new InvalidArgumentException("The array is null");
        }
        if(array_key_exists($remove, $get)){
            unset($get[$remove]);
        }
        return $get;
    }
    
    public static function combineGet(array $array, bool $urlencode = true) : string{
        $get = array();
        foreach($array as $k=>$v){
            if($urlencode){
                $v = urlencode($v);
            }
            $get[] = $k.'='.$v;
        }
        return implode("&",$get);
    }
    
    public static function sanitizePath(string $path){
        $path = str_replace(self::URL_SEPARATOR, DIRECTORY_SEPARATOR, $path);
        $path = Util::rtrim($path, DIRECTORY_SEPARATOR, self::URL_SEPARATOR);
        return $path;
    }
    
    public static function makePath(string ...$segments){
        foreach($segments as &$v){
            $v = Util::sanitizePath($v);
        }
		
		$d = __DIR__;
		
		if($d[0] === DIRECTORY_SEPARATOR && DIRECTORY_SEPARATOR === '/'){
			$d = true;
		}else{
			$d = false;
		}
		
        $r = Util::ltrim(join(DIRECTORY_SEPARATOR, $segments), DIRECTORY_SEPARATOR);
		
		if($d){
			$r = '/'.$r;
		}
		return $r;
    }
    
    public static function makeURL(string ...$segments){
        return join(self::URL_SEPARATOR, $segments);
    }
    
	public static function rtrim(string $value, string ...$char){
        foreach($char as $v){
			$value = rtrim($value, $v);
        }
        return $value;
    }
	
	public static function ltrim(string $value, string ...$char){
        foreach($char as $v){
			$value = ltrim($value, $v);
        }
        return $value;
    }
	
    public static function trim(string $value, string ...$char){
        foreach($char as $v){
			$v = $v[0];
			if(strlen($v) == 1){
				$value = trim(trim($value), $v);
			}
        }
        return $value;
    }
    
    public static function makeDirectory(string $dir) :?array{
        if($dir !== ''){
            $z = array();
            $dir = explode(DIRECTORY_SEPARATOR, Util::makePath($dir));
            foreach($dir as $k=>$v){
                if($k == 0){
                    $z[] = $v;
                }else{
                    $z[] = Util::makePath($z[$k-1], $v);
                }
                
            }
            return $z;
        }
        return NULL;
    }
    
    
    /**
     * Makes a proper datetime instance from a datetime
     * @param string $datetime The numeric timestamp or a valid PHP style date.
     * @param string $format The format expected (OPTIONAL).
     * @param string $timezone The timezone to be converted (OPTIONAL).
     * @return DateTime|string|null If format is given a string is returned, else DateTime instance is returned. If everything goes wrong NULL is returned.
     */
    public static function makeDateTime(string $datetime, ?string $format = null,  ?string $timezone = null){
        $dateTime = null;
        
        if(is_string($datetime) && strlen($datetime) > 0){
            if(!is_numeric($datetime)){
                if(!($datetime = strtotime($datetime))){
                    $dateTime = NULL;
                }else{
                    $dateTime = new DateTime();
                }
            }else{
                $dateTime = new DateTime();
            }   
        }
        
        // Checking if everything is fine.
        try{
            if($dateTime){
                $dateTime->setTimestamp($datetime);
                if($timezone){
                    $timezone = new DateTimeZone($timezone);
                    $dateTime->setTimezone($timezone);
                }
                if($format && strlen($format) > 0){
                    $dateTime = $dateTime->format($format);
                }
            }
        }catch(Exception $e){
            // Suppress anything...
        }finally{
            return $dateTime;
        }
        
    }
    
    /**
     * Removes the directory and it's contents recursively. 
     * 
     * The native `rmdir` is only limited to removing empty directories. 
     * 
     * @param string $dir The directory to remove.
     * @throws Exception If any exception arises. 
     * @return boolean
     */
    public static function rmdir(string $dir){
        $tmp = NULL;
        try{
            $dir = Util::rtrim(realpath($dir)?:"", DIRECTORY_SEPARATOR);
            if(strlen($dir) > 0 && is_dir($dir)){
                $tmp = $dir;
                $dir = scandir($dir);
                foreach($dir as $k=>&$v){
                    // Precaution against files like .htacces
                    if(strlen(trim($v,'.')) == ''){
                        $v = trim($v,'.');
                    }
                    
                    if ( ($v = Util::rtrim($tmp.DIRECTORY_SEPARATOR.$v, DIRECTORY_SEPARATOR)) != $tmp && realpath($v) !== false ){
                        if((is_dir($v) && !self::rmdir($v)) || (is_file($v) && !@unlink($v))){
                            return false;
                        }else{
                            @clearstatcache();
                        }
                    }else{
                        unset($dir[$k]);
                    }
                }
                return @rmdir($tmp);
            }
            return false;
        }catch(Exception $e){
            throw $e;
            return false;
        }finally{
            @clearstatcache();
        }
    }
    
    /**
     * Generate a random string
     *
     * @param int  $length String length
     * @param bool $raw    Return RAW data instead of ascii
     *
     * @return string The generated random string
     */
    public static function random_bytes($length, $raw = false)
    {
        $hextab  = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $tabsize = strlen($hextab);
        
        // Use PHP7 true random generator
        if ($raw && function_exists('random_bytes')) {
            return random_bytes($length);
        }
        
        if (!$raw && function_exists('random_int')) {
            $result = '';
            while ($length-- > 0) {
                $result .= $hextab[random_int(0, $tabsize - 1)];
            }
            
            return $result;
        }
        
        $random = openssl_random_pseudo_bytes($length);
        
        if ($random === false && $length > 0) {
            throw new Exception("Failed to get random bytes");
        }
        
        if (!$raw) {
            for ($x = 0; $x < $length; $x++) {
                $random[$x] = $hextab[ord($random[$x]) % $tabsize];
            }
        }
        
        return $random;
    }
}

