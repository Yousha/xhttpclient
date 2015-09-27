<?php

if (!defined('DRIVERINTERFACE_PHP')) {
   define('DRIVERINTERFACE_PHP', true);
} else {
   return;
}

/**
 * Interface for HTTP drivers.
 */
interface DriverInterface
{
   /**
    * Constructor.
    *
    * @param CookieJar $cookieJar The cookie jar to use for storing cookies.
    */
   public function __construct(CookieJar $cookieJar);

   /**
    * Send an HTTP GET request.
    */
   public function get($url, $headers = array());

   /**
    * Send an HTTP POST request.
    */
   public function post($url, $data, $headers = array());

   /**
    * Send an HTTP PUT request.
    */
   public function put($url, $data, $headers = array());

   /**
    * Send an HTTP DELETE request.
    */
   public function delete($url, $headers = array());

   /**
    * Set timeout for the request.
    */
   public function setTimeout($seconds);

   /**
    * Set default headers to include in every request.
    */
   public function setHeaders($headers);

   /**
    * Unified method to send custom HTTP requests.
    */
   public function sendRequest($method, $url, $data = null, $headers = array());
}
