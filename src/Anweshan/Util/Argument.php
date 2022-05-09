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
namespace Anweshan\Util;


/**
 * The class Argument is a class which declares most of the PHP Magic Methods to assign any variable to a class and work with all or each one of them.
 *
 *
 * **Magic Methods**: Magic methods are special methods which override PHP's default's action when certain actions are performed on an object.
 *
 * For more please see {@link https://www.php.net/manual/language.oop5.magic.php PHP Documentation}.
 *
 * @package Anweshan\Util
 *
 * @author Anweshan Roy Chowdhury
 * @since 2021
 * @version 2
 * @license MIT
 */
class Argument
{
    /**
     * Initialize the data memebers.
     * @param array $array The array of properties to initialize.
     */
    public function __construct($array = NULL){
        $this->set($array);
    }

    /**
     * Sets the array of properties.
     * @param array $properties The array of properties.
     */
    public function set(?array $properties){
        if(!is_null($properties) && is_array($properties)){
            foreach($properties as $k=>$v){
                if(is_array($v)){
                   $v = new self($v);
                }
                $this->$k = $v;
            }
        }
    }
 	
    /**
     * PHP Magic method to set a variable.
     * @param string $name The name of the variable
     * @param mixed $value The value to pass to the variable.
     * @link https://www.php.net/manual/language.oop5.overloading.php#object.set __set()
     */
    public function __set(string $name, $value){
        $this->$name = $value;
    }

    /**
     * PHP Magic method to get a variable.
     * @param string $name The name of the variable.
     * @return mixed The value of the variable.
     * @link https://www.php.net/manual/language.oop5.overloading.php#object.set __get()
     */
    public function __get(string $name){
        return isset($this->$name)?$this->$name:NULL;
    }


    /**
     * PHP Magic method which converts an object to a string.
     *
     * Invoked when `echo $this` or `(string)$this` is called.
     *
     * @return string The JSON encoded properties are returned.
     * @link https://www.php.net/manual/language.oop5.magic.php#object.tostring __toString()
     */
    public function __toString() : string {
        return __CLASS__;
    }
	
				/**
					* Converts the object back to an array.
					* @param Argument $arg The instance to convert to an array.
					* @return array|NULL Returns the array of variables in the object, 
					* 																			However there may be none or the object may be inaccessible & return null.
					*	@throws Anweshan\Exception\InvalidArgumentException Raised if the parameter is null.
					*/
				public static function toArray(Argument $arg): ?array {
								if($arg == NULL){
											throw Anweshan\Exception\InvalidArgumentException("Args cannot be empty");
								}

								$tmp= \get_object_vars($arg);

								return $tmp;
					}

}
