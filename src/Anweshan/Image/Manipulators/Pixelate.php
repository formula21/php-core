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
 *
 * The class Pixelate, helps with the pixelation of an image.
 * 
 * By definition, **Pixelation** is the term used in computer graphics to describe blurry sections or fuzziness in an image due to visibility of single-colored square display elements or individual pixels.
 * 
 * @property int $pixel The pixelation property, between 0 and 1000
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 *        
 */
class Pixelate extends BaseManipulator {
	
	/**
	 * Perform pixelate image manipulation.
	 * {@inheritDoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image{
		$pixelate = $this->getPixelate();
		
		if ($pixelate !== null) {
			$image = $image->pixelate($pixelate);
		}
		
		return $image;
	}
	
	/**
	 * Resolve pixelate amount.
	 * @return int|null The resolved pixelate amount.
	 */
	public function getPixelate()
	{
		if (!is_numeric($this->pixel) || $this->pixel < 0 or $this->pixel > 1000) {
			return;
		}
		
		return (int) $this->pixel;
	}
	
	/**
	 * Returns the pixelation property.
	 * {@inheritDoc}
	 */
	public function __toString() : string {
		return parent::getProperties('pixel');
	}
}

