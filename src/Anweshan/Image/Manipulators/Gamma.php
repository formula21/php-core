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
 * The class Gamma adjusts the image gamma.
 * 
 * By definition, **gamma correction** or simply **gamma** is the relationship between a pixel's numerical value and its actual luminance. Gamma encoding of images is used to optimize the usage of bits when encoding an image, or bandwidth used to transport an image, by taking advantage of the non-linear manner in which humans perceive light and color. It is a very important property of image processing, without which, shades captured by digital cameras wouldn't appear as they did to our eyes (on a standard monitor).
 * 
 * @property int $gam The gamma property, with values between `0.1` and `9.99`.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Gamma extends BaseManipulator {
	
	/**
	 * The numeric regular expression.
	 * @var string NUMERIC_REGEX The numeric regular expression.
	 * @access private This cannot be modified or accessed by anyone, other than by the class.
	 */
	protected const NUMERIC_REGEX = '/^[0-9]\.*[0-9]*$/';
	
	/**
	 * Perform gamma image manipulation.
	 * {@inheritDoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image {
		
		$gamma = $this->getGamma();
		
		if ($gamma) {
			$image = $image->gamma($gamma);
		}
		
		return $image;
	}
	
	/**
	 * Resolve gamma amount.
	 * @return null|float The resolved gamma amount.
	 */
	public function getGamma(){
		if (!preg_match(self::NUMERIC_REGEX, $this->gam) || $this->gam < 0.1 || $this->gam > 9.99) {
			return;			
		}
		return (float) $this->gam;
	}
	
	/**
	 * Returns the Gamma property.
	 * {@inheritDoc}
	 */
	public function __toString() : string{
		return parent::getProperties('gam');
	}
	
}

