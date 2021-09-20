<?php
require_once dirname(__DIR__).'/vendor/autoload.php';

$dir = realpath('path/to/dir/of/images');
if($dir){
  $source = new Anweshan\Filesystem\Directory\Directory($dir);
  $imageManager = new Intervention\Image\ImageManager([
		'driver' => 'gd', # Default
	]);
  $manipulators = [
		new Anweshan\Image\Manipulators\Orientation(),
		new Anweshan\Image\Manipulators\Crop(),
		new Anweshan\Image\Manipulators\Size(2000*2000),
		new Anweshan\Image\Manipulators\Brightness(),
		new Anweshan\Image\Manipulators\Contrast(),
		new Anweshan\Image\Manipulators\Gamma(),
		new Anweshan\Image\Manipulators\Sharpen(),
		new Anweshan\Image\Manipulators\Filter(),
		new Anweshan\Image\Manipulators\Blur(),
		new Anweshan\Image\Manipulators\Pixelate(),
		new Anweshan\Image\Manipulators\Background(),
		new Anweshan\Image\Manipulators\Border(),
		new Anweshan\Image\Manipulators\Encode(),
	];

  $headers = [
		new Anweshan\Http\Response\Headers\ContentLength(),
		new Anweshan\Http\Response\Headers\Expires(),
		new Anweshan\Http\Response\Headers\ETag(),
		new Anweshan\Http\Response\Headers\ContentType(),
		new Anweshan\Http\Response\Headers\ContentEncoding(),
	];

  // Set API
	$api = new Anweshan\Image\Api\Api($imageManager, $manipulators);

	// Setup Anweshan-Image Server
	$server = (new Anweshan\Image\Server())->setSource($source)->setApi($api)->setBaseUrl('/image/'); # The ->setBaseUrl part is optional

  # This is my way of doing it.
  $url = parse_url($_SERVER['REQUEST_URI']);
	$url['path'] = trim($url["path"],'/');

  $response = new Anweshan\Http\Response\Response();
	$response_api = new Anweshan\Http\Response\Api\Api($headers, $response);

  if($url['path'] === '/' || strlen($url['path']) == 0)
    # The homepage or something
  }else if(strpos($url['path'],'favicon.ico') !== false){
    # Purely my way of doing this.
		$favicon = $source->get('/favicon.ico');
    // Optionally chain setHeaders() here
		$img = $response_api->run($favicon);
    if($img){
      $response_api->setResponseCode(Anweshan\Http\HTTPCode::HTTP_OK)->output($favicon);
    }
	}else{
    try{

			$sourcePath = $server->getSourcePath($url['path']);

			if(!$server->sourceFileExists($url['path'])){
				throw new Anweshan\Filesystem\File\FileNotFoundException('Could not find the image `'.$sourcePath.'`.');
			}

			$source = $server->getSource()->get($sourcePath);

			if(!array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER) || strlen($_SERVER['HTTP_IF_NONE_MATCH']) == 0 || !is_string($_SERVER['HTTP_IF_NONE_MATCH'])){
				gResp:
				$response = new Anweshan\Http\Response\Response();
				$response_api = new Anweshan\Http\Response\Api\Api($headers, $response);
				$img = $server->outputImage($url['path'], $_GET, $response_api);
			}else{
				$x = new Anweshan\Http\Request\Headers\IfNoneMatch();
				$s = $x->decrypt($_SERVER['HTTP_IF_NONE_MATCH']);
				if($s instanceof Anweshan\Util\Argument){
					// If this is done
					$x->setHeaders($s);
				}else{
					goto gResp;
				}
			}

		}catch(Anweshan\Filesystem\File\FileException|Anweshan\Filesystem\Directory\DirectoryException|Anweshan\Filesystem\Stream\StreamException $e){
			http_response_code(404);
      exit;
		}
  }
