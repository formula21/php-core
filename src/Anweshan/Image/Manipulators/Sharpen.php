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
 * The class Sharpen, sharpens the image.
 * 
 * By definition, Image **sharpening** refers to any enhancement technique that highlights edges and fine details in an image. Image sharpening is widely used in printing and photographic industries for increasing the local contrast and sharpening the images.
 * 
 * @property int $sharp The sharpen property, between 0 and 100.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Sharpen extends BaseManipulator {
	
	/**
	 * Perform sharpen image manipulation.
	 * {@inheritDoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image): Image {
		$sharpen = $this->getSharpen();
		
		if ($sharpen !== null) {
			$image = $image->sharpen($sharpen);
		}
		
		return $image;
	}
	
	/**
	 * Resolve sharpen amount.
	 * @return int|null The resolved sharpen amount.
	 */
	public function getSharpen()
	{
		if (!is_numeric($this->sharp) || $this->sharp < 0 or $this->sharp > 100) {
			return;
		}
		
		return (int) $this->sharp;
	}
	
	/**
	 * Returns the sharpen property.
	 * {@inheritDoc}
	 */
	public function __toString(): string {
		return parent::getProperties('sharp');
	}
}

