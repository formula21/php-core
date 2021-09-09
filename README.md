![PHP-CORE official LOGO](misc/logo.png?raw=true)

## Introduction

As a project PHP-Core is a package which includes the most needed functionalities to be used and extended by other APIs. These core features can be used form time to time, so as to make the execution of a problem easier. The library contains many exciting features, and is made in such a way enabling it to be extended by other developers from core.

## Features:
1. A HTTP Headers library
    - [X] Response Headers and API
      - Full legacy cache (with ETAG and Expires) Headers.
      - Easy to implement API Runner.
    - [ ] Request Headers and API
      - [X] Decoding and Re-sending same ETAG and Expires Header from `If-None-Match`.
    - [X] Request & Response Interfaces & Abstract Classes help other HTTP Responses to be implemented.
    - [X] Closely interlaced Response Codes, with Response Constants.
2. Filesystem Library:
   - [X] Platform Independent Build.
   - [X] A Base Interface to extend features, and is the parent interface of all components. It is recommended for a developer to do this the same way. For eg:
     > ```php
     > interface MyFilesystemComponentInterface extends \Anweshan\Filesystem\FilesystemInterface
     > {
     >    # Some code blocks
     > }
     > ```
   - [X] Contains three components:
      + **Directory System** a.k.a Directory
        * A *DirectoryInterface* defines the methods and its documentation to include a directory.
        * All basic functions (*limited to the permission set by the OS*) can be performed on any resource within the directory.
        * We can even write a `stream` or `file`, at any path relative to a directory, at a non existent path, which will be made by the code itself. Appropriate fallbacks are also there.
      + **Stream System** a.k.a Stream
        * A *StreamInterface* defines a stream which is basically a handler in the memory to temporarily store data.
        * A Stream can be written to a directory, while a file can be converted to a stream from it's path.
        * A Stream may act like a pseudo-file whenever required (*i.e. it may or may not have a path, but contains all properties of the **FileInterface** *)
      + **File System** a.k.a File
        * A *FileInterface* defines a file, which I think need not be explained.
        * Well a File and Stream are interconvertible, while both can be written to a directory.
    - [X] All components are wrapped by an interface, `FilesystemInterface`.
    - [X] A Stream can be only be considered a file if it at least has a filename or extension or both.
    - [X] Timestamps are auto calculated and are as follows:
       * Last Modified Timestamp
       * Last Accessed Timestamp
       * Created Timestamp
    - [X] Mime is auto-detected based on battle-tested [ralouphie\mimey](https://github.com/ralouphie/mimey).
    - [X] Content Length is also auto calculated.
    - [X] Exceptions:
       - > All exceptions of `DirectoryInterface` are wrapped under `DirectoryException`, except `InvalidArgumentException`.
         > ```php
         > try{
         >  // Some code block
         > }catch(\Anweshan\Filesystem\Directory\DirectoryException $e){
         >  var_dump($e);
         > }
         > ```
       - All exception of `FileInterface` and `StreamInterface` are wrapped under `FileException` and `StreamException` respectively, except for `InvalidArgumentException`.
     - [X] Hash:
        - [ ] Hashing a directory
        - [X] Hashing a file and/or a stream.
           - Hashes are of two categories:
             1. Hashing the file (or resource). \[Stream are only implemented if they have a file path, filename or file extension\]
                - `hash_file`
                - `hash_hmac_file`
             2. Hashing the contents of file (or resource).
                - `hash`
                - `hash_hmac`
             	3. > Hashes for an instance are **IMMUTABLE**, unless otherwise a `LOCK_EX` is obtained on the said resource. If there aren't any locks (by default), then the
                > hash won't change even if the content changes. However such check can be done, post implementation.
      - [X] Size:
         - [ ] Size of a directory.
         - [X] Size of a file & stream are calculated and immutable.
         > **NOTE:** Because PHP's integer type is signed and many platforms use 32bit integers,
         > some filesystem functions may return unexpected results for files which are larger than 2GB.
3. Utility:
   - Argument Utility:
     > The class wraps up most magic methods available in PHP. It helps us to define unknown properties, wherever required and later getting them.
     > Other extensive features can be done.
     - To get the properties defined into the class:
       ```php
       $x = new \Anweshan\Util\Argument();
       $x->y = 24;
       $key = 'User'
       $x->$key = array();
       foreach(\Anweshan\Util\Argument::get_object_vars($x) as $k=>$v){
        var_dump($k.'=>'.$v);
       }
       ```
   - Browser Utility:
     - All browser related functions.
   - Util Class:
     - Removing the single query parameter from the url.
     - Combing all query parameter to a url.
     - SantizePath - Sanitizes the path to a resource by removing invalid and excess characters.
     - makePath - Makes a path
     - makeURL - Makes a URL.
     - trim - Implements the trim PHP native function, but the 2nd parameter can be any numbers of characters.
     - makeDirectory - Makes a directory.
     - makeDateTime - Makes a proper datetime instance from a datetime
     - rmdir - Removes the directory and it's contents recursively. The native rmdir does not work on non-empty directories. Has a fallback if any exception occurs.
     - random_bytes: If you are missing random_bytes or openssl_random_bytes, we use this one.

4. A Database System (Abstraction Layer)
	- It is a library that implements an abstraction layer over the PDO extension, by providing a powerful query builder along with an easy to use schema builder. The aim of the library is to provide an unified way of interacting with databases, no matter of the underlying relational database management system.
	- Currently, we are officially supporting MySQL, PostgreSQL, Microsoft SQL, and SQLite. We also provide experimental support - without any commitment regarding bug fixes and updates - for Firebird, IBM DB2, Oracle, and NuoDB query builder.
	- **A DOCUMENTATION WILL SOON FOLLOW**
5. Image Manipulation System:
  - The system is described in detail below as "I.M.S".

### IMS ~ Image Manipulation System
To manipulate images we use the many features from the popular library [Leauge\Glide](http://glide.thephpleague.com "HTTP based image manipulations"), however improving several tiny bits which gives the project a makeover. We forward our thanks to the battle tested library [Intervention\Image](http://image.intervention.io).

**Extra documentation to follow soon**

## Installation:
If you have composer in your system and the location of the same in the `%PATH%` variable, then you can continue with the lower block.

```cmd
$ composer require formula21/php-core
```

*OR,*
You can always use php CLI and composer.phar, to execute all `composer` commands in the CLI.
- To obtain `composer.phar`, visit "Command-Line installation" of [https://getcomposer.org/download/](https://getcomposer.org/download/)
- Alternatively, just use `composer.bat` or edit the file to the desired extension, keeping all commands same (or run them directly one after the other in the CLI alone).

Then,

```cmd
$ php composer.phar require formula21/php-core
```

> Please check all packages & repositories are extracted from the archive and installed correctly.
> Raise an issue if there is an error.

That hopefully installs the latest version of `php-core` as vendor for your project.

## Manipulate it?
If you want to manipulate images place in the directory at path/to/dir/of/images (Can be both full or relative path).
```php
require_once __DIR__.'/vendor/autoload.php';

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
		$img = $response_api->run($favicon);
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
```

## Extending the Options

The options as stated above are just limited as is my imagination. You can freely expand the project as you like. Some extensions include:

1. Using `Imagick` as a driver, the user could extend this to document manipulation, like PDF "preview" generation, and post manipulation, with cache to store the same for further use.

2. Manipulations can be done on `Cascading Stylesheet CSS` and `JavaScript JS`, files like minifying them, getting Sub-Resource Integrity, Caching, etc.

3. Manipulations of multi-media files, likes generating a preview image, thumbnails \[based on the n-th second cut\] for video files, caching sections of both video and audio files, bytes implementation etc.

4. Manipulations and assembling of Documents, Spreadsheets, Presentations, and other popular file types can be added.  

## In short:
![PHP-CORE social media image](misc/php-core.png?raw=true)

## License

The project is licensed with the MIT License. A copy of the license must be attached with repository. The license is evaluated below:

```
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
```

```
~ @formula21 - (Anweshan Roy Chowdhury)
```
