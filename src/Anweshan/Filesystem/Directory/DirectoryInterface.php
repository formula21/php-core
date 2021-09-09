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
namespace Anweshan\Filesystem\Directory;

use Anweshan\Filesystem\File\FileInterface;
use Anweshan\Filesystem\Stream\StreamInterface;
use Anweshan\Filesystem\FilesystemInterface;

/**
 * The interface DirectoryInterface defines all the methods required for a Directory to co-exists in a Filesystem.
 *
 * The interface is itself `extends` FilesystemInterface. So the instanceof this interface is interchangable.
 *
 * @package Anweshan\Filesystem
 * @subpackage Directory
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
interface DirectoryInterface extends FilesystemInterface
{
    /**
     * Checks if the directory exists and is a valid directory.
     * @return bool Returns true if the directory exists, false otherwise.
     * {@inheritdoc}
     * @see \Anweshan\Filesystem\FileSystemInterface::exists()
     */
    public function exists() : bool;

    /**
     * The method makes the full path, combining the directory and the concerned path.
     * @param string $path The path to be used.
     * @return string|NULL The full path.
     */
    public function fullPath(string $path) : ?string;

    /**
     * Gets the directory path.
     * @return string|NULL The path to the directory.
     */
    public function getDir() :?string;

    /**
     * Checks if the path exists.
     * @param string $path The path to check.
     * @return bool Returns true, if path exists, false otherwise.
     */
    public function has(string $path) :bool;

    /**
     * Gets an instanceof FileInterface from the path.
     *
     * @param string $path The path of the file/directory.
     * @return FileInterface|DirectoryInterface|NULL If it is a valid path, return a FileInterface, or null.
     */
    public function get(string $path);

    /**
     * Reads the contents from the path.
     * @param string|FileInterface $path The path to be read, or an instanceof FileInterface.
     * @return string|NULL The contents of the file or nothing.
     */
    public function read($path) :?string;

    /**
     * Writes contents to file.
     * @param StreamInterface $contents The stream whose contents is to be written.
     * @param string $path The path to be written. An empty string signifies write in same directory.
     * @param bool $mkdir The flag signifies if the path is not there, make the directories.
     * @param bool $unlink The paramter signifies if unlinking of directory(ies) are required.
     * @return FileInterface|null The fileinterface of contents.
     */
    public function write(?StreamInterface $contents, string $path = '', bool $mkdir = true, bool $unlink = true) : ?FileInterface;
}
