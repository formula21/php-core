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
use Anweshan\Image\Manipulators\Helpers\Color;
use Intervention\Image\Image;

/**
 * The class Background is one which manipulates the background color of the image.
 * 
 * For color, please refer {@link \Anweshan\Image\Manipulators\Helpers\Color}.
 * 
 * @property string $bg The background color.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT   
 */
class Background extends BaseManipulator {

	/**
	 * Perform background image manipulation.
	 * {@inheritdoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image {
		if (is_null($this->bg)) {
			return $image;
		}
		
		$color = (new Color($this->bg))->formatted();
		
		if ($color) {
			$new = $image->getDriver()->newImage($image->width(), $image->height(), $color);
			$new->mime = $image->mime;
			$image = $new->insert($image, 'top-left', 0, 0);
		}
		
		return $image;
	}
	
	/**
	 * Return property name of Background Manipulator $bg.
	 * {@inheritDoc}
	 */
	public function __toString() : string {
		return parent::getProperties('bg');
	}
}

