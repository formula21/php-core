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
 
namespace Anweshan\Image\Manipulators\Helpers;
use Intervention\Image\Image;

/**
 * The class Dimension, is a helper class to determine the dimension of a resource.
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators\Helpers
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */       
class Dimension {
	
	/**
	 * The source image.
	 * @var Image $image The source image.
	 */
	protected $image;
	
	/**
	 * The device pixel ratio.
	 * @var float $dpr The device pixel ratio.
	 */
	protected $dpr;
	
	 /**
     * Create dimension helper instance.
     * @param Image $image The source image.
     * @param float $dpr   The device pixel ratio. `Default = 1`
     * @return void
     */
	public function __construct(Image $image, float $dpr = 1.0)
	{
		$this->image = $image;
		$this->dpr = $dpr;
	}
	
	/**
	 * Resolve the dimension.
	 * @param  string $value The dimension value.
	 * @return float The resolved dimension.
	 */
	public function get($value) : float
	{
		if (is_numeric($value) and $value > 0 ) {
			return (float) $value * $this->dpr;
		}
		
		$matches = array();
		
		if (preg_match('/^(\d{1,2}(?!\d)|100)(w|h)$/', $value, $matches)) {
			if ($matches[2] === 'h') {
				return (float) $this->image->height() * ($matches[1] / 100);
			}
			
			return (float) $this->image->width() * ($matches[1] / 100);
		}
	}
}

