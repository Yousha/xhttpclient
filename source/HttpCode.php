<?php

if (!defined('HTTPCODE_PHP')) {
   define('HTTPCODE_PHP', true);
} else {
   return;
}

/**
 * Provides human-readable HTTP status code messages.
 */
class HttpCode
{
   /**
    * Associative array of HTTP status codes and their corresponding messages.
    * @var array
    */
   private static $messages = array(
      // 1xx Informational
      100 => 'Continue',
      101 => 'Switching Protocols',

      // 2xx Success
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',

      // 3xx Redirection
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      307 => 'Temporary Redirect',

      // 4xx Client Error
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Timeout',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Payload Too Large',
      414 => 'URI Too Long',
      415 => 'Unsupported Media Type',
      416 => 'Range Not Satisfiable',
      417 => 'Expectation Failed',

      // 5xx Server Error
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Timeout',
      505 => 'HTTP Version Not Supported'
   );

   /**
    * Returns the message for a given HTTP status code.
    *
    * @param int $code The HTTP status code.
    * @return string The associated message, or 'Unknown Status' if not found.
    */
   public static function getMessage($code)
   {
      $code = (int) $code;
      return isset(self::$messages[$code]) ? self::$messages[$code] : 'Unknown Status';
   }

   /**
    * Checks whether a status code is informational (1xx).
    *
    * @param int $code
    * @return bool
    */
   public static function isInformational($code)
   {
      return $code >= 100 && $code < 200;
   }

   /**
    * Checks whether a status code is successful (2xx).
    *
    * @param int $code
    * @return bool
    */
   public static function isSuccess($code)
   {
      return $code >= 200 && $code < 300;
   }

   /**
    * Checks whether a status code indicates redirection (3xx).
    *
    * @param int $code
    * @return bool
    */
   public static function isRedirection($code)
   {
      return $code >= 300 && $code < 400;
   }

   /**
    * Checks whether a status code indicates a client error (4xx).
    *
    * @param int $code
    * @return bool
    */
   public static function isClientError($code)
   {
      return $code >= 400 && $code < 500;
   }

   /**
    * Checks whether a status code indicates a server error (5xx).
    *
    * @param int $code
    * @return bool
    */
   public static function isServerError($code)
   {
      return $code >= 500 && $code < 600;
   }

   /**
    * Checks whether a status code is an error (4xx or 5xx).
    *
    * @param int $code
    * @return bool
    */
   public static function isError($code)
   {
      return self::isClientError($code) || self::isServerError($code);
   }
}
