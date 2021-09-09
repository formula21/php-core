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

namespace Anweshan\Filesystem\Stream;

use Anweshan\Filesystem\{
    File\FileInterface, FilesystemInterface
};

/**
 * The class File is an adapter to a StreamInterface, so that the "stream" in memory can be interchanged to have a "FileInterface" like implementation.
 * 
 * The class implements (and thus an `instanceof`) FileInterface. This makes both StreamInterface and FileInterface linked other than by FilesystemInterface.
 * 
 * @package Anweshan\Filesystem
 * @subpackage Stream
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class File implements FileInterface{
    
    private $stream = null;
    
   /**
    * Default Constructor.
    * @param StreamInterface $stream The instanceof StreamInterface typecasted to FileInterface.
    * @return void
    */
   public function __construct(StreamInterface $stream){
       $this->stream = $stream;
   }
    
   /**
    * Gets the stream interface instance.
    * @return StreamInterface|NULL The instance of StreamInterface.
    */
   public function getStreamInterface() :?StreamInterface{
        return $this->stream;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getMime()
     */
    public function getMime() :?string{
        return !$this->exists()?NULL:$this->stream->getMime();
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getFilename()
     */
    public function getFilename() :?string{
        return !$this->exists()?NULL:$this->stream->getFilename();
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getExtension()
     */
    public function getExtension() :?string{
        return !$this->exists()?NULL:$this->stream->getExtension();
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getBasename()
     */
    public function getBasename() :?string{
        if(!$this->exists() || (!$this->stream->isExtension() && !$this->stream->isFilename())){
            return NULL;    
        }
        
        $base = '';
        
        if($this->stream->isFilename()){
            $base = $base.$this->stream->getFilename();
        }
        
        if($this->stream->isExtension()){
           $base = $base.'.'.$this->stream->getExtension();
        }
        
        $base = trim($base);
        
        return $base;        
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::exists()
     */
    public function exists() :bool{
        return ($this->stream!=NULL);
    }
    
    /**
     * {@inheritDoc}
     * **NOTE:** If path is not NULL, i.e. canonical file path is set, we return the dir based on the path.
     * @see \Anweshan\Filesystem\File\FileInterface::getDir()
     */
    public function getDir(): ?string{
        if($this->exists() && $this->path() != NULL){
            return dirname($this->path);
        }
        return NULL;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getPath()
     */
    public function getPath() :?string{
        return $this->stream->getCanonicalPath();
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::isReadable()
     */
    public function isReadable() : bool{
        return $this->exists()?true:NULL;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::isWritable()
     */
    public function isWritable() : bool{
        return $this->exists()?false:NULL;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::read()
     */
    public function read() :?string {
        return $this->exists()?$this->stream->read():NULL;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getLastAccessedTimestamp()
     */
    public function getLastAccessedTimestamp() :?int{
        return $this->exists()?$this->stream->getTimestamp(FilesystemInterface::FILESYSTEM_LAST_ACCESSED):NULL;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getLastModifiedTimestamp()
     */
    public function getLastModifiedTimestamp() :?int{
        return $this->exists()?$this->stream->getTimestamp(FilesystemInterface::FILESYSTEM_LAST_MODIFIED):NULL;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getFileCreationTimestamp()
     */
    public function getFileCreationTimestamp() :?int{
        return $this->exists()?$this->stream->getTimestamp(FilesystemInterface::FILESYSTEM_CREATED):NULL;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getSize()
     */
    public function getSize() :?int{
        return !$this->exists()?NULL:$this->stream->getSize();
    }
    
    /**
     * {@inheritDoc}
     * **NOTE:** If the path is invalid and the type is HASH_FILE, we will return back the hash of contents.
     * @see \Anweshan\Filesystem\File\FileInterface::getHash()
     */
    public function getHash(string $algorithm, ?string $key = '', int $type = FilesystemInterface::HASH_CONTENTS) :?string{
        if($this->exists()){
            if($type == FilesystemInterface::HASH_CONTENTS){
                return $this->stream->getHash($algorithm, $key);
            }
            
            if($type == FilesystemInterface::HASH_FILE){
                // If the path is NULL, send HASH_CONTENTS.
                if($this->getPath() == NULL || !is_file($this->getPath()) || !is_readable($this->getPath())){
                    return $this->getHash($algorithm, $key);
                }
                // If the path isset, so it is a file. Now re-implementing the hash_file & hash_hmac_file stuff.
                $call = 'hash';
                $args = [$algorithm, $this->getPath()];
                
                if(strlen($key) != 0){
                    $call .= '_hmac';
                    $args[] = $key;
                }
                $call .= '_file';
                return call_user_func_array($call, $args);
            }
            // If anything otherwise goes wrong.
            //return NULL
        }
        return NULL;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\FilesystemInterface::is()
     */
    public function is(int $type) : bool{
        if($type == FilesystemInterface::IS_FILE){
            return true;
        }
        return false;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::__toString()
     */
    public function __toString() :string{
        if(!$this->exists()){
            return "";
        }
        return ($this->read()==NULL)?"":$this->read();
    }
}