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
namespace Anweshan\System;


/**
 * The interface CacheInterface is implemented for manipulating cache data.
 *
 * @package Anweshan\System
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
interface CacheInterface{
    
    /**
     * Set the cache.
     * @param mixed $cache The cache data.
     */
    public function setCache($cache) : void;
    
    /**
     * Get the cache.
     * @return mixed The cache data.
     */
    public function getCache();
    
    /**
     * Set the cache path prefix.
     * @param string $cachePathPrefix The cahce path prefix.
     */
    public function setCachePathPrefix(string $cachePathPrefix): void;
    
    /**
     * Get the cache path prefix.
     * @return string The cache path prefix
     */
    public function getCachePathPrefix() : string;
    
    /**
     * Set the group cache in folders setting.
     * @param bool $groupCacheInFolders Whether to group cache in folders.
     */
    public function setGroupCacheInFolders(bool $groupCacheInFolders): void;
    
    /**
     * Get the group cache in folders setting.
     * @return bool Whether to group cache in folders.
     */
    public function getGroupCacheInFolders() : bool;
    
    /**
     * Set the cache with file extensions setting.
     * @param bool $cacheWithFileExtensions Whether to cache with file extensions.
     */
    public function setCacheWithFileExtensions(bool $cacheWithFileExtensions);
    
    /**
     * Get the cache with file extensions setting.
     * @return bool Whether to cache with file extensions.
     */
    public function getCacheWithFileExtensions() : bool;
    
    /**
     * Get cache path.
     * @param mixed $path  The path to obtain the cache.
     * @return mixed The cache path.
     */
    public function getCachePath($path);
    
    /**
     * Check if a cache file exists.
     * @param mixed path The path parameter, to search if cache file exists.
     * @return bool	`true` the cache exists, `false` otherwise.
     */
    public function cacheFileExists($path) : bool;
    
    /**
     * Delete cache states stored.
     * @return bool `true` the delete succeeded, `false` otherwise.
     */
    public function deleteCache() : bool;
    
}