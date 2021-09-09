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

/**
 * The class Color supports a variety of color formats.
 * 
 * In addition to the 140 color names, supported by all modern browsers (listed below), the class accepts hexadecimal RGB and RBG alpha formats.
 * 
 * ### Hexadecimal
 * - 3 digit RGB: CCC
 * - 4 digit ARGB (alpha): 5CCC
 * - 6 digit RGB: CCCCCC
 * - 8 digit ARGB (alpha): 55CCCCCC
 * 
 * ### Color names
 * 
 * - aliceblue
 * - antiquewhite
 * - aqua
 * - aquamarine
 * - azure
 * - beige
 * - bisque
 * - black
 * - blanchedalmond
 * - blue
 * - blueviolet
 * - brown
 * - burlywood
 * - cadetblue
 * - chartreuse
 * - chocolate
 * - coral
 * - cornflowerblue
 * - cornsilk
 * - crimson
 * - cyan
 * - darkblue
 * - darkcyan
 * - darkgoldenrod
 * - darkgray
 * - darkgreen
 * - darkkhaki
 * - darkmagenta
 * - darkolivegreen
 * - darkorange
 * - darkorchid
 * - darkred
 * - darksalmon
 * - darkseagreen
 * - darkslateblue
 * - darkslategray
 * - darkturquoise
 * - darkviolet
 * - deeppink
 * - deepskyblue
 * - dimgray
 * - dodgerblue
 * - firebrick
 * - floralwhite
 * - forestgreen
 * - fuchsia
 * - gainsboro
 * - ghostwhite
 * - gold
 * - goldenrod
 * - gray
 * - green
 * - greenyellow
 * - honeydew
 * - hotpink
 * - indianred
 * - indigo
 * - ivory
 * - khaki
 * - lavender
 * - lavenderblush
 * - lawngreen
 * - lemonchiffon
 * - lightblue
 * - lightcoral
 * - lightcyan
 * - lightgoldenrodyellow
 * - lightgray
 * - lightgreen
 * - lightpink
 * - lightsalmon
 * - lightseagreen
 * - lightskyblue
 * - lightslategray
 * - lightsteelblue
 * - lightyellow
 * - lime
 * - limegreen
 * - linen
 * - magenta
 * - maroon
 * - mediumaquamarine
 * - mediumblue
 * - mediumorchid
 * - mediumpurple
 * - mediumseagreen
 * - mediumslateblue
 * - mediumspringgreen
 * - mediumturquoise
 * - mediumvioletred
 * - midnightblue
 * - mintcream
 * - mistyrose
 * - moccasin
 * - navajowhite
 * - navy
 * - oldlace
 * - olive
 * - olivedrab
 * - orange
 * - orangered
 * - orchid
 * - palegoldenrod
 * - palegreen
 * - paleturquoise
 * - palevioletred
 * - papayawhip
 * - peachpuff
 * - peru
 * - pink
 * - plum
 * - powderblue
 * - purple
 * - rebeccapurple
 * - red
 * - rosybrown
 * - royalblue
 * - saddlebrown
 * - salmon
 * - sandybrown
 * - seagreen
 * - seashell
 * - sienna
 * - silver
 * - skyblue
 * - slateblue
 * - slategray
 * - snow
 * - springgreen
 * - steelblue
 * - tan
 * - teal
 * - thistle
 * - tomato
 * - turquoise
 * - violet
 * - wheat
 * - white
 * - whitesmoke
 * - yellow
 * - yellowgreen
 *
 * @package Anweshan\Image
 * @subpackage Manipulators\Helpers
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
class Color {

	/**
	 * 3 digit color code expression.
     * @var string SHORT_RGB 3 digit color code expression.
     */
    public const SHORT_RGB = '/^[0-9a-f]{3}$/i';

    /**
     * 4 digit color code expression.
     * @var string SHORT_AGRB 4 digit color code expression.
     */
    public const SHORT_ARGB = '/^[0-9]{1}[0-9a-f]{3}$/i';

    /**
     * 6 digit color code expression.
     * @var string LONG_RGB 6 digit color code expression.
     */
    public const LONG_RGB = '/^[0-9a-f]{6}$/i';

    /**
     * 8 digit color code expression.
     * @var string LONG_ARGB 8 digit color code expression.
     */
    public const LONG_ARGB = '/^[0-9]{2}[0-9a-f]{6}$/i';
	
    /**
     * The colour name to hex array for all common colors.
     * @var array COLOR The colour name to hex array for all common colors.
     */
    public const COLOR = [
    		'aliceblue' => 'F0F8FF',
    		'antiquewhite' => 'FAEBD7',
    		'aqua' => '00FFFF',
    		'aquamarine' => '7FFFD4',
    		'azure' => 'F0FFFF',
    		'beige' => 'F5F5DC',
    		'bisque' => 'FFE4C4',
    		'black' => '000000',
    		'blanchedalmond' => 'FFEBCD',
    		'blue' => '0000FF',
    		'blueviolet' => '8A2BE2',
    		'brown' => 'A52A2A',
    		'burlywood' => 'DEB887',
    		'cadetblue' => '5F9EA0',
    		'chartreuse' => '7FFF00',
    		'chocolate' => 'D2691E',
    		'coral' => 'FF7F50',
    		'cornflowerblue' => '6495ED',
    		'cornsilk' => 'FFF8DC',
    		'crimson' => 'DC143C',
    		'cyan' => '00FFFF',
    		'darkblue' => '00008B',
    		'darkcyan' => '008B8B',
    		'darkgoldenrod' => 'B8860B',
    		'darkgray' => 'A9A9A9',
    		'darkgreen' => '006400',
    		'darkkhaki' => 'BDB76B',
    		'darkmagenta' => '8B008B',
    		'darkolivegreen' => '556B2F',
    		'darkorange' => 'FF8C00',
    		'darkorchid' => '9932CC',
    		'darkred' => '8B0000',
    		'darksalmon' => 'E9967A',
    		'darkseagreen' => '8FBC8F',
    		'darkslateblue' => '483D8B',
    		'darkslategray' => '2F4F4F',
    		'darkturquoise' => '00CED1',
    		'darkviolet' => '9400D3',
    		'deeppink' => 'FF1493',
    		'deepskyblue' => '00BFFF',
    		'dimgray' => '696969',
    		'dodgerblue' => '1E90FF',
    		'firebrick' => 'B22222',
    		'floralwhite' => 'FFFAF0',
    		'forestgreen' => '228B22',
    		'fuchsia' => 'FF00FF',
    		'gainsboro' => 'DCDCDC',
    		'ghostwhite' => 'F8F8FF',
    		'gold' => 'FFD700',
    		'goldenrod' => 'DAA520',
    		'gray' => '808080',
    		'green' => '008000',
    		'greenyellow' => 'ADFF2F',
    		'honeydew' => 'F0FFF0',
    		'hotpink' => 'FF69B4',
    		'indianred' => 'CD5C5C',
    		'indigo' => '4B0082',
    		'ivory' => 'FFFFF0',
    		'khaki' => 'F0E68C',
    		'lavender' => 'E6E6FA',
    		'lavenderblush' => 'FFF0F5',
    		'lawngreen' => '7CFC00',
    		'lemonchiffon' => 'FFFACD',
    		'lightblue' => 'ADD8E6',
    		'lightcoral' => 'F08080',
    		'lightcyan' => 'E0FFFF',
    		'lightgoldenrodyellow' => 'FAFAD2',
    		'lightgray' => 'D3D3D3',
    		'lightgreen' => '90EE90',
    		'lightpink' => 'FFB6C1',
    		'lightsalmon' => 'FFA07A',
    		'lightseagreen' => '20B2AA',
    		'lightskyblue' => '87CEFA',
    		'lightslategray' => '778899',
    		'lightsteelblue' => 'B0C4DE',
    		'lightyellow' => 'FFFFE0',
    		'lime' => '00FF00',
    		'limegreen' => '32CD32',
    		'linen' => 'FAF0E6',
    		'magenta' => 'FF00FF',
    		'maroon' => '800000',
    		'mediumaquamarine' => '66CDAA',
    		'mediumblue' => '0000CD',
    		'mediumorchid' => 'BA55D3',
    		'mediumpurple' => '9370DB',
    		'mediumseagreen' => '3CB371',
    		'mediumslateblue' => '7B68EE',
    		'mediumspringgreen' => '00FA9A',
    		'mediumturquoise' => '48D1CC',
    		'mediumvioletred' => 'C71585',
    		'midnightblue' => '191970',
    		'mintcream' => 'F5FFFA',
    		'mistyrose' => 'FFE4E1',
    		'moccasin' => 'FFE4B5',
    		'navajowhite' => 'FFDEAD',
    		'navy' => '000080',
    		'oldlace' => 'FDF5E6',
    		'olive' => '808000',
    		'olivedrab' => '6B8E23',
    		'orange' => 'FFA500',
    		'orangered' => 'FF4500',
    		'orchid' => 'DA70D6',
    		'palegoldenrod' => 'EEE8AA',
    		'palegreen' => '98FB98',
    		'paleturquoise' => 'AFEEEE',
    		'palevioletred' => 'DB7093',
    		'papayawhip' => 'FFEFD5',
    		'peachpuff' => 'FFDAB9',
    		'peru' => 'CD853F',
    		'pink' => 'FFC0CB',
    		'plum' => 'DDA0DD',
    		'powderblue' => 'B0E0E6',
    		'purple' => '800080',
    		'rebeccapurple' => '663399',
    		'red' => 'FF0000',
    		'rosybrown' => 'BC8F8F',
    		'royalblue' => '4169E1',
    		'saddlebrown' => '8B4513',
    		'salmon' => 'FA8072',
    		'sandybrown' => 'F4A460',
    		'seagreen' => '2E8B57',
    		'seashell' => 'FFF5EE',
    		'sienna' => 'A0522D',
    		'silver' => 'C0C0C0',
    		'skyblue' => '87CEEB',
    		'slateblue' => '6A5ACD',
    		'slategray' => '708090',
    		'snow' => 'FFFAFA',
    		'springgreen' => '00FF7F',
    		'steelblue' => '4682B4',
    		'tan' => 'D2B48C',
    		'teal' => '008080',
    		'thistle' => 'D8BFD8',
    		'tomato' => 'FF6347',
    		'turquoise' => '40E0D0',
    		'violet' => 'EE82EE',
    		'wheat' => 'F5DEB3',
    		'white' => 'FFFFFF',
    		'whitesmoke' => 'F5F5F5',
    		'yellow' => 'FFFF00',
    		'yellowgreen' => '9ACD32',
    ];
    
    /**
     * The red value.
     * @access The value can be accessed from within the package
     * @var int $red The red value.
     */
    protected $red;

    /**
     * The green value.
     * @access The value can be accessed from within the package
     * @var int $green The green value.
     */
    protected $green;

    /**
     * The blue value.
     * @access The value can be accessed from within the package
     * @var int $blue The blue value.
     */
    protected $blue;

    /**
     * The alpha value.
     * @access The value can be accessed from within the package
     * @var int|double $alpha The alpha value.
     */
    protected $alpha;

    /**
     * Create color helper instance.
     * @param string $value The color value.
     * @return void
     */
	public function __construct($value){
		do {
			if ($hex = $this->getHexFromColorName($value)) {
				$rgba = $this->parseHex($hex);
				$alpha = 1;
				break;
			}
			
			if (preg_match(self::SHORT_RGB, $value)) {
				$rgba = $this->parseHex($value.$value);
				$alpha = 1;
				break;
			}
			
			if (preg_match(self::SHORT_ARGB, $value)) {
				$rgba = $this->parseHex(substr($value, 1).substr($value, 1));
				$alpha = substr($value, 0, 1) / 10;
				break;
			}
			
			if (preg_match(self::LONG_RGB, $value)) {
				$rgba = $this->parseHex($value);
				$alpha = 1;
				break;
			}
			
			if (preg_match(self::LONG_ARGB, $value)) {
				$rgba = $this->parseHex(substr($value, 2));
				$alpha = substr($value, 0, 2) / 100;
				break;
			}
			
			$rgba = [255, 255, 255];
			$alpha = 0;
		} while (false);
		
		list($this->red, $this->blue, $this->green, $this->alpha) = array_merge($rgba, [$alpha]);
	}
	
	/**
	 * Parse hex color to RGB values.
	 * @param  string $hex The hex value.
	 * @return array  The RGB values.
	 */
	public function parseHex($hex)
	{
		return array_map('hexdec', str_split($hex, 2));
	}
	
	/**
	 * Format color for consumption.
	 * @return string The formatted color.
	 * @see \Anweshan\Image\Manipulators\Helpers\Color::__toString()
	 */
	public function formatted() : string
	{
		return (string)$this;
	}
	
	/**
	 * Get hex code by color name.
	 * @param string $name The color name.
	 * @return string|null The hex code.
	 */
	public function getHexFromColorName(string $name)
	{
		$name = strtolower($name);
		
		if (array_key_exists($name, self::COLOR)) {
			return self::COLOR[$name];
		}
	}
	
	/**
	 * The function the rgba format of the color.
	 * @return string The rgba color.
	 */
	public function __toString() : string{
		return 'rgba('.$this->red.', '.$this->green.', '.$this->blue.', '.$this->alpha.')';
	}
}

