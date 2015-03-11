<?php

if (!defined('DRIVERINTERFACE_PHP')) {
   define('DRIVERINTERFACE_PHP', true);
} else {
   return;
}

interface DriverInterface
{
   public function get($url, $headers = array());
   public function post($url, $data, $headers = array());
   public function put($url, $data, $headers = array());
   public function delete($url, $headers = array());
   public function setTimeout($seconds);
   public function setHeaders($headers);
}
