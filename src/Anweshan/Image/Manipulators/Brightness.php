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
 * The class Brightness manipulates the brightness of the image.
 * 
 * Use values between ``-100 and +100``, where ``0`` represents no change.
 *  
 * @property string $bri Adjusts the image brightness.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Brightness extends BaseManipulator {

	/**
	 * Perform brightness image manipulation.
	 * {@inheritdoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image {
		$bri = $this->getBrightness();
		if($bri !== null){
			$image = $image->brightness($bri);
		}
		return $image;
		
	}
	
	/**
	 * Resolve brightness amount.
	 * @return int|null The resolved brightness.
	 */
	public function getBrightness(){
		if (null === $this->bri || !preg_match(parent::NUMERIC_REGEX, $this->bri) || ($this->bri < -100 || $this->bri > 100))
			return;
		return (int)$this->bri;
	}
	
	/**
	 * Returns the brightness property.
	 * {@inheritDoc}
	 * 
	 */
	public function __toString(): string {
		return parent::getProperties('bri');
	}

}

