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
 *
 * iPhone is a registered trademark of Apple Inc.
 */
namespace Anweshan\Image\Manipulators;
use Intervention\Image\Image;

/**
 * The class Size is used to manipulate the size of the image.
 * 
 * By definition, **Size** is a numerical dimension, set by an object when measured. 
 * Here we measure a two-dimensional numerical value, which attributes the sides of the object.
 * 
 * The properties height(h), width(w) are manipulates the width & height of the image. 
 * The property square (sq) takes a single value and assumes it to be both the height and width. 
 * Please remember the width & height properties are overriden when square is found.
 * 
 * The device pixel ratio property (D.P.R) is the ratio between physical pixels and logical 
 * pixels.
 * 
 * For instance, the iPhone<sup>&reg;</sup> 4 and iPhone<sup>&reg;</sup> 4S
 * report a device pixel ratio of 2, as the physical linear resolution is double the logical linear 
 * resolution. 
 * Physical resolution: 960 x 640. 
 * Logical resolution: 480 x 320.
 * 
 * In such a case the image dimensions are increased by double.</p>
 * 
 * The property `fit` attributes to the following:
 * 
 * - contain: *Default*. Resizes the image to fit within the width and height boundaries without cropping, distorting or altering the aspect ratio.
 * 
 * - fill: Resizes the image to fit within the width and height boundaries without cropping or distorting the image, and the remaining space is filled with the background color. The resulting image will match the constraining dimensions.
 * 
 * - max: Resizes the image to fit within the width and height boundaries without cropping, distorting or altering the aspect ratio, and will also not increase the size of the image if it is smaller than the output size.
 * 
 * - stretch: Stretches the image to fit the constraining dimensions exactly. The resulting image will fill the dimensions, and will not maintain the aspect ratio of the input image.
 * 
 * - crop: Resizes the image to fill the width and height boundaries and crops any excess image data. The resulting image will match the width and height constraints without distorting the image. Some cropping methods have been stated:
 * 
 * 	1. It can be cropped to dimesions by just putting `fit=crop`.</li>
 * 	2. It can be cropped by adding a crop position. A crop-postion ``fit=crop-top-left or crop-top or crop-top-right or crop-left or crop-center or crop-right or crop-bottom-left or crop-bottom or crop-bottom-right``. Default is `crop-center` and is the same as `crop`.
 * 	3. It can be cropped by adding a crop focal point. Specifically without using the built-in crop position, one can specify three attributes (compulsory two), seperated by hypens, where the first & second attributes refer to focal points, and the latter one is the zoom. In other words, the format `crop-x-y-z`, where x, y are compulsory, z is optional. Each full step of z is the equivalent of a 100% zoom. Also the suggested range for z is 1 to 10.
 * 
 * @property string $h 	 Sets the height of the image, in pixels.
 * @property string $w 	 Sets the width of the image, in pixels.
 * @property string $fit Sets how the image is fitted to its target dimensions. 
 * 						 Accepts `contain (default), 
 * 						 fill, max, stretch, crop`.
 * @property string $sq  The square property on the image
 * @property string $dpr The device pixel ratio of the image
 *
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Size extends BaseManipulator {

	/**
	 * Maximum image size in pixels or null.
	 * @var integer|null $maxImageSize Maximum image size in pixels or null.
	 * @access protected This can be used an modified under the members of this package.
	 */
	protected $maxImageSize;
	
	/**
	 * The array representing property fit.
	 * The fits are following:
	 * 1. contain (DEFAULT)
	 * 2. fill
	 * 3. max
	 * 4. stretch
	 * 5. crop: Not included see description.
	 * @var string[] FIT_PROPERTY The array representing property fit. 
	 */
	public const FIT_PROPERTY = ['contain', 'fill', 'max', 'stretch'];
	
	/**
	 * The regular expression of crop when fit=crop-*.
	 * @var string FIT_CROP_REGEX The regular expression of crop when fit=crop-*.
	 * @access private The property cannot be accessed or modified by others.
	 */
	private const FIT_CROP_REGEX = '/^(crop)(-top-left|-top|-top-right|-left|-center|-right|-bottom-left|-bottom|-bottom-right|-[\d]{1,3}-[\d]{1,3}(?:-[\d]{1,3}(?:\.\d+)?)?)*$/';
	
	/**
	 * The regular expression of crop.
	 * @var string CROP_REGEX The regular expression of crop.
	 * @access private The property cannot be accessed or modified by others.
	 */
	private const CROP_REGEX = '/^crop-([\d]{1,3})-([\d]{1,3})(?:-([\d]{1,3}(?:\.\d+)?))*$/';
	
	/**
	 * The methods which can be utilized when cropping.
	 * The list of methods is provided as below:
	 * 1. crop-top-left
	 * 2. crop-top
	 * 3. crop-top-right
	 * 4. crop-left
	 * 5. crop-center (default)
	 * 6. crop-right
	 * 7. crop-bottom-left
	 * 8. crop-bottom
	 * 9. crop-bottom-right
	 * 
	 * @var integer[] CROP_METHODS The methods which can be utilized when cropping. 
	 * @access protected Accessed by members in the same package.
	 */
	protected const CROP_METHODS = [
			'crop-top-left' => [0, 0, 1.0],
			'crop-top' => [50, 0, 1.0],
			'crop-top-right' => [100, 0, 1.0],
			'crop-left' => [0, 50, 1.0],
			'crop-center' => [50, 50, 1.0],
			'crop-right' => [100, 50, 1.0],
			'crop-bottom-left' => [0, 100, 1.0],
			'crop-bottom' => [50, 100, 1.0],
			'crop-bottom-right' => [100, 100, 1.0],
	];
	
	/**
	 * Creates size manipulator instance.
	 * @param integer|null $maxImageSize Maximum image size in pixels
	 * @return void
	 */
	public function __construct($maxImageSize = null)
	{
	    parent::__construct();
		$this->maxImageSize = $maxImageSize;
	}

	/**
	 * Perform size image manipulation.
	 * {@inheritdoc}
	 * @see \Anweshan\Image\Manipulators\BaseManipulator::run()
	 */
	public function run(Image $image) : Image
	{
		// Get the width
		$width = $this->getWidth();
		// Get the height
		$height = $this->getHeight();
		// Get the square
		// If it is not a square, you will get width and height as same
		list($width, $height) = $this->getSquare();
		
		// Get the fit property
		$fit = $this->getFit();
		// Get the device pixel ratio
		$dpr = $this->getDpr();
		
		// If we have missing heights, we will resolve the same
		list($width, $height) = $this->resolveMissingDimensions($image, $width, $height);
		// Manipulating based on device pixel ratio
		list($width, $height) = $this->applyDpr($width, $height, $dpr);
		// limiting image size, based on max size
		list($width, $height) = $this->limitImageSize($width, $height);
		// The width is not equal
		if ((int) $width !== (int) $image->width() ||
				// The height is not equal
				(int) $height !== (int) $image->height()) {
			// Run the resize
			$image = $this->runResize($image, $fit, (int) $width, (int) $height);
		}
		
		// Return the image.
		return $image;
	}
	
	/**
	 * Set the maximum image size in pixels.
	 * @param integer|null $maxImageSize The maximum image size in `pixels`. 
	 * 									  Default is `NULL`.
	 * @return void
	 */
	public function setMaxImageSize($maxImageSize = null)
	{
		$this->maxImageSize = $maxImageSize;
	}
	
	/**
	 * Get the maximum image size in pixels.
	 * @return integer|NULL Maximum image size in pixels.
	 */
	public function getMaxImageSize()
	{
		return $this->maxImageSize;
	}
	
	
	/**
	 * Resolve width.
	 * @return integer|null The resolved width.
	 */
	public function getWidth()
	{
		if(!is_numeric($this->w) || $this->w <= 0){
			// No we do not have width
			return;
		}
		
		return intval($this->w);
	}
	
	/**
	 * Resolve height.
	 * @return integer|null The resolved height.
	 */
	public function getHeight()
	{
		if(!is_numeric($this->h) || $this->h <= 0){
			// No we do not have width
			return;
		}
		
		return intval($this->h);
	}
	
	/**
	 * Resolve the square parameter.
	 * *WARNING*: This property overrides/resets/sets both _width and height_.
	 * @return integer[]|array If the property is set expect an array with properties width and height OR default values.
	 */
	public function getSquare()
	{
		
		if(!is_numeric($this->sq) || $this->sq <= 0){
			// No we cannot set height or width
			return array($this->getWidth(), $this->getHeight());
		}
			
		return array((int)$this->sq, (int)$this->sq);
	}
	
	/**
	 * Resolve property fit.
	 * @return string The resolved property fit.
	 * 
	 */
	public function getFit() 
	{
		if(in_array($this->fit, self::FIT_PROPERTY, true)){
			return $this->fit;
		}else if(preg_match(self::FIT_CROP_REGEX, $this->fit)){
			return 'crop';
		}else{
			return 'contain';
		}
		
	}
	
	/**
	 * Resolve the device pixel ratio.
	 * 
	 * The values are between `0 and 8` (both inclusive). `0` disables the property.
	 * @return float The device pixel ratio.
	 */
	public function getDpr() : float
	{
		if (!is_numeric($this->dpr) || ($this->dpr < 0 || $this->dpr > 8)) {
			return 1.0;
		}
		
		return (float) $this->dpr;
	}
	
	/**
	 * The function gets the aspect ratio.
	 * @param int $width The width of an image.
	 * @param int $height The height of an image.
	 * @return float The aspect ratio. 
	 */
	private static function getAspectRatio(int $width, int $height) : float
	{
		return floatval($width/$height);
	}
	
	/**
	 * Resolve missing image dimensions.
	 * 
	 * @param Image $image The image source.
	 * @param integer|null $width The width of the image
	 * @param integer|null $height The height of the image
	 * @return array The resolved width and height.
	 */
	public function resolveMissingDimensions(Image $image, $width = null, $height = null) : array
	{
		
		$ratio = self::getAspectRatio($image->width(), $image->height());
		
		// var_dump($ratio);
		// exit;
		
		if(is_null($width) && is_null($height)){
			$width = $image->width();
			$height = $image->height();
		}
		
		if(is_null($width)){
			$width = $height * $ratio;
		}
		
		if(is_null($height)){
			$height = $width / $ratio;
		}
		
		return [
			(int) $width,
			(int) $height
		];
	}
	
	/**
	 * Apply the device pixel ratio.
	 * @param int $width The target image width
	 * @param int $height The target image height
	 * @param int $dpr The device pixel ratio
	 * @return array The modified width and height
	 */
	public function applyDpr(int $width, int $height, int $dpr): array
	{
		$width = (int)($width * $dpr);
		$height = (int)($height * $dpr);
		
		return array(
				$width,
				$height
		);
	}
	
	/**
	 * Limit image size to maximum allowed image size.
	 * @param  integer   $width  The image width.
	 * @param  integer   $height The image height.
	 * @return int[] The limited width and height.
	 */
	public function limitImageSize(int $width, int $height): array
	{
		if ($this->maxImageSize !== null) {
			$imageSize = $width * $height;
			
			if ($imageSize > $this->maxImageSize) {
				$width = $width / sqrt($imageSize / $this->maxImageSize);
				$height = $height / sqrt($imageSize / $this->maxImageSize);
			}
		}
		
		return [
				(int) $width,
				(int) $height,
		];
	}
	
	/**
	 * Perform resize image manipulation.
	 * @param  Image $image  The source image.
	 * @param  string  $fit    The fit.
	 * @param  integer $width  The width.
	 * @param  integer $height The height.
	 * @return Image The manipulated image.
	 */
	public function runResize(Image $image, string $fit, int $width, int $height) : Image
	{
		$a = [
			'contain'=>'runContainResize',
			'fill'=>'runFillResize',
			'max'=>'runMaxResize',
			'stretch'=>'runStretchResize',
			'crop'=>'runCropResize',
		];
		
		foreach($a as $k=>$v){
			if($fit === $k){
				return $this->$v($image, $width, $height);
			}
		}
		
		return $image;
	}
	
	/**
	 * Perform contain resize image manipulation.
	 * @param  Image   $image  The source image.
	 * @param  integer $width  The width.
	 * @param  integer $height The height.
	 * @return Image   The manipulated image.
	 */
	public function runContainResize(Image $image, $width, $height) : Image
	{
		return $image->resize($width, $height, function ($constraint) {
			$constraint->aspectRatio();
		});
	}
	
	/**
	 * Perform fill resize image manipulation.
	 * @param Image $image The source image.
	 * @param int $width The width.
	 * @param int $height The width.
	 * @return Image The manipulated image
	 */
	public function runFillResize(Image $image, int $width, int $height) : Image
	{
		
		$image = $this->runMaxResize($image, $width, $height);
		
		return $image->resizeCanvas($width, $height, 'center');
	}
	
	
	/**
	 * Perform max resize image manipulation.
	 * @param Image $image  The source image.
	 * @param int   $width  The width.
	 * @param int   $height The height.
	 * @return Image The manipulated image.
	 */
	public function runMaxResize(Image $image, $width, $height) : Image
	{
	    return $image->resize($width, $height, function ($constraint) {
	        $constraint->aspectRatio();
	        $constraint->upsize();
	    });
	}
	
	/**
	 * Perform stretch resize image manipulation
	 * @param Image $image The source image
	 * @param int $width The width
	 * @param int $height The height
	 * @return Image The manipulated image
	 */
	public function runStretchResize(Image $image, int $width, int $height) : Image
	{
		return $image->resize($width, $height);
	}
	
	/**
	 * Perform crop resize image manipulation.
	 * @param  Image   $image  The source image.
	 * @param  integer $width  The width.
	 * @param  integer $height The height.
	 * @return Image   The manipulated image.
	 */
	public function runCropResize(Image $image, int $width, int $height) : Image
	{
		list($resize_width, $resize_height) = $this->resolveCropResizeDimensions($image, $width, $height);
		$zoom = $this->getCrop()[2];
		$image->resize($resize_width * $zoom, $resize_height * $zoom, function ($constraint) {
			$constraint->aspectRatio();
		});
			
		list($offset_x, $offset_y) = $this->resolveCropOffset($image, $width, $height);
			
		return $image->crop($width, $height, $offset_x, $offset_y);
	}
	
	/**
	 * Resolve the crop resize dimensions.
	 * @param  Image   $image  The source image.
	 * @param  integer $width  The width.
	 * @param  integer $height The height.
	 * @return array   The resize dimensions.
	 */
	public function resolveCropResizeDimensions(Image $image, int $width, int $height): array
	{
		$ratio = self::getAspectRatio($image->width(), $image->height());
		
		if ($height > ($width * $ratio)) {
			return [$height * $ratio, $height];
		}
		
		return [$width, $width * $ratio];
	}
	
	/**
	 * Resolve the crop offset.
	 * @param  Image   $image  The source image.
	 * @param  integer $width  The width.
	 * @param  integer $height The height.
	 * @return int[]   The crop offset.
	 */
	public function resolveCropOffset(Image $image, int $width, int $height) : array
	{
		list($offset_percentage_x, $offset_percentage_y) = $this->getCrop();
		
		$offset_x = (int) (($image->width() * $offset_percentage_x / 100) - ($width / 2));
		$offset_y = (int) (($image->height() * $offset_percentage_y / 100) - ($height / 2));
		
		$max_offset_x = $image->width() - $width;
		$max_offset_y = $image->height() - $height;
		
		if ($offset_x < 0) {
			$offset_x = 0;
		}
		
		if ($offset_y < 0) {
			$offset_y = 0;
		}
		
		if ($offset_x > $max_offset_x) {
			$offset_x = $max_offset_x;
		}
		
		if ($offset_y > $max_offset_y) {
			$offset_y = $max_offset_y;
		}
		
		return [$offset_x, $offset_y];
	}
	
	/**
	 * Resolve crop with zoom.
	 * @return int[] The resolved crop.
	 */
	public function getCrop() : array
	{
		
		if (array_key_exists($this->fit, self::CROP_METHODS)) {
			return self::CROP_METHODS[$this->fit];
		}
		
		$matches = array();
		
		if (preg_match(self::CROP_REGEX, $this->fit, $matches)) {
			$matches[3] = isset($matches[3]) ? $matches[3] : 1;
			
			if ($matches[1] > 100 or $matches[2] > 100 or $matches[3] > 100) {
				return self::CROP_METHODS['crop-center'];
			}
			
			return [
					(int) $matches[1],
					(int) $matches[2],
					(float) $matches[3],
			];
		}
		
		return self::CROP_METHODS['crop-center'];
	}
	
	/**
	 * Returns the Size manipulation properties.
	 * {@inheritDoc}
	 */
	public function __toString(): string {
		return parent::setParams('w','h','sq','fit','dpr');
	}

}

