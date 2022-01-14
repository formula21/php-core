<?php
realpath(__DIR__.'/vendor/autoload.php') ? require_once realpath(__DIR__.'/vendor/autoload.php') : die("Unkown Error");
require_once realpath(__DIR__.'/include/include.php');

if(!defined("AUDIO_MIX_NORMALIZATION")){
   define("AUDIO_MIX_NORMALIZATION", 0.8);
}

if(!defined("CAPTCHA_NOISE")){
   define("CAPTCHA_NOISE", true);
}

use Anweshan\Util\Url;
use Anweshan\Util\Argument;
use Library\WavFile;

header("Cache-Control: no-cache, no-store, max-age=0, must-revalidate;");

# You can comment out this part
# Start Comment
if(!Url::isPost() || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') != 0){
   header("X-Error-Reason: Method Not Allowed");
   header("X-Error-Resolution: Not all methods are allowed.");
   http_response_code(405);
   die(include_once __DIR__.'/error/error.php');
}

# @preload_ini();
@session_start();

$first_try = false;

if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) == 0 || strcasecmp($_SERVER['HTTP_AUTHORIZATION'], 'Bearer '.$_SESSION['xhrKey']) != 0){
   $realm = "Access forbidden with invalid access token";
   if(in_array($_SESSION['xhrKey'] ?? '', $_SESSION['expiredXhrKey']) == true){
      $realm = 'Access forbidden with expired access token';
   }
   header("X-Error-Reason: Bad Gateway");
   header("X-Error-Resolution: {$realm}. Refresh the page.");
   header("WWW-Authenticate: Bearer realm=\"{$realm}\", charset=\"UTF-8\"");
   http_response_code(401);
   die(include_once __DIR__.'/error/error.php');
}else{

   $h = hash('sha1', __FILE__);

   $first_try = isset($_SESSION['CAPTCHA_FIRST'])?$_SESSION['CAPTCHA_FIRST']:false;

   if($first_try){
      unset($_SESSION['CAPTCHA_FIRST']);
   }

   if(!isset($_SESSION[$h])){
      $first_try = true;
      $_SESSION[$h] = serialize(new Argument(['time'=>time()+(2*60), 'limit'=>12]));
   }

   $args = $_SESSION[$h] ? unserialize($_SESSION[$h]) : new Argument(['time'=>time()+(2*60), 'limit'=>20]);
   if($args->time > time()){
      $args->limit--;
      $_SESSION[$h] = serialize($args);
   }else{
      // We just expired everything.
      $args = new Argument(['time'=>time()+(2*60), 'limit'=>11]);
      $_SESSION[$h] = serialize($args);
   }

   $_SESSION['expiredXhrKey'][] = $_SESSION['xhrKey'];
   $_SESSION['xhrKey'] = getXhrKey();
   header("X-API-Key: {$_SESSION['xhrKey']}");

   if(!$args || $args->limit <= 0){
     // Too many requests.
     header("X-Error-Reason: Too Many Requests");
     header("X-Error-Resolution: Please wait till ".date("d-m-Y h:i A", ($args->time)+(60)).' and try again.');
     http_response_code(429);
     die(include_once __DIR__.'/error/429.php');
   }
}

# End Comment.

$width = $_GET['w'] ?? $_SESSION['CAPTCHA_WIDTH'] ?? 200;
$height = $_GET['h'] ?? $_SESSION['CAPTCHA_HEIGHT'] ?? 50;
$characters = $_SESSION['CAPTCHA_CHARS'] ?? 6;

$str = [range(0,9), range('A','Z')];

# Comment this part

$captcha_properties = $_SESSION['CAPTCHA_PROP'] ?? array(
  'arr' => $str,
  'hash'=> hash('sha1',implode('',array_merge(...$str))),
);

if(!is_array($captcha_properties) || count($captcha_properties) < 2 || !array_key_exists('arr', $captcha_properties) || !array_key_exists('hash', $captcha_properties) || strcmp($captcha_properties['hash'], hash('sha1', implode('',array_merge(...$captcha_properties['arr'])))) != 0){
  $_SESSION['expiredXhrKey'][] = $_SESSION['xhrKey'];
  $_SESSION['xhrKey'] = getXhrKey();
  header("X-API-Key: {$_SESSION['xhrKey']}");
  header("X-Error-Reason: Not Acceptable");
  header("X-Error-Resolution: Please refresh page and try again.");
  http_response_code(406);
  die(include_once __DIR__.'/error/error.php');
}else{
   $str = $captcha_properties['arr'];
}

# End of Commenting

if($width && $height && $characters && $characters){
   // So here we will have the attributes.
  $bits = intval($characters / count($str)); // 12 / 3 = 4 6/3 = 2
  $code = '';
  foreach($str as &$v){
    shuffle($v);
    $v = array_flip($v);
    $code .= implode('',array_rand($v, $bits));
  }
  $code = str_shuffle($code);

  // var_dump($code);

  $image = imagecreatetruecolor($width, $height);
  imageantialias($image, true);

  $colors = [];

  $red = rand(125, 175);
  $green = rand(125, 175);
  $blue = rand(125, 175);

  for($i = 0; $i < strlen($code); $i++) {
    $colors[] = imagecolorallocate($image, $red - 20*$i, $green - 20*$i, $blue - 20*$i);
  }

  imagefill($image, 0, 0, $colors[0]);

  for($i = 0; $i < 10; $i++) {
    imagesetthickness($image, rand(2, 10));
    $rect_color = $colors[rand(1, count($colors)-1)];
    imagerectangle($image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $rect_color);
  }

  $black = imagecolorallocate($image, 0, 0, 0);
  $white = imagecolorallocate($image, 255, 255, 255);
  $textcolors = [$black, $white];

  $font_path = realpath(__DIR__.'/external/fonts/Captcha');
  $fonts = scandir($font_path);
  // var_dump($fonts);
  foreach($fonts as $k=>&$f){
    if(is_dir($f) || stripos($f, '.ttf') === false || ($f = realpath($font_path.'/'.$f)) == false){
       unset($fonts[$k]);
    }
  }
  $fonts = array_values($fonts);
  $string_length = strlen($code);
  $code = str_shuffle($code);
  for($i = 0; $i < $string_length; $i++) {
    $letter_space = 170/$string_length;
    $initial = 15;
    imagettftext($image, 24, rand(-15, 15), $initial + $i*$letter_space, rand(25, 45), $textcolors[rand(0, 1)], $fonts[array_rand($fonts)], $code[$i]);
  }

  $content_type = 'image/png';
  $image_function = 'imagepng';
  $error = false;

  ob_start();
  if(function_exists('imagewebp')){
     $content_type = 'image/webp';
     $image_function = 'imagewebp';
  }

  if(!@$image_function($image)){
     $error = true;
  }
  if(!$error){
    $content_length = ob_get_length();
  }
  $contents = ob_get_contents();
  ob_end_clean();
  @imagedestroy($image);

  if($error){
     $_SESSION['expiredXhrKey'][] = $_SESSION['xhrKey'];
     $_SESSION['xhrKey'] = getXhrKey();
     header("X-API-Key: {$_SESSION['xhrKey']}");
     header("X-Error-Reason: Internal Server Error");
     header("X-Error-Resolution: There was an error. Please try again.");
     http_response_code(500);
     die(include_once __DIR__.'/error/500.php');
  }

  $json = [['image'=>base64_encode($contents), 'type'=> $content_type, 'length'=>$content_length]];

  $audio = [];
  $audio_dir = realpath(__DIR__.'/external/audio/en');
  $noise_dir = dirname($audio_dir).'/noise';
  $wavCaptcha = new WavFile();
  $first = true;

  try{
    for($i = 0; $i<strlen($code); $i++){
        $l = new WavFile(realpath($audio_dir.'/'.strtoupper($code[$i]).'.wav'));
        if($first){
          $wavCaptcha->setSampleRate($l->getSampleRate())->setBitsPerSample($l->getBitsPerSample())->setNumChannels($l->getNumChannels());
          $first = false;
        }
        $wavCaptcha->appendWav($l);
    }
    // throw new Exception("No AUDIO");
  }catch(Exception $e){
      $l = new WavFile(realpath($audio_dir.'/error.wav'));
      $wavCaptcha = new WavFile();
      $wavCaptcha->setSampleRate($l->getSampleRate())->setBitsPerSample($l->getBitsPerSample())->setNumChannels($l->getNumChannels());
      $wavCaptcha->appendWav($l);
      $first = NULL;
  }

  if(!is_null($first) && defined('CAPTCHA_NOISE') && CAPTCHA_NOISE == true){
     // No error, lets scan the noise dir;
     $noiseFile = scandir($noise_dir);
     $noiseFile = array_splice($noiseFile, 2);

     $filters = array();

     $wavNoise   = false;
     $randOffset = 0;

     $noiseFile = $noiseFile[array_rand($noiseFile)];
     $noiseFile = $noise_dir.'/'.$noiseFile;
     if(realpath($noiseFile)){
        $wavNoise = new WavFile($noiseFile, false);
     }

     $randOffset = 0;
     if ($wavNoise->getNumBlocks() > 2 * $wavCaptcha->getNumBlocks()) {
       $randBlock = mt_rand(0, $wavNoise->getNumBlocks() - $wavCaptcha->getNumBlocks());
       $wavNoise->readWavData($randBlock * $wavNoise->getBlockAlign(), $wavCaptcha->getNumBlocks() * $wavNoise->getBlockAlign());
     } else {
       $wavNoise->readWavData();
       $randOffset = mt_rand(0, $wavNoise->getNumBlocks() - 1);
     }

     if ($wavNoise !== false) {
        $mixOpts = array('wav'  => $wavNoise,
                         'loop' => true,
                         'blockOffset' => $randOffset);

        $filters[WavFile::FILTER_MIX]       = $mixOpts;
        $filters[WavFile::FILTER_NORMALIZE] = defined(AUDIO_MIX_NORMALIZATION)?AUDIO_MIX_NORMALIZATION:0.8;
    }
    if(!empty($filters)){
      $wavCaptcha->filter($filters);
    }
  }

  if($wavCaptcha){
     ob_start();
     echo base64_encode($wavCaptcha->__toString());
     $contents = ob_get_contents();
     $length = ob_get_length();
     $content_type = 'audio/wav';
     ob_end_clean();
     $json[] = ['audio'=> $contents, 'type'=>$content_type ,'length'=>$length];
  }
  $_SESSION['expiredXhrKey'][] = $_SESSION['xhrKey'];
  $_SESSION['xhrKey'] = getXhrKey();
  $_SESSION['captcha'] = hash("sha256", $code);
  $_SESSION['captcha_unc'] = $code;

  $contents = NULL;
  $uniq = uniqid();
  ob_start();
  echo json_encode($json, JSON_PRETTY_PRINT);
  $contents = ob_get_contents();
  $content_length = ob_get_length();
  ob_end_clean();

  $content_type = 'application/json';

  $headers = ["Content-Type: {$content_type}", "Content-Disposition"=>"Content-Disposition: attachment, filename=\"captcha-{$uniq}\"", "Content-Length: {$content_length}","X-API-Key: {$_SESSION['xhrKey']}"];

  if(!$first_try){
     sleep(5);
  }

  if(!Url::isSSL()){
     unset($headers['Content-Disposition']);
  }

  // var_dump($first_try);

  foreach($headers as $k=>$v){
     header($v, true);
  }
  http_response_code(200);
  echo $contents;

}else{
  $_SESSION['expiredXhrKey'][] = $_SESSION['xhrKey'];
  $_SESSION['xhrKey'] = getXhrKey();
  header("X-API-Key: {$_SESSION['xhrKey']}");
  header("X-Error-Reason: Internal Server Error");
  header("X-Error-Resolution: Reload the page and try again");
  http_response_code(500);
  die(include_once __DIR__.'/error/500.php');
}
