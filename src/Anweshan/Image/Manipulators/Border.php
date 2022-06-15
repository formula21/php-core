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
use Anweshan\Image\Manipulators\Helpers\Dimension;

/**
 *
 * The class Border applies a border along the image.
 *
 * By definition of the part or edge of a surface or area that forms its outer boundary is a **border**. The definition here considers outer boundary, however in image processing we have a term **inset**, which adds an inner border.
 *
 * @property string $dpr The device pixel ratio property of the image
 * @property string $border The numeric border property of the image
 *
 * @package Anweshan\Image
 * @subpackage Manipulators
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Border extends BaseManipulator {

	/**
	 * The array of border methods as keys with their respective methods as values.
	 * @var string[] $methods The array of border methods as keys with their respective methods as values.
	 * @access private The array is private for reasons of invalid manuipulations.
	 */
	private static $methods = array(
			'expand'=>'runExpand',
			'shrink' => 'runShrink',
			'overlay' => 'runOverlay',);

	/**
	 * Perform border image manipulation.
	 * {@inheritdoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image{
		if ($border = $this->getBorder($image)) {
			$width = $color = $method = NULL;
			list($width, $color, $method) = $border;
			$function = self::$methods[$method];
			$image = $this->$function($image, $width, $color);
		}
		return $image;
	}



	/**
	 * Resolve border amount.
	 * @param  Image  $image The source image.
	 * @return array|null The resolved border amount.
	 */
	public function getBorder(Image $image)
	{
		if (!$this->border) {
			return;
		}

		$values = explode(parent::COMMA_DELIMITER, $this->border);

		$width = $this->getWidth($image, $this->getDpr(), isset($values[0]) ? $values[0] : null);
		$color = $this->getColor(isset($values[1]) ? $values[1] : null);
		$method = $this->getMethod(isset($values[2]) ? $values[2] : null);

		if ($width) {
			return [$width, $color, $method];
		}
		return;
	}

	/**
	 * Get border width.
	 * @param  Image  $image The source image.
	 * @param  float $dpr   The device pixel ratio.
	 * @param  string $width The border width.
	 * @return float The resolved border width.
	 */
	public function getWidth(Image $image, float $dpr, string $width) : float
	{
		return (new Dimension($image, $dpr))->get($width);
	}

	/**
	 * Get formatted color.
	 * @param string $color The color.
	 * @return string The formatted color.
	 */
	public function getColor(string $color) : string
	{
		return (new Color($color))->formatted();
	}

	/**
	 * Resolve the border method
	 * @param string $method The raw border method.
	 * @return string The resolved border method.
	 */
	public function getMethod(string $method = null) : string
	{
		// Check if null
		if(is_null($method) ||
				// If the length is zero
				strlen($method) == 0 ||
				// Check if key in array exists
				!array_keys(self::$methods, $method, true)){
					$method = array_keys(self::$methods);
					$method = end($method);
				}
		return $method;
	}

	/**
	 * Resolve the device pixel ratio.
	 * @return float The device pixel ratio.
	 */
	public function getDpr() : float
	{
		if (!is_numeric($this->dpr)) {
			return 1.0;
		}

		if ($this->dpr < 0 || $this->dpr > 8) {
			return 1.0;
		}

		return (float) $this->dpr;
	}

	/**
	 * Run the overlay border method.
	 * @param Image $image The source image.
	 * @param float $width The border width
	 * @param string $color The border color
	 * @return Image The manipulated image
	 */
	public function runOverlay(Image $image, float $width, string $color) : Image{
		$half = (int)round($width / 2);
		
		$callback = function($draw) use ($width, $color) {
			$draw->border($width, $color);
		};
		
		return $image->rectangle(
				$half, $half,
				(int)(round($image->width() - $half)),
				(int)(round($image->height() - $half)),
				$callback);
	}

	/**
	 * Run the shrink border method.
	 * @param Image $image The source image.
	 * @param float $width The border width
	 * @param string $color The border color
	 * @return Image The manipulated image
	 */
	public function runShrink(Image $image, float $width, string $color) :Image
	{
		$twice = (int) round($width * 2);
		return $image->resize(
				(int)(round($image->width() - $twice)),
				(int)(round($image->height() - $twice))
				)->resizeCanvas(
					$twice,
					$twice,
					'center',
					true,
					$color);
	}

	/**
	 * Run the expand border method.
	 * @param Image $image The source image
	 * @param float $width The border width
	 * @param string $color The border color
	 * @return Image
	 */
	public function runExpand(Image $image, float $width, string $color) : Image
	{
		$twice = (int)round($width * 2);
		return $image->resizeCanvas(
				$twice,
				$twice,
				'center',
				true,
				$color
				);
	}

	/**
	 * Returns two Border Manipulator properties, border & dpr
	 * {@inheritDoc}
	 */
	public function __toString(): string {
		return parent::getProperties('border','dpr');
	}

}
