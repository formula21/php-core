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
 * The class Crop crops the image, prior to any other resize operations.
 * 
 * By definition, **Cropping** is the removal of unwanted outer areas from a 
 * photographic or illustrated image. The process usually consists of the removal of some of the peripheral areas of an image to remove extraneous trash from the picture, to improve its framing, to change the aspect ratio, or to accentuate or isolate the subject matter from its background.
 * 
 * Cropping is obtained through a dataset of four numeric values. `Format: width,height,x,y`. To do some other crop operation, refer property `fit` of {@link \Anweshan\Image\Manipulators\Size::FIT_PROPERTY} and `crop`.
 * 
 * @property string $crop Crops the image to specific dimensions 
 * 						  prior to any other resize operations. 
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Crop extends BaseManipulator {
	
	/**
	 * Perform crop image manipulation.
	 * {@inheritdoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image{
		$coordinates = $this->getCoordinates($image);
		if ($coordinates) {
			// Limiting the co-ordinates
			$coordinates = $this->limitToImageBoundaries($image, $coordinates);
			// Crop the image based on coordinates
			$image = $image->crop($coordinates[0],$coordinates[1],
								  $coordinates[2],$coordinates[3]);
		}
		
		return $image;
	}
	
	/**
	 * Resolve coordinates.
	 * @param Image $image The source image
	 * @return int[]|null The resolved coordinates
	 */
	public function getCoordinates(Image $image)
	{
		$coordinates = explode(parent::COMMA_DELIMITER, $this->crop);
		
		if (count($coordinates) !== 4 or (!is_numeric($coordinates[0])) or
		(!is_numeric($coordinates[1])) or (!is_numeric($coordinates[2])) or
		(!is_numeric($coordinates[3])) or ($coordinates[0] <= 0) or
		($coordinates[1] <= 0) or ($coordinates[2] < 0) or
		($coordinates[3] < 0) or ($coordinates[2] >= $image->width()) or
		($coordinates[3] >= $image->height())) {
			return;
		}
		
		return [
			(int) $coordinates[0],
			(int) $coordinates[1],
			(int) $coordinates[2],
			(int) $coordinates[3],
		];
	}
	
	/**
	 * Limit coordinates to image boundaries.
	 * @param Image $image The source image
	 * @param array|int[] $coordinates The coordinates
	 * @return array|int[] The limited coordinates
	 */
	public function limitToImageBoundaries(Image $image, array $coordinates) : array 
	{
		
		$diff_width = $image->width() - $coordinates[2];
		$diff_height = $image->height() - $coordinates[3];
		
		if($coordinates[0] > $diff_width){
			// Scale back to hortizontal boundary
			$coordinates[0] = $diff_width;
		}
		
		if($coordinates[1] > $diff_height){
			// Scale back to vertical boundary
			$coordinates[1] = $diff_height;
		}
		
		return $coordinates;
	}
	
	/**
	 * Returns the Crop property.
	 * {@inheritDoc}
	 */
	public function __toString(): string {
		return parent::getProperties('crop');	
	}

}

