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

use Anweshan\Filesystem\File\FileInterface;
use Anweshan\Filesystem\FilesystemInterface;

/**
 * The interface StreamInterface defines all the methods to implement a Stream.
 * 
 * The interface is itself `extends` FilesystemInterface. So the instanceof this interface is interchangable.
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
interface StreamInterface extends FilesystemInterface{
	
    /**
     * Checks if the stream exists and is not NULL.
     * @return bool True is returned if the stream is valid, false otherwise.
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\FilesystemInterface::exists()
     */
    public function exists() : bool;
    
	/**
	 * Reads the stream.
	 * @return string|null
	 */
	public function read();
		
	/**
	 * Return the mime of the stream.
	 * @return string|null The mime of the stream
	 */
	public function getMime();
	
	/**
	 * The stream size in bytes.
	 * @return integer|null The size of the string
	 */
	public function getSize();
	
	/**
	 * Get the stream hash.
	 * @param string $algorithm The algorithms specified in hash_algos
	 * @return string|null The hash of the stream
	 */
	public function getHash(string $algorithm, string $key = '');
	
	/**
	 * Checks if we have a name for the stream, such as from a file.
	 * @return bool
	 */
	public function isFilename() : bool;
	
	/**
	 * Checks if we have an extension 
	 * @return bool
	 */
	public function isExtension() : bool;
	
	/**
	 * Return filename or NULL;
	 * @return string|null
	 */
	public function getFilename();
	
	/**
	 * Return extension or null;
	 * @return string|null
	 */
	public function getExtension();
    
	/**
	 * Sets a canonical path to a file from where the stream was derived. 
	 * 
	 * A canonical file-path would be one from where a stream is derived. Like an image file which is run through manipulators and now has been interpreted as a stream.
	 * 
	 * @param string $path The canonical path of the file.
	 * @return StreamInterface
	 */
	public function setCanonicalPath(?string $path) : StreamInterface;
	
	/**
	 * Gets the canonical path, if it was set.
	 * @return string|NULL The path of the canonical file.
	 */
	public function getCanonicalPath() :?string;
	
	/**
	 * Sets the timestamp properties, just like a file.
	 * @param int $property The property which is being set.
	 * @param int $value The value of the property. Proceed with 0, to store null.
	 * @return StreamInterface
	 */
	public function setTimestamp(int $property, int $value) : StreamInterface;	
	
	/**
	 * Gets the timestamp property, just like a file.
	 * @param int $property THe property which is being queried.
	 * @return int|NULL The UNIX-like timestamp of the property in query is returned.
	 */
	public function getTimestamp(int $property) : ?int;
	
	/**
	 * An instance of FileInterface which is helpful.
	 * @param bool $canonical `true` to use canonical file path, `false` is used otherwise.
	 * @return FileInterface|null 
	 */
	public function toFile(bool $canonical = false);
	
}

