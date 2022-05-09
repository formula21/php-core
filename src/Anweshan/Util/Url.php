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
 * The class Url, makes and implements a Uniform Resource Locator (URL).
 *
 * @package Anweshan\Util
 *
 * @author Anweshan
 * @since 2021
 * @version 2.1
 * @license MIT
 */
final class Url
{
    public const INDEX_PAGE = 'index.php';

    public const URL_SEPARATOR = '/';

    public const PORT_INSECURE = 80;

    public const PORT_SECURE = 443;

    /**
     * Builds a URL.
     * @param string ...$segemnts Variable number of URL segments.
     * @return string The built URL.
     */
    public static function buildURL(string ...$segments){
       return join(self::URL_SEPARATOR, $segments);
    }

    /**
     * Makes an URL from a given uri.
     * @param string $uri The required uri.
     * @return string The final url.
     */
     public static function getPageWithUri(string $uri = null){
        if(is_string($uri) && empty($uri)){
           $uri = '/';
        }

        if(is_null($uri)){
           $uri = self::getPath();
        }

        $uri = self::stripSlash(trim($uri, '/'));
        $protocol = self::getProtocol();
        $port = ':' . self::getPort();
        $port = (($port == ':'.self::PORT_INSECURE) || ($port == ':'.self::PORT_SECURE)) ? '' : $port;
        $domain = self::getDomain();
        $url = "{$protocol}{$port}://{$domain}/{$uri}";
        return $url;
     }

    /**
     * Gets the current page uri.
     * @param bool $no_index_path Flag determines where the URI will contain the index page name & extension.
     * @param string $index_page_default The name of the index page, where any requests are analyzed.
     * @return string The current page URI (Uniform Resource Identifier).
     */
    public static function getCurrentUri(bool $no_index_path = false, string $index_page_default = NULL){

        $index_page_default = $index_page_default ?? self::INDEX_PAGE;

        $path = self::addBackSlash(trim(self::getPath(), '/'), 'top');
        $path = self::stripSlash($path);

        $query = self::getQuery() ?? "";

        if(!empty($query)){
            $query = "?".$query;
        }

        $fragment = self::getFragment() ?? "";

        // Check for fragments
        if(!empty($fragment)){
            $fragment = "#".$fragment;
        }


        // Add the query and fragment
        $uri = rtrim($path.self::addBackSlash($query.$fragment, 'top'), '/');

        // Check uri is empty or not

        $uri = !empty($uri)?$uri:'/';

        // Add corrections for words having the indexof "index.php"
        // but not index.php
        // like "pindex.php" or "table_index.php"
        if($no_index_path && stripos($uri, $index_page_default) !== false){
           $c = stripos($uri, $index_page_default);
           $char = @$uri[$c-1] ?? '';
           if($char !== self::URL_SEPARATOR || empty($char)){
              $no_index_path = false;
           }
        }

        if($no_index_path && stripos($uri, $index_page_default) !== false){
            $before = trim(strstr($uri, $index_page_default, true), '/');
            $after = trim(substr($uri,stripos($uri, $index_page_default)+strlen($index_page_default)), '/');
            $uri = $before.'/'.$after;
            $uri = rtrim($uri, '/');
        }

        return $uri;
    }

    /**
     * Gets the base url.
     * @return string|null The base url.
     */
    public function getBaseUrl(){
        $uri = self::getCurrentUri();
        $url = self::getCurrentPage();

        if ($uri !== '/') {
            $url = trim(str_replace($uri, '', $url), '/');
        }

        return self::addBackSlash($url);
    }

    /**
     * The current page uniform resource locator.
     * @param bool $strip_slash Flag to signify if excess slashes are stripped.
     * @param bool $no_index_path Flag to signify if default index is included
     * @param string $index_page_default The name of the default index
     * @return string The URL (Uniform Resource Locator).
     */
    public static function getCurrentPage(bool $strip_slash = false, bool $no_index_path = false, string $index_page_default = NULL){

        $protocol = self::getProtocol();
        $host = self::getDomain();
        $port = ':' . self::getPort();
        $port = (($port == ':'.self::PORT_INSECURE) || ($port == ':'.self::PORT_SECURE)) ? '' : $port;

        $index_page_default = $index_page_default ?? self::INDEX_PAGE;

        $path = self::addBackSlash(trim(self::getPath(),'/'), 'top');

        if($strip_slash){
            $path = self::stripSlash($path);
        }

        $query = self::getQuery() ?? "";

        if(!empty($query)){
            $query = "?".$query;
        }

        $fragment = self::getFragment() ?? "";

        if(!empty($fragment)){
            $fragment = "#".$fragment;
        }

        $url = $host.$port.$path.self::addBackSlash($query.$fragment, 'top');

        // Add corrections for words having the indexof "index.php"
        // but not index.php
        // like "pindex.php" or "table_index.php"
        if($no_index_path && stripos($url, $index_page_default) !== false){
           $c = stripos($url, $index_page_default);
           $char = @$url[$c-1] ?? '';
           if($char !== self::URL_SEPARATOR || empty($char)){
              $no_index_path = false;
           }
        }

        if($no_index_path && stripos($url, $index_page_default)!==false){
            $before = trim(strstr($url, $index_page_default, true), '/');
            $after = trim(substr($url,stripos($url, $index_page_default)+strlen($index_page_default)), '/');
            $url = $before.'/'.$after;
            $url = rtrim($url, '/');
        }

        if($strip_slash){
            $url = rtrim(self::stripSlash($url), '/');
        }

        $url = $protocol."://".$url;

        return $url;

    }

    /**
     * Gets the fragment portion of the URL or URI (i.e. after #).
     * @param bool|string $uri The URL (or URI).
     * @return mixed The fragment portion of the uri
     */
    public static function getFragment($uri = false){
        if(!$uri){
            $uri = self::getUri();
        }
        return parse_url($uri, PHP_URL_FRAGMENT);
    }


    /**
     * Go to the previous URL.
     */
    public static function previous()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
     * Redirect to chosen URL.
     *
     * @param string $url → the URL to redirect
     */
    public static function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Strips the URI or URL off slashes ('/') excess.
     * @param string $uri The uri to be stripped.
     * @return mixed The uri after stripping.
     */
    public static function stripSlash(string $uri = null){
        if(!$uri){
            $uri = $_SERVER['REQUEST_URI'];
        }

        $uri = preg_replace('/(\/+)/','/',$uri);
        return $uri;
    }

    /**
     * Gets the protocol (http or https).
     * @param bool|string $url The url to be analyzed.
     * @return string http or https (http by default).
     */
    public static function getProtocol($url = false)
    {
        if ($url) {
            return (preg_match('/^https/', $url)) ? 'https' : 'http';
        }

        $protocol = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($protocol, 0, strpos($protocol, '/'));

        $ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');

        return ($ssl) ? $protocol . 's' : $protocol;
    }

    /**
     * Gets if site is SSL enabled.
     * @param bool|string $url The url to be analyzed.
     * @return bool
     */
    public static function isSSL($url = false)
    {
        return self::getProtocol($url) === 'https';
    }

    /**
     * Gets the server port.
     * @return int|string|null The port
     */
    public static function getPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * Gets the methods i.e. method/method1/method2...
     * @return string The methods from the document root.
     */
    public static function getUriMethods()
    {
        $root = str_replace($_SERVER['DOCUMENT_ROOT'], '', getcwd());
        $subfolder = trim($root, '/');

        return trim(str_replace($subfolder, '', self::getUri()), '/');
    }

    /**
     * Gets the path.
     * @param bool|string $url The url to be analyzed.
     * @return mixed The path from the url.
     */
    public static function getPath($url = false){
        if(!$url){
            $url = $_SERVER['REQUEST_URI'];
        }
        $url = trim($url, '/');
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * Gets the URI.
     * @return string
     */
    public static function getUri(){
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the query.
     * @param bool $url The url to be analyzed.
     * @return mixed The query string as key=value&...
     */
    public static function getQuery($url = false){
        if($url){
            return parse_url($url, PHP_URL_QUERY);
        }
        return $_SERVER['QUERY_STRING'] ?? "";
    }

    /**
     * Gets the domain.
     * @param bool|string $url The url to be analyzed.
     * @return mixed Gets the domain.
     */
    public static function getDomain($url = false)
    {
        if ($url) {
            return parse_url($url, PHP_URL_HOST);
        }

        return $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['SERVER_NAME'];
    }

    /**
     * Adds backslashes to the URI.
     * @param string $uri The URL or URI to analyze.
     * @param string $position The position to append the slash.
     * @return string|bool The uri with slashes or false.
     */
    public static function addBackSlash($uri, $position = 'end')
    {
        switch ($position) {
            case 'top':
                $uri = !empty($uri) ? '/' . ltrim($uri, '/') : '';
                break;
            case 'end':
                $uri = !empty($uri) ? rtrim($uri, '/') . '/' : '';
                break;
            case 'both':
                $uri = ! empty($uri) ? '/' . trim($uri, '/') . '/' : '';
                break;
            default:
                $uri = false;
        }

        return $uri;
    }

    /**
     * Gets the requested method.
     * @return string
     */
    public static function getRequestMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Checks if the resource is called by the requested method.
     * @param string $method Specify the method.
     * @return bool
     */
    public static function isRequestMethod(string $method){
        $method = strtoupper($method);
        return ($_SERVER['REQUEST_METHOD'] === $method);
    }

    /**
     * Returns if the method is PUT.
     * @return bool
     */
    public static function isPut(){
        return self::isRequestMethod('PUT');
    }

    /**
     * Returns if the method is POST.
     * @return bool
     */
    public static function isPost(){
        return self::isRequestMethod('POST');
    }

    /**
     * Returns if the method is GET.
     * @return bool
     */
    public static function isGet(){
        return self::isRequestMethod('GET');
    }

    /**
     * Returns an array of segments from the URI
     * @param string $uri The uniform resource identifier
     * @param bool $no_index_path If the uri should not contain the current index path.
     *                                (In case URI is not supplied.)
     *
     * @param string $index_page_default The default name of the index page.
     *
     * @return array|null
     */
     public static function segments(string $uri = null, bool $no_index_path = false, string $index_page_default = NULL){
         $uri = $uri ?? self::getCurrentUri($no_index_path, $index_page_default);
         $uri = ($uri!=NULL)?self::stripSlash($uri):$uri;
         if($uri && ((strlen($uri) == 1 && $uri == '/') || strlen(trim($uri, '/')) == 0)){
           return array('');
         }
         return ($uri && strlen($uri) > 0 )? explode('/', trim($uri, '/')) : null;
     }

     /**
      * Returns an first element from the array of segments of the URI
      * @param string $uri The uniform resource identifier
      * @param bool $no_index_path If the uri should not contain the current index path.
      *                                (In case URI is not supplied.)
      *
      * @param string $index_page_default The default name of the index page.
      *
      * @return string|null
      */
      public static function getFirstSegment(string $uri = null, bool $no_index_path = false, string $index_page_default = NULL){
          $segments = self::segments($uri, $no_index_path, $index_page_default);
          if($segments){
            return array_values($segments)[0];
          }
          return null;
      }

      /**
       * Returns an last element from the array of segments of the URI
       * @param string $uri The uniform resource identifier
       * @param bool $no_index_path If the uri should not contain the current index path.
       *                                (In case URI is not supplied.)
       *
       * @param string $index_page_default The default name of the index page.
       *
       * @return string|null
       */
       public static function getLastSegment(string $uri = null, bool $no_index_path = false, string $index_page_default = NULL){
           $segments = self::segments($uri, $no_index_path, $index_page_default);
           if($segments){
             return end($segments);
           }
           return null;
       }
}
