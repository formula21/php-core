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

use Anweshan\Filesystem\FilesystemInterface;

/**
 * The interface FileInterface defines all the methods required for a File to co-exists in a Filesystem.
 * 
 * The interface is itself `extends` FilesystemInterface. So the instanceof this interface is interchangable.
 * 
 * @package Anweshan\Filesystem
 * @subpackage File
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
interface FileInterface extends FilesystemInterface
{
    /**
     * Checks if the file exists and is a valid file.
     * @return bool Returns true if the file exists, false otherwise.
     * {@inheritdoc}
     * @see \Anweshan\Filesystem\FileSystemInterface::exists()
     */
    public function exists() : bool;

    /**
     * Gets the filename.
     * @return string|null
     */
    public function getFilename() :?string;
    
    /**
     * Gets the basename.
     * @return string|null
     */
    public function getBasename() :?string;
    
    /**
     * Gets the extension.
     * @return string|null
     */
    public function getExtension() :?string;
    
    /**
     * Gets the directory.
     * @return string|null
     */
    public function getDir() :?string;
    
    /**
     * Gets if the file is writable.
     * @return bool `true` if file is writable.
     */
    public function isWritable() : bool;
    
    /**
     * Gets if the file is readable.
     * @return bool `true` if file is readable.
     */
    public function isReadable() : bool;
    
    /**
     * Gets the last modified timestamp.
     * @return int|null The last modified timestamp.
     */
    public function getLastModifiedTimestamp() :?int;
    
    /**
     * Gets the last accessed timestamp.
     * @return int The last accessed timestamp.
     */
    public function getLastAccessedTimestamp() :?int;
    
    /**
     * Gets the creation timestamp.
     * @return int The created timestamp.
     */
    public function getFileCreationTimestamp() :?int;
    
    /**
     * Read the file contents.
     * @return string|null
     */
    public function read() :?string;
    
    /**
     * Gets the mime of the file.
     * @return string|null
     */
    public function getMime() :?string;
    
    /**
     * Gets the full-path of the file.
     * @return string|null
     */
    public function getPath() :?string;
    
    /**
     * Gets the size of the file (in bytes).
     * **Note**: Because PHP's integer type is signed and many platforms use 32bit integers, some filesystem functions may return unexpected results for files which are larger than 2GB.
     * @return int|null The size of the file.
     */
    public function getSize() :?int;
    
    /**
     * Gets the hash of the file.
     * @param string $algorithm The hash algorithm.
     * @param string|null $key The key to implement hash_hmac.
     * @param string $type The type signifies which has is demanded.
     * @return string|null The hash of the file or contents.
     */
    public function getHash(string $algorithm, ?string $key = '', int $type = FilesystemInterface::HASH_CONTENTS) :?string;
    
    /**
     * Gets the string implementation of the instance.
     * @return string The contents of the file or an empty string.
     */
    public function __toString() : string;
}

