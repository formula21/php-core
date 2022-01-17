<?php
   
require_once realpath(dirname(__DIR__).'/vendor/autoload.php');

use Anweshan\Image\Manipulators\Helpers\TextImage;
   
$path = realpath(dirname(__DIR__).'/lib/fonts/Roboto/Roboto-Regular.ttf');
   
$textImage = TextImage::textImage($path, 'A')->setBackground('orange')->setImageType(TextImage::IMAGE_PNG)->setSize(256);

$l = TextImage::text($textImage, null);

header("Content-Length: {$l->size}");
header("Content-Type: {$l->mime}");
header("Content-Disposition: inline; filename=image.{$l->extension}");

echo base64_decode($l->contents);