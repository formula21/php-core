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
 * The class Blur, blurs the image.
 * 
 * The concept of **blur**, or smoothing, of an image removes "outlier" pixels that may be noise in the image. Blurring is an example of applying a low-pass filter to an image.
 * 
 * A blur scale is between `0` to `100`, both inclusive.
 *
 * @property string $blur The blur property.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Blur extends BaseManipulator {

	/**
	 * Performs blur image manipulation.
	 * {@inheritdoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image {
		$blur = $this->getBlur();
		
		if ($blur !== null) {
			$image = $image->blur($blur);
		}
		
		return $image;
	}
	
	/**
	 * Resolve blur amount.
	 * @return int|null The resolved blur
	 */
	public function getBlur(){
		if (!is_numeric($this->blur) || ($this->blur < 0 or $this->blur > 100)) {
			return;
		}
		
		return (int) $this->blur;
	}
	
	/**
	 * Returns the Blur manipulator property $blur
	 * {@inheritDoc}
	 */
	public function __toString() : string {
		return parent::getProperties('blur');
	}
	
	
}

