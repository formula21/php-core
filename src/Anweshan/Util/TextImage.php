<?php

namespace Anweshan\Util;


use Anweshan\Exception\BadFunctionCallException;
use Anweshan\Exception\RuntimeException;
use Anweshan\Exception\OutOfRangeException;
use Anweshan\Exception\InvalidArgumentException;
use Anweshan\Util\Argument;

class TextImage{

    const BACKGROUND_HEX = [ "#964848", "#5d4037", "#0288d1", "#7a5c35", "#375073", "#ab5f30", "#338a30", "#384982", ];
    const FOREGROUND_HEX = "#ffffff";

    const IMAGE_JPEG = 0;
    const IMAGE_PNG = 1;
    const IMAGE_GIF = 2;
    const IMAGE_WEBP = 3;

    const DEFAULT_ANGLE = 8;

    const MAX_SIZE = 512;

    private $font;
    private $letter;
    private $background;
    private $foreground;
    private $width;
    private $height;
    private $font_size;

    public function __construct(string $font, string $letter, string $background = '', string $foreground = '', int $width = 0, int $height = 0, int $font_size = 0){
          $this->setFont($font)
               ->setLetter($letter)
               ->setBackground($background)
               ->setForeground($foreground)
               ->setWidth($width)
               ->setHeight($height)
               ->setFontSize($font_size);
    }

    public function setFont(string $font){
        if( !file_exists($font) || !is_file($font) || !($this->font = realpath($font)) ){
            $this->font = NULL;
            throw new FileException("The file is not found.");
        }

        return $this;
    }

    public function setLetter(string $letter){
        if(strlen($letter) == 0){
           throw new InvalidArgumentException("Invalid argument letter");
        }
        $this->letter = $letter[0];
        return $this;
    }

    public function setBackground(string $background){
        if(!empty($background)){
          $tmp = self::hexToRGB($background);
          if($tmp !== false){
             $this->background = $tmp;
          }else{
             throw new InvalidColorException("The color {$background} is not valid.");
          }
        }
        return $this;
    }

    public function setForeground(string $foreground){
        if(!empty($foreground)){
          $tmp = self::hexToRGB($foreground);
          if($tmp !== false){
             $this->foreground = $tmp;
          }else{
             throw new InvalidColorException("The color {$foreground} is not valid.");
          }
        }
        return $this;
    }

    public function setWidth(int $width){
        if($width < 0){
           throw new NegativeNumberException("Width cannot be a negative integer.");
        }

        if($width == 0){
           $width = NULL;
        }

        $this->width = $width;
        return $this;
    }

    public function setHeight(int $height){
        if($height < 0){
           throw new NegativeNumberException("Height cannot be a negative integer.");
        }

        if($height == 0){
           $height = NULL;
        }

        $this->height = $height;
        return $this;
    }

    public function setFontSize(int $font_size){
      if($font_size < 0){
          throw new NegativeNumberException("Font Size cannot be a negative integer.");
      }

      if($font_size == 0){
        if($this->width && $this->height && $this->width > 0 && $this->height > 0){
           $font_size = intval($this->width + $this->height)/4;
        }
      }

      $this->font_size = $font_size;
      return $this;
    }

    /* GETTERS */
    public function getFont(){
        return $this->font;
    }

    public function getLetter(){
        return $this->letter;
    }

    public function getBackground(){
        return $this->background;
    }

    public function getForeground(){
        return $this->foreground;
    }

    public function getWidth(){
        return $this->width;
    }

    public function getHeight(){
        return $this->height;
    }

    public function getFontSize(){
        return $this->font_size;
    }

    public static function hexToRGB(string $color){
        $x = false;
        if($color[0] === '#'){
           $color = substr($color, 1);
        }

        if(is_string($color) && strlen($color) == 6){
            $x = true;
            $color = str_split($color, 2);
            list($r, $g, $b) = $color;
        }

        if(is_string($color) && strlen($color) == 3){
            $x = true;
            $color = str_split($color, 1);
            list($r, $g, $b) = $color;
        }

        if(!$x){
           return false;
        }

        $r = hexdec( $r );
			  $g = hexdec( $g );
			  $b = hexdec( $b );

        return ['r'=>$r, 'g'=>$g, 'b'=>$b];

    }

    public static function imageTTFCenter($image, $text, $font, $size, $angle = 8){
      $function_list = ['imagesx', 'imagesy', 'imagettfbbox', ];
      foreach($function_list as $v){
         if(!function_exists($v)){
            throw new BadFunctionCallException("Function {$v} does not exists.");
         }
      }
      list($xi, $yi) = array(imagesx($image), imagesy($image));
      $box = imagettfbbox($size, $angle, $font, $text);
      list($xr, $yr) = array(abs(max($box[2], $box[4])), abs(max($box[5], $box[7])));
      list($x, $y) = array(intval(($xi - $xr) / 2), intval(($yi + $yr) / 2));
      return array($x, $y);
   }

   private static function useTextImage(TextImage $image){

      if($image && $image->getFont() && file_exists($image->getFont()) && is_file($image->getFont()) && $image->getLetter() && strlen($image->getLetter()) > 0){
         $image->setLetter($image->getLetter());
         $image->setFont(realpath($image->getFont()));

         if(!$image->getBackground()){
            $backgrounds = self::BACKGROUND_HEX;
            $background = array_rand(array_flip($backgrounds));
            $image->setBackground($background);
         }

         if(!$image->getForeground()){
            $image->setForeground(self::FOREGROUND_HEX);
         }

         if(!$image->getWidth() && !$image->getHeight()){
            $image->setWidth(512)->setHeight(512);
         }

         if(!$image->getHeight()){
            $image->setHeight($image->getWidth());
         }

         if(!$image->getWidth()){
            $image->setWidth($image->getHeight());
         }

         if($image->getFontSize() == 0){
            $image->setFontSize(0);
         }

         return $image;
      }
      return false;
   }

   /**
    * Returns the text on background, as a file resource or browser resource (as Arguments)
    * @param TextImage $obj The instance, which helps to generate the said image.
    * @param int $format The format of the image. (Default: {@link #IMAGE_JPEG})
    * @param int $resolution_percentage The resolution of the image in percentage
    *            0 is least (worst) and 100 is most (best). (Default: 100).
    *            By default imagegif does not accept any resolution param.
    *
    * @param string|NULL $path The path where the resource is to be bundled.
    *                          If NULL is provided, the resource would not expect a file.
    *
    * @param bool $resource    Flag denotes if the resource is returned.
    *
    * @param int $angle       The angle of rotation.
    *
    * @return bool|Argument   If there is an error while executing the function, return
    *                         false. If a file path was provided, return true or imageresource.
    *                         If NULL was provided return instanceof Argument containing
    *                         (contents, size, mime) or imageresource.
    *
    * @throws InvalidArgumentException If the instanceof TextImage was not properly initialized or
    *                                  configured or the format to be obtained is invalid.
    *
    * @throws RuntimeException         If the driver was not loaded.
    * @throws BadFunctionCallException If the functions relative to gd driver were not found.
    * @throws OutOfRangeException      If the resolution_percentage is not in range.
    * @throws FileException            If a file at the path provided exists.
    *
    */
   public static function text(TextImage $obj, int $format = self::IMAGE_JPEG, int $resolution_percentage = 100, string $path = NULL, bool $resource = false, int $angle = self::DEFAULT_ANGLE){

      // Checking the object.
      if(!($obj = self::useTextImage($obj))){
          throw new InvalidArgumentException("The argument was not valid");
      }

      // Checking if the gd library exists.
      if(!extension_loaded('gd')){
         throw new RuntimeException("Extension gd is not loaded. Check php.ini settings and php documentation.");
      }

      // Initializing the variables.
      list($extension, $mime, $resolution, $function) = array_fill(0, 4, NULL);
      list($args, $argument) = [array(NULL, NULL), new Argument()];

      // Checking for the resolution_percentage bounds.
      if($resolution_percentage < 0 || $resolution_percentage > 100){
         throw new OutOfRangeException("Resolution % = {$resolution_percentage} should be in [0, 100]");
      }

      // Checking if format is valid, and initializing all interlaced parameters with the same.
      switch($format){
         case self::IMAGE_JPEG:
            list($extension, $resolution, $function) = array($extension ?? "jpeg", $resolution ?? $resolution_percentage, $function ?? "imagejpeg");
         case self::IMAGE_WEBP:
            list($extension, $resolution, $function) = array($extension ?? "webp", $resolution ?? $resolution_percentage, $function ?? "imagewebp");
         case self::IMAGE_PNG:
            list($extension, $resolution, $function) = array($extension ?? "png", $resolution ?? ($resolution_percentage / 100), $function ?? "imagepng");
            // Adding a third resolution paramater.
            $args[] = $resolution;
            break;
         case self::IMAGE_GIF:
            list($extension, $resolution, $function) = array($extension ?? "gif", $resolution ?? ($resolution_percentage / 100), $function ?? "imagegif");
            // The resoultion parameter is ignored.
            $resolution = NULL;
            break;
         default:
          throw new InvalidArgumentException("Format is invalid");
      }

      // Mime is deducted.
      $mime = $mime ?? "image/{$extension}";

      // Checking if all usuable functions exist or not.
      $required_functions = array_unique(['imagecreatetruecolor', 'imagecolorallocate', 'imagefill', 'imagettftext', 'imagesx', 'imagesy', 'imagettfbbox', $function]);

      foreach($required_functions as $v){
        if(!function_exists($v)){
           throw new BadFunctionCallException("Function {$v} does not exist");
        }
      }

      // Checking and using path.
      if($path != NULL){
        if(is_file($path)){
           throw new FileException("Path {$path} exists!");
        }
         if(is_dir($path)){
            $file = uniqid("TMP_").'.'.$extension;
            $path = rtrim($path, DIRECTORY_SEPARATOR);
            $path = implode(DIRECTORY_SEPARATOR, [$path, $file]);
         }

      }

      // Creating an GD Image Resource.
      if( !($im = @imagecreatetruecolor($obj->getWidth(), $obj->getHeight())) ){
         throw new RuntimeException("Some error occured during creating image");
      }

      // Unpacking foreground and background, and assigning them with no special keys.
      list($foreground, $background) = array(array_values($obj->getForeground()), array_values($obj->getBackground()));



      // Allocating the forground and background color.
      list($foreground, $background) = array(imagecolorallocate($im, ...$foreground), imagecolorallocate($im, ...$background));

      // Check for null
      if(is_null($foreground) || is_null($background)){
          throw new RuntimeException("Some error occured during creating image");
      }

      // Filling the image with the background color.
      if(!@imagefill($im, 0, 0, $background)){
         throw new RuntimeException("Some error occured during creating image");
      }

      // Centering with font file.

      list($xi, $yi) = array(@imagesx($im), imagesy(@$im));

      // Get the box of 8 points.
      $box = @imagettfbbox($obj->getFontSize(), $angle, $obj->getFont(), $obj->getLetter());

      if(!$xi || !$yi || !$box){
          throw new RuntimeException("Some error occured during creating image");
      }

      // Array of eight points, of which two corners are now measured.
      list($xr, $yr) = array(abs(max($box[2], $box[4])), abs(max($box[5], $box[7])));
      list($x, $y) = array(intval(($xi - $xr) / 2), intval(($yi + $yr) / 2));

      // Writing the text
      if(!@imagettftext($im, $obj->getFontSize(), 0, $x, $y, $foreground, $obj->getFont(), $obj->getLetter())){
         throw new RuntimeException("Some error occured during creating image");
      }

      if($resource){
         return $im;
      }

      // Making formal callable arguments.
      // For imagegif the third parameter is pre-omitted.
      list($args[0], $args[1]) = array($im, $path);

      if(is_null($path)){
         ob_start();
      }

      $tmp = @call_user_func_array($function, $args);

      if(is_null($path)){
         list($argument->contents, $argument->size, $argument->mime, $argument->extension) = array(base64_encode(ob_get_contents()), ob_get_length(), $mime, $extension);
         ob_end_clean();
      }

      if(!$tmp || !is_null($path)){
         $argument = $tmp;
      }

      return $argument;

   }

}
