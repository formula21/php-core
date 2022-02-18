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

use Anweshan\Util\Argument;

use Anweshan\Exception\{
    InvalidArgumentException, InvalidColorException, NegativeNumberException,
    OutOfBoundsException, RuntimeException, DriverException, BadFunctionCallException
};

use Anweshan\Filesystem\{
    FilesystemException,
    Directory\Directory, Directory\DirectoryNotFoundException,
    File\File, File\FileInterface, File\FileNotFoundException,
    File\UnreadableFileException, File\FileException, File\FileExistsException,
    Stream\Stream 
};

/**
 * The class TextImage is a render to convert alpha-numeric or special characters to an image.
 * 
 * The class finds helps the render to have many properties like:
 * - dimensions
 * - background & forground color
 * - quality
 * - font size
 * - any `ttf` font
 * - angle of rotation
 * 
 * It renders the image in one of the following formats:
 * - jpeg
 * - png
 * - gif
 * - webp 
 * 
 * @package Anweshan\Image
 * @subpackage Manipulators\Helpers
 * 
 * @author Anweshan
 * @since 2022
 * @version 1
 * @license MIT
 */
final class TextImage
{
    /**
     * An array of preferred background colors.
     * @var array
     */
    const PREFERRED_BACKGROUND_HEX = [ "#964848", "#5d4037", "#0288d1", "#7a5c35", "#375073", "#ab5f30", "#338a30", "#384982", ];
    
    /**
     * A white foreground.
     * @var string
     */
    const WHITE_FOREGROUND_HEX = "#ffffff";
    
    /**
     * The image type specifies the output is in JPEG format. This is considered the DEFAULT FORMAT or fallback.
     * @var integer
     */
    const IMAGE_JPEG = 0;
    /**
     * The image type specifies the output is in PNG format.
     * @var integer
     */
    const IMAGE_PNG = 1;
    /**
     * The image type specifies the output is in GIF format.
     * @var integer
     */
    const IMAGE_GIF = 2;
    /**
     * The image type specifies the output is in WEBP format. The format is not supported by all browsers, but is itself the most modern representation.
     * @var integer
     */
    const IMAGE_WEBP = 3;
    
    /**
     * The default angle of rotation (in degrees).
     * @var integer
     */
    const DEFAULT_ANGLE = 8;
    
    /**
     * The maximum size of the image in pixels.
     * @var integer
     */
    const MAX_SIZE = 512;
    
    /**
     * The minimum font-size of the text in points.
     * @var integer
     */
    const MIN_FONT_SIZE = 0;
    
    /**
     * The default resolution percentage.
     * @var integer
     */
    const DEFAULT_RESOLUTION_PERCENTAGE = 100;
    
    /**
     * The default 'gd' driver to be used.
     * @var string
     */
    const DEFAULT_DRIVER = 'gd';
    
    /**
     * JPEG can have many extensions, jpeg, jpg, jpe. We assume jpg is the default.
     * @var string
     */
    const JPEG_DEFAULT_EXTENSION = 'jpg';
    
    private $font_path;
    private $background;
    private $foreground;
    private $text;
    private $width;
    private $height;
    private $font_size;
    private $resolution;
    private $image_type;
    private $angle;
    
    /**
     * The default constructor
     * @param FileInterface|string $font_path The font file to be used while generating the text.
     * @param string $text The alpha-numeric/special characters to transform.
     * @param Color|string $background The background colour to be used.
     * @param Color|string $foreground The forground colour to be used.
     * @param int $width The height of the image. Size must be less than MAX_SIZE.
     * @param int $height The width of the image. Size must be less than MAX_SIZE.
     * @param int $font_size The font size to be used.
     * @param int $image_type The type of the image required. See all the constants.
     * @param int $angle The angle of the image.
     * @param int $resolution The resolution of the image.
     */
    public function __construct($font_path, string $text, int $width = self::MAX_SIZE, int $height = self::MAX_SIZE, int $font_size = self::MIN_FONT_SIZE, int $image_type = self::IMAGE_JPEG, int $angle = self::DEFAULT_ANGLE, int $resolution = self::DEFAULT_RESOLUTION_PERCENTAGE, $background = null, $foreground = null){
        $this->setFontPath($font_path)
        ->setText($text)
        ->setBackground($background)
        ->setForeground($foreground)
        ->setWidth($width)
        ->setHeight($height)
        ->setFontSize($font_size)
        ->setImageType($image_type)
        ->setAngle($angle)
        ->setResolution($resolution);
    }
    
    /**
     * Sets the font path.
     * @param FileInterface|string $font_path The path of the font file.
     * @throws InvalidArgumentException The paramter is empty/null.
     * @throws FileNotFoundException The file is not found.
     * @throws UnreadableFileException The file cannot be read properly.
     * @return TextImage Self referenced for chaining.
     */
    public function setFontPath($font_path) : TextImage {
        if($font_path == NULL){
            throw new InvalidArgumentException("The argument must be a valid font file");
        }
        
        if(!is_string($font_path) && !is_a($font_path, "Anweshan\Filesystem\File\FileInterface")){
            throw new InvalidArgumentException("The font path must be an object or string");
        }else if(is_string($font_path)){
            $font_path = new File($font_path);
        }
        
        if(!$font_path->exists()){
            // This can happen. Say at first when object is initiated we find the path & file.
            // Then the file is deleted due to some other glitch.
            // So a run-time error.
            throw new FileNotFoundException("The file cannot be found");
        }
        
        if(!$font_path->isReadable()){
            throw new UnreadableFileException("The file cannot be read.");
        }
        
        $this->font_path = $font_path;
        return $this;
    }
    
    /**
     * Set the alpha-numeric or special characters;
     * @param string $text The alpha-numeric or special characters;
     * @throws InvalidArgumentException If the text to be used is empty.
     * @return TextImage Self referenced for chaining.
     */
    public function setText(string $text) : TextImage {
        if(empty($text)){
            throw new InvalidArgumentException("The argument must be any valid chracter");
        }
        $this->text = $text;
        return $this;
    }
    
    /**
     * Set the background color.
     * 
     * The parameter can accept any name or hex (with/without #) as a color. 
     * So all names provided in {@link \Anweshan\Image\Manipulators\Helpers\Color} are valid.
     *  
     * @param Color|string $background The background color.
     * @return TextImage Self referenced for chaining.
     * @see \Anweshan\Image\Manipulators\Helpers\Color
     */
    public function setBackground($background = NULL): TextImage {
        if($background == NULL){
            $background = array_rand(array_flip(self::PREFERRED_BACKGROUND_HEX));
        }
        
        if(is_string($background)){
            $background = self::toColor($background, true);
        }
        
        $this->background = $background;
        return $this;
    }
    
    /**
     * Set the forground color.
     * 
     * The parameter can accept any name or hex (with/without #) as a color. 
     * So all names provided in {@link \Anweshan\Image\Manipulators\Helpers\Color} are valid. 
     * 
     * @param Color|string $foreground The foreground color.
     * @return TextImage Self referenced for chaining.
     * @see \Anweshan\Image\Manipulators\Helpers\Color
     */
    public function setForeground($foreground = NULL): TextImage {
        if($foreground == NULL){
            $foreground = self::WHITE_FOREGROUND_HEX;
        }
        
        if(is_string($foreground)){
            $foreground = self::toColor($foreground, true);
        }
        
        $this->foreground = $foreground;
        return $this;
    }
    
    /**
     * Set the color of the image.
     * 
     * The parameters can accept any name or hex (with/without #) as a color. 
     * So all names provided in {@link \Anweshan\Image\Manipulators\Helpers\Color} are valid.
     *  
     * @param Color|string $forground The foreground color.
     * @param Color|string $background The background color.
     * @return TextImage Self referenced for chaining.
     */
    public function setColor($forground = NULL, $background = NULL) : TextImage{
        return $this->setForeground($forground)->setBackground($background);
    }
    
    /**
     * Set the width.
     * @param int $width The width of the image in pixels.
     * @throws NegativeNumberException The width of an image must be positive.
     * @throws OutOfBoundsException The width of the image cannot exceed MAX_SIZE.
     * @return TextImage Self referenced for chaining.
     */
    public function setWidth(int $width) : TextImage {
        if($width < 0){
            throw new NegativeNumberException("The width of the image cannot be negative.");
        }
        
        if($width > self::MAX_SIZE){
            $width = self::MAX_SIZE;
            throw new OutOfBoundsException("The width cannot be more than {$width}.");
        }
        
        if($width == 0){
            $width = NULL;
        }
        
        $this->width = $width ?? self::MAX_SIZE;
        return $this;
    }
    
    /**
     * Set the height.
     * @param int $height The height of the image in pixels.
     * @return TextImage Self referenced for chaining.
     */
    public function setHeight(int $height) : TextImage {
        if($height < 0){
            throw new NegativeNumberException("The height of the image cannot be negative.");
        }
        
        if($height > self::MAX_SIZE){
            $height = self::MAX_SIZE;
            throw new OutOfBoundsException("The height cannot be more than {$height}.");
        }
        
        if($height == 0){
            $height = NULL;
        }
        
        $this->height = $height ?? self::MAX_SIZE;
        return $this;
    }
    
    /**
     * Sets the dimensions of the image.
     * @param int $width The width of the image.
     * @param int $height The height of the image.
     * @return TextImage Self referenced for chaining. 
     */
    public function setSize(int $width, int $height) : TextImage {
        return $this->setWidth($width)->setHeight($height);
    }
    
    /**
     * Sets the square size dimensions of the image.
     * @param int $size The width & height of the image in pixels.
     * @return TextImage Self referenced for chaining.
     */
    public function setSquareSize(int $size) : TextImage {
        return $this->setSize($size, $size);
    }
    
    /**
     * Set the font size of the character.
     * @param int $font_size The font size of the character in points.
     * @throws NegativeNumberException The font size cannot be negative.
     * @return TextImage Self referenced for chaining.
     */
    public function setFontSize(int $font_size): TextImage {
        if($font_size < self::MIN_FONT_SIZE){
            throw new NegativeNumberException("Font Size cannot be negative.");
        }
        
        $this->font_size = $font_size;
        return $this;
        
    }
    
    /**
     * Set the type of image.
     * @param int $image_type The type of image required for rendering.
     * @throws InvalidArgumentException Thrown when the image type is invalid.
     * @return TextImage Self referenced for chaining.
     */
    public function setImageType(int $image_type = self::IMAGE_JPEG){
        switch($image_type){
            case self::IMAGE_JPEG:
            case self::IMAGE_PNG:
            case self::IMAGE_GIF:
            case self::IMAGE_WEBP:
                break;
            default:
                throw new InvalidArgumentException("The image type is invalid.");
        }
        $this->image_type = $image_type;
        return $this;
    }
    
    /**
     * Set the angle of rotation.
     * @param int $angle The angle of rotation.
     * @throws NegativeNumberException Raised if angle of rotation is negative.
     * @throws OutOfBoundsException Raised if the angle of rotation is not between 0 and 359 [both inclusive].
     * @return TextImage Self referenced for chaining.
     */
    public function setAngle(int $angle = self::DEFAULT_ANGLE){
        if($angle < 0){
            throw new NegativeNumberException("The angle of rotation cannot be negative");
        }
        
        if($angle > 359){
            throw new OutOfBoundsException("The angle of rotation must be between 0 and 359 [both inclusive]");
        }
        
        $this->angle = $angle;
        return $this;
    }
    
    /**
     * Set the resolution of the image.
     * @param int $resolution The resolution of image.
     * @throws NegativeNumberException Raised if the resolution is set to be negative.
     * @throws OutOfBoundsException Raised if the resolution is not between 0 and 100 [both inclusive].
     * @return TextImage Self referenced for chaining.
     */
    public function setResolution(int $resolution = self::DEFAULT_RESOLUTION_PERCENTAGE){
        if($resolution < 0){
            throw new NegativeNumberException("The resolution cannot be negative");
        }
        
        if($resolution > self::DEFAULT_RESOLUTION_PERCENTAGE){
            throw new OutOfBoundsException("The resolution must be between 0 and 100 [both inclusive]");
        }
        
        $this->resolution = $resolution;
        return $this;
    }
    
    /**
     * Get the font path.
     * @return string The full path of the font file to be used.
     */
    public function getFontPath() : FileInterface {
        return $this->font_path;
    }
    
    /**
     * Get the characters to be made an image.
     * @return string The characters which may be alpha-numeric or special.
     */
    public function getText() : string{
        return $this->text;
    }
    
    /**
     * The background color to be used.
     * @return Color The color format which may is in hex.
     */
    public function getBackground() : Color{
        return $this->background;
        
    }
    
    /**
     * The forground color to be used.
     * @return Color The color format which may is in hex.
     */
    public function getForeground() : Color{
        return $this->foreground;
    }
    
    /**
     * The width of the image.
     * @return int The width in pixels.
     */
    public function getWidth(): int{
        return $this->width;
    }
    
    /**
     * The height of the image.
     * @return int The height in pixels.
     */
    public function getHeight(): int{
        return $this->height;
    }
    
    /**
     * The font-size of the character.
     * @return int The font-size in pixels.
     */
    public function getFontSize(): int{
        return $this->font_size;
    }
    
    /**
     * Get the angle of rotation;
     * @return int The angle in degrees.
     */
    public function getAngle() : int{
        return $this->angle;
    }
    
    /**
     * Get the resolution.
     * @return int The resolution in percentage.
     */
    public function getResolution() : int {
        return $this->resolution;
    }
    
    /**
     * Get the image type.
     * @return int The type of image.
     */
    public function getImageType() : int {
        return $this->image_type;
    }
    
    /**
     * The color to be used.
     * @param string $color The hex color like `#ffffff` or `white`.
     * @param bool $default If all exceptions are to suppressed in case there is an invalid color.
     * @throws InvalidColorException Raised if the color value is invalid & default parameter is `false`.
     * @return Color The object of the class color.
     * @see \Anweshan\Image\Manipulators\Helpers\Color
     */
    public static function toColor(string $color, bool $default = false) : Color{
        if(empty($color)){
            throw new InvalidColorException("The {$color} is invalid.");
        }
        
        if($color[0] === '#'){
            $color = substr($color, 1);
            return self::toColor($color, $default);
        }
        
        $color_obj = new Color($color);
        
        if($color_obj->isDefault() && !$default){
            throw new InvalidColorException("The {$color} is invalid.");
        }
        
        return $color_obj;
    }
    
    /**
     * Convert color object to RGB.
     * @param Color $color The color object to interpret.
     * @return array An array giving the red, green and blue channel.
     */
    public static function colorToRGBA(Color $color) : array{
        return array('r'=>$color->getRed(), 'g'=>$color->getGreen(), 'b'=>$color->getBlue(), 'a'=>$color->getAlpha());
    }
    
    /**
     * Formats the resolution to the given image type.
     * @param int $image_type The type of image.
     * @param int $resolution The resolution of the image.
     * @throws OutOfBoundsException Raised if the resolution is out of bounds i.e not between 0 and 100 (both inclusive).
     * @throws InvalidArgumentException Raised if the image type is not a JPEG/PNG/GIF/WEBP.
     * @return int|NULL The formatted resolution to the type of image provided.
     */
    public static function formatResolution(int $image_type = self::IMAGE_JPEG, int $resolution = self::DEFAULT_RESOLUTION_PERCENTAGE){
        
        if($resolution < 0 || $resolution > 100){
            throw new OutOfBoundsException("Resolution must be between 0 and 100 [both inclusive]");
        }
        
        switch($image_type){
            case self::IMAGE_JPEG:
            case self::IMAGE_WEBP:
                break;
            case self::IMAGE_PNG:
                $resolution = intval($resolution / 10) - 1;
                if($resolution < 0){
                    $resolution = 0;
                }
                break;
            case self::IMAGE_GIF:
                $resolution = NULL;
                break;
            default:
                throw new InvalidArgumentException("The image type is invalid.");
        }
        
        return $resolution;
    }
    
    /**
     * Get an object of the class `TextImage` without the constructor.
     * @param string|FileInterface $font_path The font path to be used.
     * @param string $text The text to be transformed.
     * @param int $width The width of the image.
     * @param int $height
     * @param int $font_size
     * @param int $image_type
     * @param int $angle
     * @param int $resolution
     * @param string|Color $background
     * @param string|Color $foreground
     * @return TextImage
     */
    public static function textImage($font_path, string $text, int $width = self::MAX_SIZE, int $height = self::MAX_SIZE, int $font_size = self::MIN_FONT_SIZE, int $image_type = self::IMAGE_JPEG, int $angle = self::DEFAULT_ANGLE, int $resolution = self::DEFAULT_RESOLUTION_PERCENTAGE, $background = null, $foreground = null): TextImage{
        $obj = new self($font_path, $text, $width, $height, $font_size, $image_type, $angle, $resolution, $background, $foreground);
        return $obj;
    }
    
    /**
     * Renders the text to an image.
     * @param TextImage $textImage The object of the class TextImage.
     * @param string|null|bool $path The flag denoting if the output is a resource or argument or file.
     * @param string $jpeg_extension The default jpeg file extension to be used as jpeg can be denoted as 'jpeg', 'jpe', 'jpg'.
     * @throws InvalidArgumentException Raised if any argument is invalid.
     * @throws DriverException Raised if the `gd` library is not loaded.
     * @throws BadFunctionCallException Raised if any of the functions presented by `gd` library is called but is not present.
     * @throws DirectoryNotFoundException Raised if the directory of the path presented is not found.
     * @throws FileExistsException Raised if the file to be written to the directory with a particular name & extension is already exists.
     * @throws FileException Raised if there are any recurring problems with any file used in the workspace.
     * @throws RuntimeException Raised if there are any problems with the resource creation.
     * @throws FilesystemException Raised if the type of problem is within the filesystem, but cannot be determined if the filesystem is a directory or stream or file itself.
     * @return resource|\Anweshan\Util\Argument|\Anweshan\Filesystem\File\FileInterface|boolean If parameter `path` is boolean, then the gd resource is returned, else if it is null then an many argument with properties are returned, else if it a valid string path, then the file is returned, else false is returned. 
     */
    public static function text(TextImage $textImage, $path, string $jpeg_extension = self::JPEG_DEFAULT_EXTENSION){
        
        if($textImage == null){
            throw new InvalidArgumentException("The first parameter cannot be null");
        }
        
        $jpeg_extension = empty($jpeg_extension) ? NULL : $jpeg_extension;
        
        $jpeg_extension = $jpeg_extension ?? self::JPEG_DEFAULT_EXTENSION;
        
        if(!extension_loaded(self::DEFAULT_DRIVER)){
            throw new DriverException("The image driver is not loaded!!");
        }
        
        $required_functions = array_unique(['imagecreatetruecolor', 'imagecolorallocatealpha', 'imagefill', 'imagettftext', 'imagesx', 'imagesy', 'imagettfbbox', ]);
        
        foreach($required_functions as $v){
            if(!function_exists($v)){
                throw new BadFunctionCallException("The function {$v} does not exist.");
            }
        }
        
        if(!is_null($path) && !is_bool($path) && (!is_string($path) || strlen($path) == 0)){
            throw new InvalidArgumentException("The path parameter can either be a boolean or null or a non-empty string");
        }
        
        list($jpeg, $png, $gif, $webp, $dir, $name) = array_fill(0, 6, false);
        
        if(is_string($path) && strlen($path) > 0){
            // Path will be seperated with DIRECTORY_SEPARATOR. So we replace it.
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
            
            $path = pathinfo($path);
            
            if(isset($path['dirname']) && isset($path['filename'])){
                if(is_dir($path['dirname'])){
                    $dir = new Directory($path['dirname']);
                }else{
                    throw new DirectoryNotFoundException("The directory in which the file needs to be saved is not found.");
                }
                
                // Storing name of file.
                $name = $path['filename'];
                
                // Filling all possible name combinations
                list($jpeg, $png, $gif, $webp) = array_fill(0, 4, $name);
                
                // Appending all extensions.
                list($jpeg, $png, $gif, $webp) = array($jpeg.".{$jpeg_extension}", $png.".png", $gif.".gif", $webp.".webp");
                list($jpeg, $png, $gif, $webp) = array($dir->has($jpeg), $dir->has($png), $dir->has($gif), $dir->has($webp));
                if($jpeg && $png && $gif && $webp){
                    // Checking if all files exist.
                    throw new FileExistsException("All possible image files exists in the given directory with the name {$path['filename']}");
                }
            }else{
                throw new FileException("The path provided is invalid");
            }
        }
        
        list($font_path, $text) = array($textImage->getFontPath(), $textImage->getText());
        
        if(empty($text)){
            throw new RuntimeException("The text to be rendered cannot be empty");
        }
        
        if(!$font_path->exists() || !$font_path->isReadable()){
            throw new FileException("There was some problem with the font file.");
        }
        
        list($width, $height) = array($textImage->getWidth(), $textImage->getHeight());
        
        if($width == 0){
            $width = intval(self::MAX_SIZE / 2);
        }
        
        if($height == 0){
            $height = intval(self::MAX_SIZE / 2);
        }
        
        list($background, $foreground, $image_type, $font_size, $angle) = array(array_values(self::colorToRGBA($textImage->getBackground())), array_values(self::colorToRGBA($textImage->getForeground())), $textImage->getImageType(), $textImage->getFontSize(), $textImage->getAngle());
        
        $resolution = self::formatResolution($image_type, $textImage->getResolution());
        
        list($img_function, $extension, $img_exists) = array_fill(0, 3, NULL);
        $img_function_args = array_fill(0, 3, NULL);
        
        switch($image_type){
            case self::IMAGE_JPEG:
                $img_function = 'imagejpeg';
                $extension = $jpeg_extension ?? self::JPEG_DEFAULT_EXTENSION;
                $img_exists = $jpeg;
                break;
            case self::IMAGE_PNG:
                $extension = 'png';
                $img_exists = $png;
                break;
            case self::IMAGE_GIF:
                $extension = 'gif';
                $img_exists = $gif;
                unset($img_function_args[2]);
                break;
            case self::IMAGE_WEBP:
                $extension = 'webp';
                $img_exists = $webp;
                break;
        }
        
        // Constructing image function
        $img_function = $img_function ?? "image{$extension}";
        
        if(count($img_function_args) == 3){
            $img_function_args[2] = $resolution;
        }
        
        if(!function_exists($img_function)){
            throw new BadFunctionCallException("Function {$img_function} is not present.");
        }
        
        if($img_exists){
            throw new FileExistsException("The image file cannot be saved with the current name in the current directory");
        }
        
        if($path == NULL && $extension === $jpeg_extension){
            $extension = self::JPEG_DEFAULT_EXTENSION;
        }
        
        $mime = "image/{$extension}"; 
        
        // GD Starts Here.
        
        // Creating an GD Image Resource.
        if( !($im = @imagecreatetruecolor($width, $height)) ){
            throw new RuntimeException("Some error occured during creating image");
        }
        
        // Allocating the background & forground colors to the image pallate.
        list($foreground, $background) = array(@imagecolorallocatealpha($im, ...$foreground), @imagecolorallocatealpha($im, ...$background));
        
        if(!$foreground || !$background){
            throw new RuntimeException("Some error occured during creating image");
        }
        
        // Filling the image with the background color.
        if(!@imagefill($im, 0, 0, $background)){
            throw new RuntimeException("Some error occured during creating image");
        }
        
        // Centering with font file.
        list($xi, $yi) = array(@imagesx($im), @imagesy($im));
        
        // Get the box of 8 points.
        if($font_size == 0){
            $font_size = intval((0.5 * $width + 0.5 * $height)/2);
            
            if($font_size > $height){
                $font_size = (int)(0.99 * $height);
            }
            
            if($font_size > $width){
                $font_size = (int)(0.99 * $width);
            }
            // var_dump($font_size);
            // exit;
        }
        
        $box = @imagettfbbox($font_size, $angle, $font_path->getPath(), $text);
        
        # var_dump($box);
        # exit;
        
        if(!$xi || !$yi || !$box){
            throw new RuntimeException("Some error occured during creating image");
        }
        
        // Array of eight points, of which two corners are now measured.
        list($xr, $yr) = array(abs(max($box[2], $box[4])), abs(max($box[5], $box[7])));
        list($x, $y) = array(intval(($xi - $xr) / 2), intval(($yi + $yr) / 2));
        
        if(!@imagettftext($im, $font_size, 0, $x, $y, $foreground, $font_path->getPath(), $text)){
            throw new RuntimeException("Some error occured during creating image");
        }
        
        if(is_bool($path)){
            return $im;
        }
        
        list($img_function_args[0], $img_function_args[1]) = array($im, NULL);
        
        @ob_start();
        $tmp = @call_user_func_array($img_function, $img_function_args);
        
        if(!$tmp){
            throw new RuntimeException("Some error occured during creating image"); 
        }
        
        $argument = new Argument();
        
        list($argument->contents, $argument->size, $argument->mime, $argument->extension) = array(base64_encode(ob_get_contents()), ob_get_length(), $mime, $extension);
        
        ob_end_clean();
        
        if(is_null($path)){
            return $argument;
        }
        
        // Write the stream.
        $stream = new Stream(base64_decode($argument->contents), $name ?? '', $argument->extension);
        try{
            $file = $dir->write($stream);
            if(!$file->exists() || !$file->isReadable()){
                throw new FilesystemException("Invalid");
            }
            return $file;
        }catch(FilesystemException $e){
            // Suppress all exceptions.
        }
        
        return false;
    }
}
    