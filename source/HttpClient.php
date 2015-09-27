<?php

if (!defined('HTTPCLIENT_PHP')) {
   define('HTTPCLIENT_PHP', true);
} else {
   return;
}

require_once 'Exceptions/HttpClientException.php';
require_once 'CookieJar.php';
require_once 'Drivers/DriverInterface.php';
require_once 'Drivers/CurlDriver.php';
require_once 'Drivers/SocketDriver.php';

final class HttpClient
{
   private $driver;
   private $defaultHeaders = array();
   private $timeout = 30;
   private $cookieJar;

   public function __construct($driverType = 'curl', $options = array())
   {
      $this->cookieJar = new CookieJar();

      if ($driverType === 'curl') {
         $this->driver = new CurlDriver($this->cookieJar); // Pass cookie jar
      } elseif ($driverType === 'socket') {
         $this->driver = new SocketDriver($this->cookieJar);
      } else {
         $e = new HttpClientException('Invalid driver type.', 0);
         $e->setContext(array('given_type' => $driverType));
         throw $e;
      }

      if (isset($options['timeout'])) {
         $this->setTimeout($options['timeout']);
      }

      if (isset($options['default_headers']) && is_array($options['default_headers'])) {
         $this->setHeaders($options['default_headers']);
      }
   }

   public function get($url, $headers = array())
   {
      return $this->sendRequest('GET', $url, null, $headers);
   }

   public function post($url, $data, $headers = array())
   {
      return $this->sendRequest('POST', $url, $data, $headers);
   }

   public function put($url, $data, $headers = array())
   {
      return $this->sendRequest('PUT', $url, $data, $headers);
   }

   public function delete($url, $headers = array())
   {
      return $this->sendRequest('DELETE', $url, null, $headers);
   }

   public function setTimeout($seconds)
   {
      $this->timeout = $seconds;
      $this->driver->setTimeout($seconds);
   }

   public function setHeaders($headers)
   {
      $this->defaultHeaders = $headers;
      $this->driver->setHeaders($headers);
   }

   public function clearCookies()
   {
      $this->cookieJar->clear();
   }

   private function sendRequest($method, $url, $data = null, $headers = array())
   {
      try {
         return $this->driver->sendRequest($method, $url, $data, $headers);
      } catch (Exception $e) {
         $context = array(
            'url' => $url,
            'request_method' => $method,
            'driver' => get_class($this->driver)
         );

         if ($e instanceof HttpClientException) {
            $context = array_merge($context, $e->getContext());
         }

         $wrapped = new HttpClientException(
            "HTTP request failed: " . $e->getMessage(),
            $e->getCode()
         );
         $wrapped->setContext($context);
         throw $wrapped;
      }
   }
}
