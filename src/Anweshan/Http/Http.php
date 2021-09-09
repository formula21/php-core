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
namespace Anweshan\Http;

use Anweshan\Exception\InvalidArgumentException;
use Anweshan\Filesystem\FilesystemInterface;
use Anweshan\Filesystem\File\FileInterface;
use Anweshan\Filesystem\Stream\StreamInterface;
use Anweshan\Util\Util;

/**
 * The class Http is the utility class to many other header api's.
 *
 * @package Anweshan\Http
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
final class Http
{
    
    /**
     * The format to send for date-time.
     * @var string The format gives an output like **Thu, 01 Jan 1970 00:00:00 GMT**
     */
    public const GMT_TIMESTAMP_FORMAT = 'D, d M Y H:i:s e';
    
    /**
     * Extracts the instanceof FileInterface and initializes the same reference.
     * @param FilesystemInterface $file The reference instanceof {@link \Anweshan\Filesystem\FilesystemInterface FilesystemInterface} or anything else.
     * @return bool If the extract was successful, returns true or false.
     */
    public static function toFileInterface(FilesystemInterface &$file = null) : bool{
        if($file instanceof FileInterface || $file instanceof StreamInterface){
            if(method_exists($file, 'toFile')){
                $file = $file->toFile();
            }
            return $file->exists();
        }
        return false;
    }
    
    /**
     * Gets the time in Greenwich Mean Time (GMT)/Coordinated Universal Time (UTC). The timezone if identified is GMT which is UTC+0.
     *
     * @param int|NULL $time The time to convert.
     * @return string|NULL The formated time.
     */
    public static function getGMT(?int $time): ?string
    {
        if($time){
            $tmp = Util::makeDateTime($time, self::GMT_TIMESTAMP_FORMAT, 'GMT');
            if($tmp == NULL || !is_string($tmp)){
                $default_timezone = date_default_timezone_get();
                try{
                    date_default_timezone_set('GMT');
                    $tmp = @date(self::GMT_TIMESTAMP_FORMAT, $time);
                    if($tmp){
                        $time = $tmp;
                    }
                }catch(InvalidArgumentException $e){
                    // Suppress, as this is just a failsafe method
                    // We can use any time of exception here, doesn't really matter.
                }finally{
                    date_default_timezone_set($default_timezone);
                }
            }else{
                $time = $tmp;
            }
        }
        return strval($time);
    }
}

