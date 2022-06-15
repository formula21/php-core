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
 * The class Contrast manipulates the contrast of an image.
 * 
 * By definition, **Contrast** is the difference in luminance or colour that makes an object (or its representation in an image or display) distinguishable. In visual perception of the real world, contrast is determined by the difference in the colour and {@link \Anweshan\Image\Manipulators\Brightness brightness} of the object and other objects within the same field of view.
 * 
 * Use values between `-100 and +100` where `0` represents no change.
 * 
 * @property string $con Adjusts the image contrast.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Contrast extends BaseManipulator {

	/**
	 * Perform contrast image manipulation.
	 * {@inheritdoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image {
		$con = $this->getContrast();
		
		if ($con !== null) {
			$image = $image->contrast($con);
		}
		
		return $image;
	}
	
	/**
	 * Resolve contrast amount.
	 * @return null|int The resolved contrast amount or NULL
	 */
	public function getContrast(){
		if (null === $this->con || !preg_match(parent::NUMERIC_REGEX, $this->con) || ($this->con < -100 || $this->con > 100)) 
		{
			return;
		}
		return (int) $this->con;
	}
	
	/**
	 * Returns the Contrast property.
	 * {@inheritDoc}
	 */
	public function __toString(): string {
		return parent::getProperties('con');
	}

}

