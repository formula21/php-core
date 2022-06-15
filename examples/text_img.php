<?php
   
require_once realpath(dirname(__DIR__).'/vendor/autoload.php');

use Anweshan\Image\Manipulators\Helpers\TextImage;
   
$path = realpath(dirname(__DIR__).'/lib/fonts/Roboto/Roboto-Regular.ttf');
   
$textImage = TextImage::textImage($path, '007')->setImageType(TextImage::IMAGE_PNG)->setSize(200, 100);

$l = TextImage::text($textImage, null);

// var_dump(

header("Content-Length: {$l->size}");
header("Content-Type: {$l->mime}");
header("Content-Disposition: inline; filename=image.{$l->extension}");
header("X-Property-Font-Size: {$textImage->getFontSize()}");
echo base64_decode($l->contents);