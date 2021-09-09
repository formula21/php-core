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
 * The interface BrowserInterface defines some methods and constants which are common accross all browsers.
 * @package Anweshan\Util
 * 
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
interface BrowserInterface
{
	/**
	 * @var string
	 */
    const BROWSER_UNKNOWN = 'unknown';
	
	/**
	 * @var string
	 */
    const VERSION_UNKNOWN = 'unknown';
    
	/**
	 * @var string
	 * @link http://www.opera.com
	 */
    const BROWSER_OPERA = 'Opera';
    
	/**
	 * @var string
	 * @link http://www.opera.com/mini/
	 */
	const BROWSER_OPERA_MINI = 'Opera Mini';
    
	/**
	 * @var string
	 * @link http://www.webtv.net/pc
	 */
	const BROWSER_WEBTV = 'WebTV';
    
	/**
	 * @var string
	 * @link http://www.microsoft.com/edge
	 */
	const BROWSER_EDGE = 'Edge';
    
	/**
	 * @var string
	 * @link http://www.microsoft.com/ie/
	 */
	const BROWSER_IE = 'Internet Explorer';
    
	/**
	 * @var string
	 * @link http://en.wikipedia.org/wiki/Internet_Explorer_Mobile
	 */
	const BROWSER_POCKET_IE = 'Pocket Internet Explorer';

	/**
	 * @var string
	 * @link http://www.konqueror.org/
	 * @since 1.7
	 */
	const BROWSER_KONQUEROR = 'Konqueror';
    
	/**
	 * @var string
	 * @since 1.5
	 * @link http://www.icab.de/
	 */
	const BROWSER_ICAB = 'iCab';
    
	/**
	 * @var string
	 * @link http://www.omnigroup.com/applications/omniweb/
	 * @since 1.6
	 */
	const BROWSER_OMNIWEB = 'OmniWeb';
    
	/**
	 * @var string
	 * @link http://www.ibphoenix.com/
	 */
	const BROWSER_FIREBIRD = 'Firebird';
    
	/**
	 * @var string
	 * @link https://www.mozilla.org/en-US/firefox/
	 */
	const BROWSER_FIREFOX = 'Firefox';
    
	/**
	 * @var string
	 * @since 1.8
	 * @link https://brave.com/
	 */
	const BROWSER_BRAVE = 'Brave';
    
	/**
	 * @var string
	 * @since 1.8
	 * @link https://www.palemoon.org/
	 */
	const BROWSER_PALEMOON = 'Palemoon';
    
	/**
	 * @var string
	 * @since 1.8
	 * @link http://www.geticeweasel.org/
	 */
	const BROWSER_ICEWEASEL = 'Iceweasel';
    
	/**
	 * @var string
	 * @since 1.8
	 * @link http://wiki.mozilla.org/Projects/shiretoko
	 */
	const BROWSER_SHIRETOKO = 'Shiretoko';
    
	/**
	 * @var string
	 * @link http://www.mozilla.com/en-US/
	 */
	const BROWSER_MOZILLA = 'Mozilla';
    
	/**
	 * @var string
	 * @since 1.8
	 * @link http://www.w3.org/Amaya/
	 */
	const BROWSER_AMAYA = 'Amaya';
    
	/**
	 * @var string
	 * @link http://en.wikipedia.org/wiki/Lynx
	 */
	const BROWSER_LYNX = 'Lynx';
    
	/**
	 * @var string
	 * @link http://apple.com
	 */
	const BROWSER_SAFARI = 'Safari';
    
	/**
	 * @var string
	 * @link http://apple.com
	 */
	const BROWSER_IPHONE = 'iPhone';
    
	/**
	 * @var string
	 * @link http://apple.com
	 */
	const BROWSER_IPOD = 'iPod';
    
	/**
	 * @var string
	 * @link http://apple.com
	 */
	const BROWSER_IPAD = 'iPad';
    
	/**
	 * @var string
	 * @link http://www.google.com/chrome
	 */
	const BROWSER_CHROME = 'Chrome';
    
	/**
	 * @var string
	 * @link http://www.android.com/
	 */
	const BROWSER_ANDROID = 'Android';
    
	/**
	 * @var string
	 * @link http://en.wikipedia.org/wiki/Googlebot
	 */
	const BROWSER_GOOGLEBOT = 'GoogleBot';
    
	/**
	 * @var string
	 * @link https://en.wikipedia.org/wiki/CURL
	 */
	const BROWSER_CURL = 'cURL';
    
	/**
	 * @var string
	 * @link https://en.wikipedia.org/wiki/Wget
	 */
	const BROWSER_WGET = 'Wget';
    
	/**
	 * @var string
	 * @link https://www.ucweb.com/
	 */
	const BROWSER_UCBROWSER = 'UCBrowser';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXBOT = 'YandexBot';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXIMAGERESIZER_BOT = 'YandexImageResizer';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXIMAGES_BOT = 'YandexImages';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXVIDEO_BOT = 'YandexVideo';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXMEDIA_BOT = 'YandexMedia';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXBLOGS_BOT = 'YandexBlogs';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXFAVICONS_BOT = 'YandexFavicons';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXWEBMASTER_BOT = 'YandexWebmaster';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXDIRECT_BOT = 'YandexDirect';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXMETRIKA_BOT = 'YandexMetrika';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXNEWS_BOT = 'YandexNews';
    
	/**
	 * @var string
	 * @link http://yandex.com/bots
	 */
	const BROWSER_YANDEXCATALOG_BOT = 'YandexCatalog';
    
	/**
	 * @var string
	 * @link http://en.wikipedia.org/wiki/Yahoo!_Slurp
	 */
	const BROWSER_SLURP = 'Yahoo! Slurp';
    
	/**
	 * @var string
	 * @link http://validator.w3.org/
	 */
	const BROWSER_W3CVALIDATOR = 'W3C Validator';
    
	/**
	 * @var string
	 * @link http://www.blackberry.com/
	 */
	const BROWSER_BLACKBERRY = 'BlackBerry';
    
	/**
	 * @var string
	 * @since 1.1
	 * @link http://en.wikipedia.org/wiki/GNU_IceCat
	 */
	const BROWSER_ICECAT = 'IceCat';
    
	/**
	 * @var string
	 * @link http://en.wikipedia.org/wiki/Web_Browser_for_S60
	 */
	const BROWSER_NOKIA_S60 = 'Nokia S60 OSS Browser';
    
	/**
	 * WAP-based browsers on the Nokia Platform.
	 * @var string
	 */
	const BROWSER_NOKIA = 'Nokia Browser';
    
	/**
	 * @var string
	 * @link http://explorer.msn.com/
	 */
	const BROWSER_MSN = 'MSN Browser';
    
	/**
	 * @var string
	 * @link http://search.msn.com/msnbot.htm
	 */
	const BROWSER_MSNBOT = 'MSN Bot';
    
	/**
	 * @var string
	 * @link http://en.wikipedia.org/wiki/Bingbot
	 */
	const BROWSER_BINGBOT = 'Bing Bot';
    
	/**
	 * @var string
	 * @since 1.9
	 * @link https://vivaldi.com/
	 */
	const BROWSER_VIVALDI = 'Vivaldi';
    
	/**
	 * @var string
	 * @link https://browser.yandex.ua/
	 */
	const BROWSER_YANDEX = 'Yandex';
    
	/**
	 * @var string
	 * @since 0.1
	 * @deprecated Since 1.8
	 * @link http://browser.netscape.com/
	 */
	const BROWSER_NETSCAPE_NAVIGATOR = 'Netscape Navigator';
    
	/**
	 * @var string
	 * @since 0.3
	 * @deprecated Since 1.0
	 * @link http://galeon.sourceforge.net/
	 */
	const BROWSER_GALEON = 'Galeon';
    
	/**
	 * @var string
	 * @deprecated Since 1.0
	 * @link http://en.wikipedia.org/wiki/NetPositive
	 * @since 0.1
	 */
	const BROWSER_NETPOSITIVE = 'NetPositive';
    
	/**
	 * @var string
	 * @deprecated Since 1.0
	 * @since 0.1
	 * @link http://en.wikipedia.org/wiki/History_of_Mozilla_Firefox
	 */
	const BROWSER_PHOENIX = 'Phoenix';
    
	/**
	 * @var string
	 */
	const BROWSER_PLAYSTATION = "PlayStation";
    
	/**
	 * @var string
	 */
	const BROWSER_SAMSUNG = "SamsungBrowser";
    
	/**
	 * @var string
	 */
	const BROWSER_SILK = "Silk";
    
	/**
	 * @var string
	 */
	const BROWSER_I_FRAME = "Iframely";
    
	/**
	 * @var string
	 */
	const BROWSER_COCOA = "CocoaRestClient";
	
	/**
	 * @var string
	 */
	const PLATFORM_UNKNOWN = 'unknown';
    
	/**
	 * @var string
	 */
	const PLATFORM_WINDOWS = 'Windows';
    
	/**
	 * @var string
	 */
	const PLATFORM_WINDOWS_CE = 'Windows CE';
    
	/**
	 * @var string
	 */
	const PLATFORM_APPLE = 'Apple';
    
	/**
	 * @var string
	 */
	const PLATFORM_LINUX = 'Linux';
    
	/**
	 * @var string
	 */
	const PLATFORM_OS2 = 'OS/2';
    
	/**
	 * @var string
	 */
	const PLATFORM_BEOS = 'BeOS';
    
	/**
	 * @var string
	 */
	const PLATFORM_IPHONE = 'iPhone';
    
	/**
	 * @var string
	 */
	const PLATFORM_IPOD = 'iPod';
    
	/**
	 * @var string
	 */
	const PLATFORM_IPAD = 'iPad';
    
	/**
	 * @var string
	 */
	const PLATFORM_BLACKBERRY = 'BlackBerry';
    
	/**
	 * @var string
	 */
	const PLATFORM_NOKIA = 'Nokia';
    
	/**
	 * @var string
	 */
	const PLATFORM_FREEBSD = 'FreeBSD';
    
	/**
	 * @var string
	 */
	const PLATFORM_OPENBSD = 'OpenBSD';
    
	/**
	 * @var string
	 */
	const PLATFORM_NETBSD = 'NetBSD';
    
	/**
	 * @var string
	 */
	const PLATFORM_SUNOS = 'SunOS';
    
	/**
	 * @var string
	 */
	const PLATFORM_OPENSOLARIS = 'OpenSolaris';
    
	/**
	 * @var string
	 */
	const PLATFORM_ANDROID = 'Android';
    
	/**
	 * @var string
	 */
	const PLATFORM_PLAYSTATION = "Sony PlayStation";
    
	/**
	 * @var string
	 */
	const PLATFORM_ROKU = "Roku";
    
	/**
	 * @var string
	 */
	const PLATFORM_APPLE_TV = "Apple TV";
    
	/**
	 * @var string
	 */
	const PLATFORM_TERMINAL = "Terminal";
    
	/**
	 * @var string
	 */
	const PLATFORM_FIRE_OS = "Fire OS";
    
	/**
	 * @var string
	 */
	const PLATFORM_SMART_TV = "SMART-TV";
    
	/**
	 * @var string
	 */
	const PLATFORM_CHROME_OS = "Chrome OS";
    
	/**
	 * @var string
	 */
	const PLATFORM_JAVA_ANDROID = "Java/Android";
    
	/**
	 * @var string
	 * @since 1.9
	 */
	const PLATFORM_POSTMAN = "Postman";
    
	/**
	 * @var string
	 */
	const PLATFORM_I_FRAME = "Iframely";
	
	/**
	 * @var string
	 */
	const OPERATING_SYSTEM_UNKNOWN = 'unknown';
    
    
    /**
     * Reset all properties.
     */
    public function reset();
    
    /**
     * Check to see if the specific browser is valid.
     * @param mixed $browserName
     * @return bool
     */
    public function isBrowser($browserName) : bool;
    
    /**
     * The name of the browser. 
     * 
     * @return string|null Name of the browser
     */
    public function getBrowser() :?string;
    
    /**
     * Set the name of the browser.
     * @param string|null $browser The name of the Browser
     */
    public function setBrowser(?string $browser);
    
    /**
     * The name of the platform.  
     * 
     * All return types are from the class contants
     * 
     * @return string|null Name of the browser
     */
    public function getPlatform(): ?string;
    
    /**
     * Set the name of the platform
     * @param string $platform The name of the Platform
     */
    public function setPlatform(?string $platform);
    
    /**
     * The version of the browser.
     * @return string Version of the browser (will only contain alpha-numeric characters and a period)
     */
    public function getVersion() : ?string;
    
    /**
     * Set the version of the browser
     * @param string $version The version of the Browser, will only contain alpha-numeric characters and a period.
     */
    public function setVersion(?string $version);
    
    /**
     * Is the browser from a mobile device?
     * @return bool True if the browser is from a mobile device otherwise false.
     */
    public function isMobile() : bool;
    
    /**
     * Is the browser from a tablet device?
     * @return bool True if the browser is from a tablet device otherwise false.
     */
    public function isTablet() : bool;
    
    /**
     * Is the browser from a robot (ex Slurp,GoogleBot)?
     * @return bool True if the browser is from a robot otherwise false.
     */
    public function isRobot() : bool;
    
    /**
     * Get the user agent value in use to determine the browser.
     * @return string|null The user agent from the HTTP header.
     */
    public function getUserAgent() :?string;
    
    /**
     * Set the user agent value.
     * @param string|null $agent The value for the User Agent.
     */
    public function setUserAgent(?string $agent);
    
}

