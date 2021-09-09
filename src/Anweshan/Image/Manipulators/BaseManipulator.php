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
 * The class BaseManipulator is the base for all manipulators.
 * 
 * All manipulators hence defined will be done so, after inheriting the properties of this class.
 * Hence this class is an abstract class.
 * 
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
abstract class BaseManipulator implements ManipulatorInterface {
	
	/**
	 * The array of valid mimes, accepted by the manipulator API.
	 * @var string[] MIMES The array of valid mimes, accepted by the manipulator API.
	 */
	public static $allowed_mimes = array(
			'image/jpeg'=>NULL,
			'image/webp'=>NULL,
			'image/gif'=>NULL,
			'image/png'=>NULL,
	);
	
	public $mimes;
	
	/**
	 * The comma seperated delimiter.
	 * @var string COMMA_DELIMITER The comma seperated delimiter.
	 */
	public const COMMA_DELIMITER = ',';
	
	/**
	 * The numeric regular expression.
	 * @var string NUMERIC_REGEX The numeric regular expression.
	 */
	protected const NUMERIC_REGEX = '/^-*[0-9]+$/'; 
	
	/**
	 * The manipulation params.
	 * @var string[] $params The manipulation params.
	 * @access protected Can be directly accessed
	 * 					 by inherited classes.
	 */
	protected $params = [];
	
	public function __construct(){
	    $this->mimes = self::$allowed_mimes;
	}
	
	/**
	 * Get a specific manipulation param.
	 * @param mixed $name The manipulation name.
	 * @return string|null The manipulation value. If not found it is null.
	 */
	public function __get($name){
		if(array_key_exists($name, $this->params)){
			return $this->params[$name];
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @return BaseManipulator An instance of the class.
	 * @see \Anweshan\Image\Manipulators\ManipulatorInterface::setParams()
	 */
	public final function setParams(array $params) {
		$this->params = $params;
		return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Anweshan\Image\Manipulators\ManipulatorInterface::getParams()
	 */
	public final function getParams(){
		return $this->params;
	}
	
	/**
	 * This method helps to build a comma seperated list of property values.
	 * 
	 * @param string ...$properties Any number of string properties
	 * @return string A comma-seperated list of properties.
	 */
	protected final static function getProperties(string ...$properties) : string{
		$arr = array();
		foreach($properties as $p){
			if(is_string($p) && strlen($p) > 0 && !in_array($p, $arr, true)){
				$arr[] = $p;
			}
		}
		
		return implode(self::COMMA_DELIMITER, $arr);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Anweshan\Image\Manipulators\ManipulatorInterface::run()
	 */
	abstract public function run(Image $image) : Image;
	
	/**
	 * {@inheritDoc}
	 * @see \Anweshan\Image\Manipulators\ManipulatorInterface::__toString()
	 */
	abstract public function __toString() : string;
	
	
}

