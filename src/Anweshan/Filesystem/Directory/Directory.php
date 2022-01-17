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


use Anweshan\Filesystem\FilesystemInterface;
use Anweshan\Filesystem\Stream\StreamInterface;
use Anweshan\Exception\InvalidArgumentException;
use Anweshan\Util\Util;
use Anweshan\Filesystem\File\{
    FileInterface, File, FileExistsException, UnwritableFileException
};

/**
 * The class Directory facilitates the storage, reading & writting of other streams and files on the disk and/or memory.
 *
 * The class implements (and thus an `instanceof`) DirectoryInterface.
 *
 * @package Anweshan\Filesystem
 * @subpackage Directory
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Directory implements DirectoryInterface
{
    /**
     * The path which is said to be a directory.
     * @var string The path in the Filesystem.
     */
    private $dir = NULL;

    /**
     * Basic class constructor
     * @param string $dir The directory to be used.
     */
    public function __construct(string $dir){
        $this->setDir($dir);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Directory\DirectoryInterface::fullPath()
     */
    public function fullPath(string $path) : ?string{
        if($this->exists()){
            return Util::makePath($this->dir, Util::sanitizePath($path));
        }
        return NULL;
    }

    /**
     * Sets the directory to be used.
     * @param string $dir The directory to be used.
     * @throws DirectoryNotFoundException Raised if the directory is not found or invalid.
     * @throws InvalidArgumentException Raised if the directory is not a string or is empty.
     */
    protected function setDir(?string $dir){
        if(is_string($dir) && strlen($dir) > 0){
            $dir = Util::trim($dir, '/', DIRECTORY_SEPARATOR);
            if(is_dir($dir) && file_exists($dir)){
                $dir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
                $this->dir = $dir;
            }else{
                throw new DirectoryNotFoundException("${dir} is unknown");
            }
        }else{
            throw new InvalidArgumentException("The directory argument is invalid!!");
        }
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Directory\DirectoryInterface::exists()
     */
    public function exists() : bool {
        if($this->dir != NULL){
            if(!is_dir($this->dir)){
                $this->dir = NULL;
            }
        }

        return ($this->dir != NULL);
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Directory\DirectoryInterface::getDir()
     */
    public function getDir() :?string{
        return $this->dir;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Directory\DirectoryInterface::has()
     */
    public function has(string $path) :bool{
        if(!$this->exists()){
            return false;
        }

        $path = $this->fullPath($path);

        if(file_exists($path) && (is_file($path) || is_dir($path))){
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Directory\DirectoryInterface::get()
     */
    public function get(string $path){
        if($this->exists() && $this->has($path)){
            $path = $this->fullPath($path);
            $file = NULL;
            if(is_dir($path)){
               $file = new Directory($path);
            }
            if(is_file($path)){
              $file = new File($path);
            }

            return $file;
        }
        return NULL;
    }

    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\Directory\DirectoryInterface::read()
     */
    public function read($path) :?string {
        if(is_string($path)){
           return $this->read($this->get($path));
        }
        if(($path instanceof FileInterface)){
            if(!$path->exists() || !$path->isReadable()){
                return NULL;
            }
            return $path->read();
        }
        return NULL;
    }


    /**
     * {@inheritDoc}
     * @throws InvalidArgumentException Raised if any argument is invalid.
     * @throws UnwritableFileException Raised if the {@link Anweshan\Filesystem\File\\FileInterface FileInterface} instance could not be created.
     * @throws InvalidDirectoryException Raised if a directory could not be created.
     * @throws DirectoryException Raised if one or more directories to be created are invalid (like naming, format etc).
     * @throws FileExistsException Raised if a file was tried to be overwritten.
     *
     * @see \Anweshan\Filesystem\Directory\DirectoryInterface::write()
     *
     * @see {@link \Anweshan\Util\Util::rmdir() rmdir()} to remove non-empty directory(ies).
     */
    public function write(?StreamInterface $contents, string $path = '', bool $mkdir = true, bool $unlink = true): ?FileInterface
    {
        $dir = NULL;
        if(!$this->exists()){
            return NULL;
        }

        $path = trim($path);

        if($contents == NULL || !$contents->exists() || 
            (!$contents->isFilename() && !$contents->isExtension())){
            throw new InvalidArgumentException("Not a valid stream.");
        }

        $filename = ($contents->isFilename()==false)?"":$contents->getFilename();
        $filename .= ($contents->isExtension()==false)?"":".".$contents->getExtension();

        if($path === '' || trim($path, '.') === ''){
            // Empty string... or home dir... Even .. => .
            $path = $this->fullPath($filename);
            $x = @file_put_contents($path, $contents->read(), LOCK_EX);
            if(!$x){
                if($unlink && is_file($path)){
                    unlink($path);
                }
                throw new UnwritableFileException("The file could not be written at path `${path}`");
            }
            return new File($path);
        }

        if(strlen(Util::trim($path, '/', DIRECTORY_SEPARATOR)) == 0){
            throw new InvalidArgumentException("Not a valid path");
        }

        $p = $this->fullPath($path);
        if(!is_dir($p) && $mkdir){
            $dir = Util::makeDirectory($path);
            if($dir && is_array($dir) && count($dir) > 0){
                foreach($dir as $k=>$v){
                    $p = $this->fullPath($v);
                    if(!is_dir($p)){
                        if(!@mkdir($p)){
                            throw new InvalidDirectoryException("Cannot make directory at `${p}`");
                        }
                        @clearstatcache();
                    }else{
                        // We unset this, so that by accident
                        // we do not unlink this pre-existing directory
                        unset($dir[$k]);
                    }
                }
            }else{
                throw new DirectoryException("Invalid directory(ies)");
            }
        }

        $path = $this->fullPath($path);
        if(is_dir($path)){
            $path = Util::makePath($path, $filename);
            if(file_exists($path) && is_file($path)){
                // Unlink the directories.
                if($dir && $unlink){
                    foreach($dir as $v){
                        if(is_dir($v)){
                            Util::rmdir($v);
                        }
                    }
                }
                // File overwritting is still not supported.
                throw new FileExistsException("File exists at path `${path}`");
            }
            $x = @file_put_contents($path, $contents->read(), LOCK_EX);
            if(!$x){
                // Unlink the directories.
                if($dir && $unlink){
                    foreach($dir as $v){
                        if(is_dir($v)){
                            Util::rmdir($v);
                        }
                    }
                }
                throw new UnwritableFileException("The file could not be written at path `${path}`");
            }
            return new File($path);
        }

        return NULL;
    }
    
    /**
     * {@inheritDoc}
     * @see \Anweshan\Filesystem\FilesystemInterface::is()
     */
    public function is(int $type, string $path = NULL) : bool{
        
        if(is_string($path) && !empty($path) && $this->has($path)){
            return ($this->get($path)->is($type));
        }
        
        if($type == FilesystemInterface::IS_DIRECTORY && $path == NULL){
            return true;
        }
        return false;
    }

    /**
     * The directory path or empty string.
     * @return string
     */
    public function __toString(){
        return $this->dir?:"";
    }
}
