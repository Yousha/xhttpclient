<?php

if (!defined('HTTPCLIENTEXCEPTION_PHP')) {
   define('HTTPCLIENTEXCEPTION_PHP', true);
} else {
   return;
}

/**
 * Custom exception for HTTP Client errors.
 */
final class HttpClientException extends Exception
{
   private $context = array();

   public function __construct($message, $code = 0)
   {
      parent::__construct($message, $code);
   }

   public function setContext($context)
   {
      $this->context = $context;
   }

   public function getContext()
   {
      return $this->context;
   }

   public function __toString()
   {
      $str = __CLASS__ . ": [{$this->code}]: {$this->message}\n";
      if (!empty($this->context)) {
         foreach ($this->context as $key => $value) {
            $str .= " [$key] $value\n";
         }
      }
      return $str;
   }
}
