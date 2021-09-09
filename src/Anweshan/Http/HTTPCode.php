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
namespace Anweshan\Http;

use ReflectionClass;

/**
 * The class HTTPCode defines all known HTTP Request & Reponse Codes as defined by RFC7231.
 *
 * To learn more, please visit {@link https://tools.ietf.org/html/rfc7231 RFC7231}.
 *
 * Another helpful tool for Status Monitoring is {@link https://httpstatuses.com https://httpstatuses.com}. For info on a particular code, access the link as _https://httpstatuses.com/100_. For eg, for status code 404, we use _https://httpstatuses.com/404_.
 *
 * @package Anweshan\Http
 *
 * @author Anweshan
 * @since 2021
 * @version 2
 * @license MIT
 */
final class HTTPCode
{
    /**
     * Continue.
     * @var integer
     */
    public const HTTP_CONTINUE = 100;
    /**
     * Switching Protocols.
     * @var integer
     */
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    /**
     * Processing.
     * @var integer
     */
    public const HTTP_PROCESSING = 102;
    /**
     * OK.
     * @var integer
     */
    public const HTTP_OK = 200;
    /**
     * Created.
     * @var integer
     */
    public const HTTP_CREATED = 201;
    /**
     * Accepted.
     * @var integer
     */
    public const HTTP_ACCEPTED = 202;
    /**
     * Non-authoritative Information.
     * @var integer
     */
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    /**
     * No Content.
     * @var integer
     */
    public const HTTP_NO_CONTENT = 204;
    /**
     * Reset Content.
     * @var integer
     */
    public const HTTP_RESET_CONTENT = 205;
    /**
     * Partial Content.
     * @var integer
     */
    public const HTTP_PARTIAL_CONTENT = 206;
    /**
     * Multi-Status.
     * @var integer
     */
    public const HTTP_MULTI_STATUS = 207;
    /**
     * Already Reported.
     * @var integer
     */
    public const HTTP_ALREADY_REPORTED = 208;
    /**
     * IM Used.
     * @var integer
     */
    public const HTTP_IM_USED = 226;
    /**
     * Multiple Choices.
     * @var integer
     */
    public const HTTP_MULTIPLE_CHOICES = 300;
    /**
     * Moved Permanently.
     * @var integer
     */
    public const HTTP_MOVED_PERMANENTLY = 301;
    /**
     * Found.
     * @var integer
     */
    public const HTTP_FOUND = 302;
    /**
     * See Other.
     * @var integer
     */
    public const HTTP_SEE_OTHER = 303;
    /**
     * Not Modified.
     * @var integer
     */
    public const HTTP_NOT_MODIFIED = 304;
    /**
     * Use Proxy.
     * @var integer
     */
    public const HTTP_USE_PROXY = 305;
    /**
     * Temporary Redirect.
     * @var integer
     */
    public const HTTP_TEMPORARY_REDIRECT = 307;
    /**
     * Permanent Redirect.
     * @var integer
     */
    public const HTTP_PERMANENT_REDIRECT = 308;
    /**
     * Bad Request.
     * @var integer
     */
    public const HTTP_BAD_REQUEST = 400;
    /**
     * Unauthorized.
     * @var integer
     */
    public const HTTP_UNAUTHORIZED = 401;
    /**
     * Payment Required.
     * @var integer
     */
    public const HTTP_PAYMENT_REQUIRED = 402;
    /**
     * Forbidden.
     * @var integer
     */
    public const HTTP_FORBIDDEN = 403;
    /**
     * Not Found.
     * @var integer
     */
    public const HTTP_NOT_FOUND = 404;
    /**
     * Method Not Allowed.
     * @var integer
     */
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    /**
     * Not Acceptable.
     * @var integer
     */
    public const HTTP_NOT_ACCEPTABLE = 406;
    /**
     * Proxy Authentication Required.
     * @var integer
     */
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    /**
     * Request Timeout.
     * @var integer
     */
    public const HTTP_REQUEST_TIMEOUT = 408;
    /**
     * Conflict.
     * @var integer
     */
    public const HTTP_CONFLICT = 409;
    /**
     * Gone.
     * @var integer
     */
    public const HTTP_GONE = 410;
    /**
     * Length Required.
     * @var integer
     */
    public const HTTP_LENGTH_REQUIRED = 411;
    /**
     * Precondition Failed.
     * @var integer
     */
    public const HTTP_PRECONDITION_FAILED = 412;
    /**
     * Payload Too Large.
     * @var integer
     */
    public const HTTP_PAYLOAD_TOO_LARGE = 413;
    /**
     * Request URI Too Long.
     * @var integer
     */
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    /**
     * Unsupported Media Type.
     * @var integer
     */
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    /**
     * Requested Range Not Satisfiable.
     * @var integer
     */
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    /**
     * Expectation Failed.
     * @var integer
     */
    public const HTTP_EXPECTATION_FAILED = 417;
    /**
     * I am a teapot.
     * @var integer
     */
    public const HTTP_I_AM_A_TEAPOT = 418;
    /**
     * Misdirected Request.
     * @var integer
     */
    public const HTTP_MISDIRECTED_REQUEST = 421;
    /**
     * Unprocessable Entity.
     * @var integer
     */
    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    /**
     * Locked.
     * @var integer
     */
    public const HTTP_LOCKED = 423;
    /**
     * Failed Dependency.
     * @var integer
     */
    public const HTTP_FAILED_DEPENDENCY = 424;
    /**
     * Upgrade Required.
     * @var integer
     */
    public const HTTP_UPGRADE_REQUIRED = 426;
    /**
     * Precondition Required.
     * @var integer
     */
    public const HTTP_PRECONDITION_REQUIRED = 428;
    /**
     * Too Many Requests.
     * @var integer
     */
    public const HTTP_TOO_MANY_REQUESTS = 429;
    /**
     * Request Header Fields Too Large.
     * @var integer
     */
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    /**
     * Connection Closed Without Response.
     * @var integer
     */
    public const HTTP_CONNECTION_CLOSED_WITHOUT_RESPONSE = 444;
    /**
     * Unavailable For Legal Reasons.
     * @var integer
     */
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    /**
     * Client Closed Request.
     * @var integer
     */
    public const HTTP_CLIENT_CLOSED_REQUEST = 499;
    /**
     * Internal Server Error.
     * @var integer
     */
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    /**
     * Not Implemented.
     * @var integer
     */
    public const HTTP_NOT_IMPLEMENTED = 501;
    /**
     * Bad Gateway.
     * @var integer
     */
    public const HTTP_BAD_GATEWAY = 502;
    /**
     * Service Unavailable.
     * @var integer
     */
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    /**
     * Gateway Timeout.
     * @var integer
     */
    public const HTTP_GATEWAY_TIMEOUT = 504;
    /**
     * HTTP Version Not Supported.
     * @var integer
     */
    public const HTTP_HTTP_VERSION_NOT_SUPPORTED = 505;
    /**
     * Variant Also Negotiates.
     * @var integer
     */
    public const HTTP_VARIANT_ALSO_NEGOTIATES = 506;
    /**
     * Insufficient Storage.
     * @var integer
     */
    public const HTTP_INSUFFICIENT_STORAGE = 507;
    /**
     * Loop Detected.
     * @var integer
     */
    public const HTTP_LOOP_DETECTED = 508;
    /**
     * Not Extended.
     * @var integer
     */
    public const HTTP_NOT_EXTENDED = 510;
    /**
     * Network Authentication Required.
     * @var integer
     */
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;
    /**
     * Network Connect Timeout Error.
     * @var integer
     */
    public const HTTP_NETWORK_CONNECT_TIMEOUT_ERROR = 599;
    
    
    /**
     * Get all constant values as an array.
     * @return array
     */
    public static function getConstants() {
        $obj = new ReflectionClass(__CLASS__);
        return $obj->getConstants();
    }
    
    
}