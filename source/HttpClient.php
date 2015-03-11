<?php

if (!defined('HTTPCLIENT_PHP')) {
   define('HTTPCLIENT_PHP', true);
} else {
   return;
}

require_once 'Drivers/DriverInterface.php';
require_once 'Drivers/CurlDriver.php';
require_once 'Drivers/SocketDriver.php';
require_once 'Exceptions/HttpClientException.php';

/**
 * Main HTTP client class. It acts as a wrapper and selects a driver based on options.
 */
final class HttpClient
{
   private $driver;

   public function __construct($driverType = 'curl')
   {
      if ($driverType === 'curl') {
         $this->driver = new CurlDriver();
      } elseif ($driverType === 'socket') {
         $this->driver = new SocketDriver();
      } else {
         throw new HttpClientException('Invalid driver type.');
      }
   }

   public function get($url, $headers = array())
   {
      return $this->driver->get($url, $headers);
   }

   public function post($url, $data, $headers = array())
   {
      return $this->driver->post($url, $data, $headers);
   }

   public function put($url, $data, $headers = array())
   {
      return $this->driver->put($url, $data, $headers);
   }

   public function delete($url, $headers = array())
   {
      return $this->driver->delete($url, $headers);
   }

   public function setTimeout($seconds)
   {
      $this->driver->setTimeout($seconds);
   }

   public function setHeaders($headers)
   {
      $this->driver->setHeaders($headers);
   }
}
