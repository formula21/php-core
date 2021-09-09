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
use Anweshan\Image\Manipulators\Helpers\Color;

/**
 * The class Orientation, changes the view/angle/orientation of the image on a canvas.
 * 
 * The most common **orientation** practices are landscape and potrait (used in all handheld devices).
 * The angles of orientation are between -360&deg; (clockwise) to +360&deg; (counter-clockwise).
 * If nothing is provided, the `or=auto` is assumed.
 * After rotation, the empty space is filled with a background color.
 * 
 * @property string|int $or The orientation in degrees (default auto).
 * @property string $bg The background color, to fill after rotation.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Orientation extends BaseManipulator {
	
	/**
	 * The six digit hex-code for the color transparent.
	 * @var string TRANSPARENT The six digit hex-code for the color transparent.
	 */
	public const TRANSPARENT = 'ffffff';
	
	/**
	 * Perform orientation image manipulation.
	 * {@inheritDoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image{
		$bg = $this->getBackground();
		$or = $this->getOrientation();
		
		if($or === 'auto'){
			return $image->orientate();
		}
		
		return $image->rotate($or, $bg);
	}
	
	
	/**
	 * Resolve orientation.
	 * @return string|int The orientation angle or value
	 */
	public function getOrientation(){
		if(!preg_match(parent::NUMERIC_REGEX, $this->or) || $this->or < -360 || $this->or > 360){
			return 'auto';
		}
		$this->or = $this->or % 360;
		return intval($this->or);
	}
	
	/**
	 * Detects the background color and returns a rgba string.
	 * @return string|null A background color. Default transparent if supported.
	 */
	public function getBackground() {
		if(is_null($this->bg) || !is_string($this->bg)){
			return self::TRANSPARENT;
		}
		$color = new Color($this->bg);
		return $color->formatted();
	}
	
	/**
	 * Return the Orientation properties.
	 * {@inheritDoc}
	 */
	public function __toString() : string {
		return parent::getProperties('or','bg');
	}
}

