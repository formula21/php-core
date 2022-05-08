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
namespace Anweshan\Filesystem\File;

use Anweshan\Exception\InvalidArgumentException;
use Anweshan\Filesystem\FilesystemInterface;
use Anweshan\Filesystem\Directory\DirectoryInterface;
use Anweshan\Filesystem\Directory\Directory;

/**
 * The class File, is an wrapper which is itself represents a "computer file" & it's properties.
 *
 * To elaborate, a computer file is a computer resource for recording data in a computer storage device, primarily identified 
 * by its file name. This wrapper is esentially an instanceof FileInterface, which looks for and gets the different properties 
 * of the file.
 * 
 * The uniqueness of a file is determined at the user level limited to the "name" by which it is represented.
 *
 * @package Anweshan\Filesystem
 * @subpackage File
 *
 * @author Anweshan
 * @since 2021-API
 * @version 2
 * @copyright Notice of Copyright included by ARC Groups LLC. All Rights Reserved.
 * @license MIT
 */
class File implements FileInterface{

    private $path = NULL;


    public function __construct($path)
    {
        $this->path = !realpath($path) ? $path : realpath($path);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getPath()
     */
    public function getPath() : ?string
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::exists()
     */
    public function exists(): bool
    {
        return (is_file($this->path) && file_exists($this->path));
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getFilename()
     */
    public function getFilename() :?string
    {
        if(!$this->exists()){
            return NULL;
        }
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getExtension()
     */
    public function getExtension() :?string
    {
        if(!$this->exists()){
            return NULL;
        }
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getBasename()
     */
    public function getBasename() :?string
    {
        if(!$this->exists()){
            return NULL;
        }
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getDir()
     */
    public function getDir() : ?DirectoryInterface
    {
        if(!$this->exists()){
            return NULL;
        }
        $dir = new Directory(pathinfo($this->path, PATHINFO_DIRNAME));
    }

    /**
     *
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::isReadable()
     */
    public function isReadable(): bool
    {
        return $this->exists() && is_readable($this->path);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getSize()
     */
    public function getSize() :?int
    {
        if(!$this->exists()){
            return NULL;
        }
        return filesize($this->path);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::read()
     */
    public function read() :?string
    {
        if(!$this->exists() || !$this->isReadable()){
            return false;
        }
        return file_get_contents($this->path);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getMime()
     */
    public function getMime() :?string
    {
        $mime_obj = new \Mimey\MimeTypes;
        $mime = NULL;

        if(!$this->exists() || !$this->isReadable()){
            throw new FileException("File is non usable");
        }


        if($this->getExtension()){
           $mime = $mime_obj->getMimeType($this->getExtension());
        }else{
           $data = $this->read();
           $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $data);
        }

        # Problem faced in PHP 7.3 and below
        # See https://3v4l.org/K2jqo
        if($mime === 'image/svg'){
            $mime = 'image/svg+xml';
        }

        return $mime;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getLastAccessedTimestamp()
     */
    public function getLastAccessedTimestamp() :?int
    {
        if(!$this->exists() || !$this->isReadable()){
            return false;
        }
        return fileatime($this->path);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getLastModifiedTimestamp()
     */
    public function getLastModifiedTimestamp() :?int
    {
        if(!$this->exists() || !$this->isReadable()){
            return false;
        }
        return filemtime($this->path);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getFileCreationTimestamp()
     */
    public function getFileCreationTimestamp() :?int
    {
        if(!$this->exists() || !$this->isReadable()){
            return false;
        }
        return filectime($this->path);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::getHash()
     */
    public function getHash(string $algorithm, ?string $key = '', int $hash_type = FilesystemInterface::HASH_CONTENTS):?string
    {
        $hash_algo_functions = hash_algos();
        $hash_function = array('hash');
        $arguments = array();

        if($key != NULL && is_string($key) && strlen($key) > 0){
            $hash_algo_functions = hash_hmac_algos();
            $hash_function[] = 'hmac';
        }

        if(!$this->exists()){
            return null;
        }

        $algorithm = strtolower($algorithm);

        if(!in_array($algorithm, $hash_algo_functions, true)){
            throw InvalidArgumentException("${algorithm} is unknown");
        }

        $arguments[] = $algorithm;

        if($hash_type == FilesystemInterface::HASH_CONTENTS){
            $arguments[] = $this->read();
        }

        if($hash_type == FilesystemInterface::HASH_FILE){
            $hash_function[] = 'file';
            $arguments[] = $this->path;
        }

        if(in_array('hmac', $hash_function, true)){
            $arguments[] = $this->key;
        }

        if(count($arguments) <= 1){
            throw new InvalidArgumentException("hash_type = ${hash_type} is invalid");
        }

        $hash_function = implode('_', $hash_function);

        $hash = call_user_func_array($hash_function, $arguments);

        return $hash;

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
     * @see \Anweshan\Filesystem\File\FileInterface::isWritable()
     */
    public function isWritable(): bool
    {
        return $this->exists() && is_writable($this->path);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\File\FileInterface::__toString()
     */
	public function __toString(): string
    {
        if(!$this->exists() || !$this->isReadable()){
            throw new FileException("Invalid File.");
        }
        return (string) ($this->read());
    }

}
