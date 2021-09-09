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
namespace Anweshan\Image;

use Anweshan\Exception\InvalidArgumentException;
use Anweshan\Http\Response\Api\ResponseInterface;
use Anweshan\Http\HTTPCode;

use Anweshan\Filesystem\{
    FilesystemInterface, Directory\DirectoryInterface, File\FileNotFoundException, File\FileInterface,
    File\UnreadableFileException, Stream\StreamInterface
};

use Anweshan\Image\{
    Api\ApiInterface, Manipulators\BaseManipulator
};

use Anweshan\System\{
    ServerInterface, SourceInterface
};

/**
 * The class Server is used to direct the filesystem to handle an image.
 *
 * Typically used to **serve** a response to the browser, the class can handle response headers (by acception Http\Response API). The class can be used to render the error pages as well.
 *
 *
 * @package Anweshan\Image
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Server implements SourceInterface, ServerInterface, ImageInterface{

	/**
	 * Source file system.
	 * @var DirectoryInterface
	 */
	protected $source;

	/**
	 * Source path prefix
	 * @var string
	 */
	protected $sourcePathPrefix;

	/**
	 * The base URL
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * Image manipulation API.
	 * @var ApiInterface
	 */
	protected $api;

	 /**
     * Default image manipulations.
     * @var array
     */
    protected $defaults = [];

    /**
     * Preset image manipulations.
     * @var array
     */
    protected $presets = [];

    /**
     * Response code.
     * @var int
     */
    protected $response_code;

	/**
	 * Default constructor to set source and the api.
	 * @param DirectoryInterface $source Source file system.
	 * @param ApiInterface $api Image manipulation API.
	 * @return void
	 */
    public function __construct(DirectoryInterface $source = NULL, ApiInterface $api = NULL, string $sourcePathPrefix = ''){
		$this->setSource($source)->setApi($api);
	}

	/**
	 * {@inheritdoc}
	 * @return Server
	 * @throws InvalidArgumentException If source is not an instanceof DirectoryInterface
	 * @see \Anweshan\System\SourceInterface::setSource()
	 */
	public function setSource($source){
		if($source == NULL || $source instanceof DirectoryInterface){
		    $this->source = $source;
		}else{
		    throw new InvalidArgumentException("Invalid source");
		}

		return $this;
	}

	/**
	 * Get the source file system.
	 * @return DirectoryInterface|null Source file system.
	 * @see \Anweshan\System\SourceInterface::getSource()
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * {@inheritDoc}
	 * @return Server
	 * @see \Anweshan\System\SourceInterface::setSourcePathPrefix()
	 */
	public function setSourcePathPrefix(?string $sourcePathPrefix)
	{
		if($sourcePathPrefix != NULL)
		    $this->sourcePathPrefix = trim($sourcePathPrefix, '/');
		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see \Anweshan\System\SourceInterface::getSourcePathPrefix()
	 */
	public function getSourcePathPrefix(){
		return $this->sourcePathPrefix;
	}



	/**
	 * Get source path.
	 * @param  string                $path Image path.
	 * @return string                The source path.
	 * @throws FileNotFoundException Raised if the path is missing.
	 * {@inheritDoc}
	 * @see \Anweshan\System\SourceInterface::getSourcePath()
	 */
	public function getSourcePath(string $path)
	{
	    $path = trim($path, '/');

		$baseUrl = $this->baseUrl.'/';

		if (substr($path, 0, strlen($baseUrl)) === $baseUrl) {
			$path = trim(substr($path, strlen($baseUrl)), '/');
		}

		if ($path === '') {
			throw new FileNotFoundException('Image path missing.');
		}

		if ($this->sourcePathPrefix) {
			$path = $this->sourcePathPrefix.'/'.$path;
		}

		return rawurldecode($path);
	}

	/**
	 * Check if a source file exists.
	 * @param  string $path Image path.
	 * @return bool   Whether the source file exists.
	 * {@inheritDoc}
	 * @see \Anweshan\System\SourceInterface::sourceFileExists()
	 */
	public function sourceFileExists(?string $path) : bool
	{
		return $path==NULL?false:$this->source->has($this->getSourcePath($path));
	}

	/**
	 * Set base URL.
	 * @param string $baseUrl Base URL.
	 * @return Server
	 */
	public function setBaseUrl(string $baseUrl)
	{
		$this->baseUrl = trim($baseUrl, '/');
		return $this;
	}

	/**
	 * Get base URL.
	 * @return string Base URL.
	 */
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}

	/**
	 * Set image manipulation API.
	 * @param ApiInterface $api Image manipulation API.
	 * @return Server
	 * @see \Anweshan\System\ServerInterface::setApi()
	 */
	public function setApi($api){
	    if($api == null || $api instanceof ApiInterface){
	        $this->api = $api;
	    }else{
	        throw new InvalidArgumentException("Invalid image manipulation api");
	    }
	    return $this;
	}

	/**
	 * Get image manipulation API.
	 * @return ApiInterface Image manipulation API.
	 * @see \Anweshan\System\ServerInterface::getApi()
	 */
	public function getApi(){
		return $this->api;
	}

	/**
     * Set default image manipulations.
     * @param array $defaults Default image manipulations.
     * @return Server
     * @see \Anweshan\System\ServerInterface::setDefaults()
     */
	public function setDefaults(array $defaults = []){
		$this->defaults = $defaults;
		return $this;
	}

	/**
	 * Get default image manipulations.
	 * @return array Default image manipulations.
	 * @see \Anweshan\System\ServerInterface::getDefaults()
	 */
	public function getDefaults(){
		return $this->defaults;
	}

	/**
	 * Set preset image manipulations.
	 * @param array $presets Preset image manipulations.
	 * @return Server
	 * @see \Anweshan\System\ServerInterface::getDefaults()
	 */
	public function setPresets(array $presets){
		$this->presets = $presets;
		return $this;
	}

	/**
	 * Get preset image manipulations.
	 * @return array Preset image manipulations.
	 * @see \Anweshan\System\ServerInterface::getPresets()
	 */
	public function getPresets(){
		return $this->presets;
	}

	/**
	 * Get all image manipulations params, including defaults and presets.
	 * @param  array $params Image manipulation params.
	 * @return array All image manipulation params.
	 * @see \Anweshan\System\ServerInterface::getAllParams()
	 */
	public function getAllParams(array $params)
	{
		$all = $this->defaults;

		if (isset($params['p'])) {
			foreach (explode(',', $params['p']) as $preset) {
				if (isset($this->presets[$preset])) {
					$all = array_merge($all, $this->presets[$preset]);
				}
			}
		}

		return array_merge($all, $params);
	}

	/**
	 * {@inheritdoc}
	 * @throws FileNotFoundException
	 * @throws UnreadableFileException
	 * @throws InvalidArgumentException
	 * @return FilesystemInterface
	 * @see \Anweshan\Image\ImageInterface::makeImage()
	 */
	public function makeImage($path, array $params){

	    if(!is_string($path) && !($path instanceof FileInterface)){
	        throw new InvalidArgumentException("Parameter path is invalid");
	    }

	    $source = $path;
	    $sourcePath = NULL;

	    if(is_string($path)){
    	    $sourcePath = $this->getSourcePath($path);

    	    if(!$this->sourceFileExists($path)){
    	        throw new FileNotFoundException('Could not find the image `'.$sourcePath.'`.');
    	    }

    	    // FileInterface is here.
    	    /**
    	     *
    	     * @var \Anweshan\Filesystem\File\FileInterface $source
    	     */
    	    $source = $this->getSource()->get($sourcePath);
	    }

	    if ($source == NULL || !($source instanceof FileInterface) || !$source->exists() || !$source->isReadable()) {
	        throw new UnreadableFileException('Could not read the image `'.$sourcePath.'`.');
	    }

	    $allowed_mimes = BaseManipulator::$allowed_mimes;
	    $mimey = new \Mimey\MimeTypes;

	    foreach(array_keys($allowed_mimes) as $k){
	        $allowed_mimes[$k] = $mimey->getAllExtensions($k);
	    }
	    $allowed = [];

	    foreach($allowed_mimes as $v){
	        $allowed = array_merge($allowed, $v);
	    }

	    $extension = $source->getExtension();

	    if(!in_array($extension,$allowed, true)){
	        return $source;
	    }

	    /**
	     * @var \Anweshan\Image\Api\ApiInterface $api
	     */
	    $api = $this->api;

	    /**
	     * @var StreamInterface $stream
	     */
	    $stream = NULL;

	    if( $api && $api instanceof ApiInterface ){

	        $stream = $api->run($source, $this->getAllParams($params));

	        if($stream && $stream instanceof StreamInterface && $stream->exists())
	        {
	            $fullpath = NULL;

	            if($path instanceof FileInterface){
	                $fullpath = $path->getPath();
	            }else{
	                $fullpath = $this->getSource()->fullPath($sourcePath);
	            }

	            $stream = $stream->setCanonicalPath($fullpath)
	                             ->setExtension(
	                                 (new \Mimey\MimeTypes())
	                                 ->getExtension($stream->getMime()))
	                             ->toFile(true);
	        }
	    }

	    return $stream;
	}

	/**
	 * Outputs an image to the browser.
	 * @param string|FileInterface $path The image path.
	 * @param array $params The parameters
	 * @param ResponseInterface $api The instanceof {@link \Anweshan\Http\Response\Api\ResponseInterface ApiInterface}.
	 * @throws InvalidArgumentException If the argument(s) are invalid.
	 * @return bool
	 */
	public function outputImage($path, array $params, ResponseInterface $api){
	    if($api == NULL || !($api instanceof ResponseInterface))
	    {
	        throw new InvalidArgumentException("The response ApiInterface is invalid");
	    }

	    $img = $this->makeImage($path, $params);

	    if($img && $api->run($img) == true){
	        if($api->setResponseCode(HTTPCode::HTTP_OK)->output($img) == true){
             return true;
          }
	    }
	    return false;
	}
}
