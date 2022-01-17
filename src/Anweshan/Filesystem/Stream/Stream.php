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

use Anweshan\Exception\InvalidArgumentException;
use Anweshan\Filesystem\FilesystemInterface;

/**
 * The class Stream implements a memory-allocated buffer to act like a "stream" in a Filesystem.
 *
 * The class implements (and thus an `instanceof`) StreamInterface.
 *
 * We define a stream to be a memory-allocated temporary buffer where contents (raw, string, binary) can be stored, modified, accessed, written. Once a stream is defined or allocated it cannot be completely erased from the memory, till the same is destroyed.
 *
 * A stream can be derived from a file and its contents (before/after alteration). The file which populates the stream is known as a "canonical file". A stream here-in would have many similarities to act like a File based on the {@link \Anweshan\Filesystem\File\FileInterface FileInterface}, like (but not limited to):
 * - A last-modified timestamp
 * - A last-accessed timestamp
 * - A creation timestamp
 * - A canonical path
 * - Size (in bytes)
 * - File Name
 * - File Extension
 * - File Mime
 *
 *
 * @package Anweshan\Filesystem
 * @subpackage Stream
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Stream implements StreamInterface
{
    /**
     * The memory-allocated buffer.
     * @var mixed Some immutable buffer.
     */
    private $data = NULL;

    /**
     * The canonical path of the file
     * @var string The canonical path of the file, from where the stream was derived.
     */
    private $path = NULL;

    /**
     * The filename of the stream.
     * @var int The name of the file which wraps the stream.
     */
    private $filename = NULL;

    /**
     * The extension of the stream.
     * @var string The extension of the file which wraps the stream.
     */
    private $extension = NULL;

    /**
     * The timestamp when the canonical-file was accessed w.r.t the stream.
     * @var int The UNIX-like timestamp.
     */
    private $lastAccessed = NULL;

    /**
     * The timestamp when the canonical-file was modified w.r.t the stream.
     * @var int The UNIX-like timestamp.
     */
    private $lastModified = NULL;

    /**
     * The timestamp when the canonical-file was created w.r.t the stream.
     * @var int The UNIX-like timestamp.
     */
    private $fileCreated = NULL;


    /**
     * Constructs a stream of data.
     * @param mixed $data Data to be streamed. This is immutable.
     * @param string $filename The filename if any.
     * @param string $extension The file extension if any.
     * @param string $path The path of the file (if any).
     */
    public function __construct($data, ?string $filename = NULL, ?string $extension = NULL, ?string $path = NULL){
        $this->data = $data;
        $this->setFilename($filename)->setExtension($extension)->setCanonicalPath($path);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::exists()
     */
    public function exists() : bool{
        return $this->data != NULL;
    }

    /**
     * {@inheritDoc}
     * @throws InvalidArgumentException Raised if the property is unknown.
     * @see \Anweshan\Filesystem\Stream\StreamInterface::setTimestamp()
     */
    public function setTimestamp(int $property, int $value) : StreamInterface {
        if($this->data == NULL || ($value < 0 || $value > time()) || ($value = ($value == 0)?NULL:$value)){
            return $this;
        }

        switch($property){
            case FilesystemInterface::FILESYSTEM_LAST_ACCESSED:
                $this->lastAccessed = $value;
                break;
            case FilesystemInterface::FILESYSTEM_LAST_MODIFIED:
                $this->lastModified = $value;
                break;
            case FilesystemInterface::FILESYSTEM_CREATED:
                $this->fileCreated = $value;
                break;
        }

        return $this;
    }

    public function getTimestamp(int $property) :? int{
        $value = $this->data;
        if($value){
            switch($property){
                case FilesystemInterface::FILESYSTEM_LAST_ACCESSED:
                    $value = $this->lastAccessed;
                    break;
                case FilesystemInterface::FILESYSTEM_LAST_MODIFIED:
                    $value = $this->lastModified;
                    break;
                case FilesystemInterface::FILESYSTEM_CREATED:
                    $value = $this->fileCreated;
                    break;
                default:
                    throw new InvalidArgumentException('Invalid property');
            }
        }
        return $value;
    }

    /**
     * Sets the filename
     * @param mixed $filename The file's name.
     * @return StreamInterface The instanceof StreamInterface.
     */
    public function setFilename($filename) : StreamInterface{
        if($filename == NULL)
        {
            $this->filename = $filename;
            return $this;
        }

        $f = preg_match('/^(?!.{256,})(?!^(aux|clock\$|con|nul|prn|com[1-9]|lpt[1-9])(?:$|\.)$)[^\x00-\x1f \?\*\:\"\|\/\<\>\\\.][A-Za-z\w\d\.\_\-]*[^\?\*\:\"\|\/\<\>\\\.]$/i', $filename);

        if($this->exists() && is_string($filename) && strlen($filename) > 0 && $f !== false){
            $this->filename = $filename;
        }

        return $this;
    }

    /**
     * Sets the extension of the file.
     * @param mixed $extension The extension of the file.
     * @return StreamInterface
     */
    public function setExtension($extension) : StreamInterface{
        if($extension == NULL){
            $this->extension = $extension;
            return $this;
        }

        if($this->exists() && is_string($extension) && strlen($extension) != 0){
            if($extension[0] === '.')
                $extension = substr($extension, 1);
            $this->extension = $extension;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::isFilename()
     */
    public function isFilename() : bool{
        return ($this->filename != NULL);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::isExtension()
     */
    public function isExtension() : bool{
        return ($this->extension != NULL);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::getFilename()
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::getExtension()
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::getSize()
     */
    public function getSize()
    {
        if($this->exists() == false)
        {
            return false;
        }

        return strlen($this->data);
    }

    /**
     * {@inheritDoc}
     * Please note: The path must be a file, or nothing is set or overriden. However if null is sent, and the data-member is not NULL, the value is overriden to store NULL.
     *
     * @see \Anweshan\Filesystem\Stream\StreamInterface::setCanonicalPath()
     */
    public function setCanonicalPath(?string $path): StreamInterface{
        if($path == NULL && $this->path != NULL){
            $this->path = NULL;
            return $this;
        }
        if(is_file($path)){
            $this->path = $path;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::getCanonicalPath()
     */
    public function getCanonicalPath() : ?string {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::read()
     */
    public function read()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::getHash()
     */
    public function getHash(string $algorithm, string $key = '')
    {
        $hash_algo_functions = hash_algos();
        $hash_function = array('hash');

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

        $arguments = array($algorithm, $this->read());

        if(in_array('hmac', $hash_function, true)){
            $arguments[] = $this->key;
        }


        $hash_function = implode('_', $hash_function);

        $hash = call_user_function_array($hash_function, $arguments);

        return $hash;

    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\FilesystemInterface::is()
     */
    public function is(int $type) : bool{
        if($type == FilesystemInterface::IS_STREAM){
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Stream\StreamInterface::getMime()
     */
    public function getMime()
    {
        if(!$this->exists()){
            return false;
        }

        $mime_obj = new \Mimey\MimeTypes;

        $mime = NULL;
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
     * @see \Anweshan\Filesystem\Stream\StreamInterface::toFile()
     */
    public function toFile(bool $canonical = false)
    {
        if(!$this->exists()){
            return NULL;
        }

        if($canonical && $this->getCanonicalPath() != NULL){
            if($this->filename == NULL){
                $this->setFilename(pathinfo($this->getCanonicalPath(), PATHINFO_FILENAME));
            }
            if($this->extension == NULL){
                $this->setExtension(pathinfo($this->getCanonicalPath(), PATHINFO_EXTENSION));
            }
            if($this->lastModified == NULL){
                $this->lastModified = filemtime($this->getCanonicalPath())?:NULL;
            }
            if($this->lastAccessed == NULL){
                $this->lastAccessed = fileatime($this->getCanonicalPath())?:NULL;
            }
            if($this->fileCreated == NULL){
                $this->fileCreated = filectime($this->getCanonicalPath())?:NULL;
            }
        }

        if($this->filename == NULL){
            throw new InvalidArgumentException("No filename set.");
        }

        if($this->extension == NULL){
            throw new InvalidArgumentException("No extension set.");
        }

        $file = new File($this);
        return $file;
    }
}
