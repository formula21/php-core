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
namespace Anweshan\Filesystem;

/**
 * The interface FilesystemInterface is the parent interface to all other interfaces to be defined or co-exists in the filesystem of the system (Operating System).
 *
 * Currently there are three main child-interfaces. They are linked and listed from the ascending order of their existence in an Operating System:
 * - {@link \Anweshan\Filesystem\Directory\DirectoryInterface DirectoryInterface}
 * - {@link \Anweshan\Filesystem\File\FileInterface FileInterface}
 * - {@link \Anweshan\Filesystem\Stream\StreamInterface StreamInterface}
 *
 * Instances of each are both `instanceof` FilesystemInterface and the Filesystem they interface.
 *
 * @package Anweshan\Filesystem
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
interface FilesystemInterface
{
    /**
     * The integer signifies the last time the filesystem was accessed.
     * @var integer The UNIX timestamp.
     */
    public const FILESYSTEM_LAST_ACCESSED = 1;

    /**
     * The integer signifies the last time the filesystem was modified.
     * @var integer The UNIX timestamp.
     */
    public const FILESYSTEM_LAST_MODIFIED = 2;

    /**
     * The integer signifies the time the filesystem was created.
     * @var integer The UNIX timestamp.
     */
    public const FILESYSTEM_CREATED = 3;

    /**
     * The integer signifies the hash of the contents are demanded. (Default)
     * @var integer The integer of Contents.
     */
    public const HASH_CONTENTS = 1;

    /**
     * The integer signifies the hash of the file is demanded.
     * @var integer The integer of File.
     */
    public const HASH_FILE = 2;

    /**
     * Integer denoting the resource "is" a directory.
     * @var integer
     */
    public const IS_DIRECTORY = 0;
    
    /**
     * Integer denoting the resource "is" a file.
     * @var integer
     */
    public const IS_FILE = 1;
    
    /**
     * Integer denoting the resource "is" a stream.
     * @var integer
     */
    public const IS_STREAM = 2;
    
    /**
     * Checks if the resource concerned exists or not.
     * @return bool
     */
    public function exists() : bool;
    
    /**
     * Checks if the resource matches the required integer denoting the resource.
     * It basically check if the resource just called "is" a "file/directory/stream".
     * @param int $type The type of resource integer to compare with.
     * @return bool If the challenge matches return true, else return false.
     */
    public function is(int $type): bool;
}
