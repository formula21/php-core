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
 * The class filter, performs the filter manipulation, by applying custom filters to an image.
 *  
 * The built-in filters:
 * - Sepia
 * - Greyscale
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT       
 */
class Filter extends BaseManipulator {
	
	/**
	 * The filter types as keys with their respective methods as values.
	 * @var string[] FILTER The filter types as keys with their respective methods as values.
	 * @access private This cannot be modified or accessed by anyone, other than by the class.
	 */
	private const FILTER = [
		'greyscale'=>'runGreyscaleFilter',
		'sepia'=>'runSepiaFilter',
	];
	
	/**
	 * Returns the filter property.
	 * {@inheritdoc}
	 */
	public function __toString() : string {
		return parent::getProperties('filt');
	}

	/**
	 * Performs the filter image manipulation.
	 * {@inheritdoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image{
		
		if($this->filt){
			if(array_key_exists($this->filt, self::FILTER)){
				$v = self::FILTER[$this->filt];
				$image = $this->$v($image);
			}
		}
		
		return $image;
	}
	
	/**
	 * Runs the greyscale manipulation.
	 * @param Image $image The source image
	 * @return Image Returns the greyscale image
	 */
	public function runGreyscaleFilter(Image $image): Image{
		return $image->greyscale();
	}
	
	/**
	 * Runs the sepia manipulation.
	 * @param Image $image The source image.
	 * @return Image Returns the sepia image.
	 */
	public function runSepiaFilter(Image $image): Image {
		$image->greyscale()->brightness(-10)->contrast(10)->colorize(38, 27, 12)
			  ->brightness(-10)->contrast(10);
		
		return $image;
	}
}

