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
use Mimey\MimeTypes;

/**
 * 
 * The class Encode, produces the shapes the a displayable image, after/if manipulated.
 * 
 * 
 * @property string $q The `quality` property.
 * @property string $fm The `format` property.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Encode extends BaseManipulator {

	/**
	 * The constructor to encode the image.
	 * @uses BaseManipulator::MIMES to retrieve the mimes.
	 */
	public function __construct(){
	    parent::__construct();
		$mimes = new MimeTypes;
		foreach(array_keys($this->mimes) as $v){
		    $this->mimes[$v] = $mimes->getAllExtensions($v);
		}
	}
	
	/**
	 * The encode image manipulator.
	 * {@inheritdoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image {
		$format = $this->getFormat($image);
		$quality = $this->getQuality();
		
		if (in_array($format, ['jpg', 'pjpg'], true)) {
			$image = $image->getDriver()
					 ->newImage($image->width(), $image->height(), '#fff')
					 ->insert($image, 'top-left', 0, 0);
		}
		
		if ($format === 'pjpg') {
			$image = $image->interlace();
			$format = 'jpg';
		}
		
		return $image->encode($format, $quality);
	}
	
	/**
	 * Resolve format.
	 * @param  Image  $image The source image.
	 * @return string The resolved format.
	 */
	public function getFormat(Image $image)
	{	    
		$allowed = $this->mimes;
		
		foreach($this->mimes as $v){
		  if (in_array($this->fm, $v, true)) {
			 return $this->fm;
		  }
		}
		
		if ($format = array_search($image->mime(), array_keys($allowed), true)) {
		    $format = array_values($allowed)[$format][0];
			return $format;
		}
		
		return 'jpg';
	}
	
	/**
	 * Resolve quality.
	 * @return int The resolved quality.
	 */
	public function getQuality() : int
	{
		$default = 90;
		
		if (!is_numeric($this->q) || ($this->q < 0 or $this->q > 100)) {
			return $default;
		}
		
		return (int) $this->q;
	}
	
	/**
	 * Returns the encode properties.
	 * {@inheritDoc}
	 */
	public function __toString() : string {
		return parent::getProperties('q', 'fm');
	}
	
}

