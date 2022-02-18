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
namespace Anweshan\Image\Api;

use Imagick;

use Intervention\Image\{
    ImageManager, Image
};
use Anweshan\Exception\{
    InvalidArgumentException, DriverException
};

use Anweshan\Filesystem\{
    File\FileInterface, Stream\Stream, Stream\StreamInterface
};


/**
 * The class API actually helps to apply the image manipulators to the image.
 * 
 * @package Anweshan\Image
 * @subpackage Api
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Api implements ApiInterface{
	/**
	 * An image manager for manipulation
	 * @var ImageManager $imageManager An image manager for manipulation
	 * @access protected Accessible only within the package.
	 */
	protected $imageManager;

	/**
	 * @var \Anweshan\Image\Manipulators\ManipulatorInterface[]|null $manipulators Collection of manipulators.
	 * @access protected Accessible only within the package.
	 */
	protected $manipulators;
	
	/**
	 * Create an runnable instance.
	 * @param ImageManager|string $imageManager The image manager or a driver name for image manager
	 * @param \Anweshan\Image\Manipulators\ManipulatorInterface[] $manipulators Collection of manipulators.
	 * @return void
	 */
	public function __construct($imageManager, array $manipulators)
	{
		$this->setImageManager($imageManager);
		$this->setManipulators($manipulators);
	}

	/**
	 * Set the image manager.
	 * @param ImageManager|string $imageManager The image manager or a driver name for image manager
	 * @return void
	 * @throws InvalidArgumentException If the parameter `imageManager` is null or empty.
	 * @throws DriverException If the parameter `imageManager` is a string,
	 * 						   but the driver name is invalid or not loaded.
	 */
	public function setImageManager($imageManager)
	{
	    if(is_null($imageManager) || empty($imageManager) || (!is_string($imageManager) && !($imageManager instanceof ImageManager))){
			throw new InvalidArgumentException('Not a valid image manager.');
		}
		
		
		if(is_string($imageManager)){
			if(!in_array($imageManager,['gd','imagick'])){
				throw new DriverException('Sorry '.$imageManager.' is not a valid image driver');
			}
			if(!extension_loaded($imageManager)){
				throw new DriverException('Sorry '.$imageManager.' is not loaded or enabled'); 
			}
			$imageManager = new ImageManager([
				'driver' => $imageManager
			]);
		}
		
		if( $imageManager instanceof ImageManager){
			$this->imageManager = $imageManager;
			return;
		}
		
		
		
	}

	/**
	 * Get the image manager.
	 * @return ImageManager|null Intervention image manager.
	 */
	public function getImageManager()
	{
		return $this->imageManager;
	}

	/**
	 * Set the manipulators.
	 * @param \Anweshan\Image\Manipulators\ManipulatorInterface[] $manipulators Collection of manipulators.
	 * @return void
	 * @throws InvalidArgumentException If a manipulator is not `instanceof` {@link \Anweshan\Image\Manipulators\ManipulatorInterface ManipulatorInterface}.
	 */
	public function setManipulators(array $manipulators)
	{
		foreach ($manipulators as $manipulator) {
		    if (!($manipulator instanceof \Anweshan\Image\Manipulators\ManipulatorInterface)) {
				throw new InvalidArgumentException('Not a valid manipulator.');
			}
		}

		$this->manipulators = $manipulators;
	}

	/**
	 * Get the manipulators.
	 * @return \Anweshan\Image\Manipulators\ManipulatorInterface[] Collection of manipulators.
	 */
	public function getManipulators()
	{
		return $this->manipulators;
	}

	/**
	 * Get the encoded image after manipulation of api.
	 * @return StreamInterface The enocoded image
	 * {@inheritdoc}
	 * @throws InvalidArgumentException source is invalid.
	 * @see \Anweshan\Image\Api\ApiInterface::run()
	 * @see Api::isGdResource()
	 * @see Api::isImagick()
	 * @see Api::isInterventionImage()
	 */
	public function run($source, array $params)
	{
	    if($source == NULL || (!self::isGdResource($source) && !self::isImagick($source) && !self::isInterventionImage($source) && !self::isFileInterface($source) && !is_string($source))){
	        throw new InvalidArgumentException("Invalid Image given");
	    }
	    
	    // IF FileInterface 
	    if(self::isFileInterface($source) && $source->exists()){
	        $source = $source->read();
	    }
		
		$image = $this->imageManager->make($source);

		foreach ($this->manipulators as $manipulator) {
			$manipulator->setParams($params);

			$image = $manipulator->run($image);
		}
		
		$stream = new Stream($image->getEncoded());
		return $stream;
	}
	
	
	/**
	 * Determines if current source data is GD resource.
	 *
	 * @param resource|null $source The source data.
	 * 
	 * @return boolean `true` if the image is a GD resource,
	 * 				   `false` otherwise.
	 * @access protected Accessed within the package itself.
	 */
	protected static function isGdResource($source): bool
	{	
		return is_resource($source) && (get_resource_type($source) == 'gd');
	}
	
	/**
	 * Determines if the current source data is an object.
	 * 
	 * The object is checked againt [Imagick object](http://php.net/manual/class.imagick.php).
	 * 
	 * @param object|Imagick|null $source The source data
	 * @return boolean `true` if the image is an Imagick object,
	 * 				   `false` otherwise.
	 * @access protected Accessed within the package itself.
	 */
	protected static function isImagick($source) : bool {
		return is_object($source) && is_a($source, 'Imagick');
	}
	
	/**
	 * Determines if the current source is an object.
	 * 
	 * The object is checked against [Intervention\Image\Image object](http://image.intervention.io).
	 * 
	 * @param object|Image|null $source The source data
	 * @return boolean `true` if the image is an 
	 * 				   [Intervention\Image\Image object](http://image.intervention.io),
	 * 				   `false` otherwise.
	 * @access protected Accessed within the package itself.
	 */
	protected static function isInterventionImage($source): bool
	{
		return is_object($source) && is_a($source, '\Intervention\Image\Image');
	}
	
	/**
	 * Determines if the current source is an instance of FileInterface or StreamInterfacr.
	 * 
	 * The object is checked against \Anweshan\Filesystem\File\FileInterface and \Anweshan\Filesystem\Stream\StreamInterface.
	 * 
	 * @param object|Image|null $source The source data
	 * @return boolean `true` if the image is an instanceof
	 * 				   \Anweshan\Filesystem\File\FileInterface or 			 \Anweshan\Filesystem\Stream\StreamInterface
	 * 				   `false` otherwise.
	 * @access protected Accessed within the package itself.
	 */
	protected static function isFileInterface($source) : bool{
	    return ($source instanceof FileInterface) || ($source instanceof StreamInterface);
	}
}