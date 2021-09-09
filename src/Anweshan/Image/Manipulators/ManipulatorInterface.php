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
namespace Anweshan\Image\Manipulators;

use Intervention\Image\Image;

/**
 * The interface ManipulatorInterface are some predefined methods to be implemented by any
 * manipulator.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
interface ManipulatorInterface
{
    /**
     * Set the manipulation params.
     * @param string[] $params The manipulation params.
     * @return mixed
     */
    public function setParams(array $params);
    
    /**
     * Get the manipulation parameters
     * @return string[] The manipulation parameters
     */
    public function getParams();
    
    /**
     * Perform the image manipulation.
     * @param Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image) : Image;
    
    /**
     * Get the property name or names, which is (or are) being manipulated.
     * If there is more than a single property being manipulated, a comma seperated string is returned
     * @return string A comma-seperated string of properties are returned.
     */
    public function __toString() : string;
}

