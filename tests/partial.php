<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

function partial_content(Anweshan\Filesystem\File\FileInterface $file, int chunk_size = 16 ,int $min_threshold = 3, int $expiry_time = 1000){
	
	$mime = $file->getMime();
	if(stripos($mime, 'audio') !== false || stripos($mime, 'video') !== false){

	  $size = $file->getSize();
	  $range = '';
	  $expires = Http::getGMT(time()-$expiry_time);
	  // We have to go for partial content...
	  if(isset($_SERVER['HTTP_RANGE'])){
		@list($size_unit, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);

		@list($seek_start, $seek_end) = explode('-', $range);

		if(empty($size_unit) || empty($range)){
		   // Something has gone absolutely wrong.
		   header("Content-Type: text/html; charset=UTF-8");
		   http_response_code(Anweshan\Http\HTTPCode::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE);
		   include_once ($error_dir->get('error.php')->getPath());
		   exit;
		}

		$seek_start = (strlen($seek_start) == 0 || !is_numeric($seek_start))? 0 : intval($seek_start);

		if(!is_int($seek_start)){
		   $seek_start = 0;
		}

		if(empty($seek_end)){
		   $seek_end = $seek_start + intval(abs($size / $min_threshold));
		}

		if($seek_end >= ($size - 1)){
		   $seek_end = ($size - 1);
		}



		header("Accept-Ranges: ${size_unit}");
		header("Content-Range: {$size_unit} ".$seek_start.'-'.$seek_end.'/'.$size);
		header('Content-Length: '.($seek_end - $seek_start + 1));
		header("Content-Transfer-Encoding: binary");
		header("Content-Type: {$file->getMime()}");
		header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
		header("Content-Disposition: inline; filename=\"{$filename}\"");
		header("Access-Control-Expose-Headers: Range, Content-Length, Method");
		header("Expires: {$expires}");

		http_response_code(Anweshan\Http\HTTPCode::HTTP_PARTIAL_CONTENT);


		$default_bytes = 1024 * chunk_size;
		$cur=$seek_start;

		$fp = fopen($file->getPath(), 'rb');
		fseek($fp, $seek_start);

		while(!feof($fp) ){
		  set_time_limit(0);
		  print(fread($fp,$default_bytes));
		  $cur+= $default_bytes;
		  flush();
		  ob_flush();
		}

		fclose($fp);
	  }else{
		header('Accept-Ranges: bytes');
		header("Content-Length: {$file->getSize()}");
		header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
		header("Content-Type: {$file->getMime()}");
		header("Content-Disposition: inline; filename=\"{$filename}\"");
		header("Access-Control-Expose-Headers: Range, Content-Length, Method");
		header("Expires: {$expires}");
		header("X-Hash: md5-{$file->getHash('md5')}");


		ob_start();
		echo file_get_contents($file->getPath());
		$contents = ob_get_contents();
		$length = ob_get_length();
		ob_end_clean();

		header("Content-Length: {$length}");
		http_response_code(Anweshan\Http\HTTPCode::HTTP_OK);
		echo $contents;
	  }
	  exit;
	}
	
	return $file;
}