<?php

if (!defined('CURLDRIVER_PHP')) {
   define('CURLDRIVER_PHP', true);
} else {
   return;
}

/**
 * CURL based driver.
 */
final class CurlDriver implements DriverInterface
{
   private $headers = array();
   private $timeout = 30;
   private $userAgent = 'XHttpClient/1.2.0';

   public function get($url, $headers = array())
   {
      return $this->_send('GET', $url, null, $headers);
   }

   public function post($url, $data, $headers = array())
   {
      return $this->_send('POST', $url, $data, $headers);
   }

   public function put($url, $data, $headers = array())
   {
      return $this->_send('PUT', $url, $data, $headers);
   }

   public function delete($url, $headers = array())
   {
      return $this->_send('DELETE', $url, null, $headers);
   }

   public function setTimeout($seconds)
   {
      $this->timeout = $seconds;
   }

   public function setHeaders($headers)
   {
      $this->headers = $headers;
   }

   public function sendRequest($method, $url, $data = null, $headers = array())
   {
      return $this->_send($method, $url, $data, $headers);
   }

   private function _send($method, $url, $data = null, $headers = array())
   {
      $ch = curl_init();
      $headers = array_merge($this->headers, $headers);

      if (!isset($headers['User-Agent'])) {
         $headers['User-Agent'] = $this->userAgent;
      }

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->formatHeaders($headers));
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      if ($method === 'POST') {
         curl_setopt($ch, CURLOPT_POST, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      } elseif (in_array($method, array('PUT', 'DELETE'))) {
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
         if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
         }
      }

      $response = curl_exec($ch);

      if ($response === false) {
         $error = curl_error($ch);
         $errno = curl_errno($ch);
         curl_close($ch);

         $e = new HttpClientException("cURL request failed: {$error}", $errno);
         $e->setContext(array(
            'url' => $url,
            'error' => $error,
            'errno' => $errno,
            'request_method' => $method
         ));
         throw $e;
      }

      $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
      $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      return array(
         'status' => $statusCode,
         'headers' => $this->parseHeaders(substr($response, 0, $headerSize)),
         'body' => substr($response, $headerSize)
      );
   }

   private function formatHeaders($headers)
   {
      $formatted = array();
      foreach ($headers as $key => $value) {
         $formatted[] = "$key: $value";
      }
      return $formatted;
   }

   private function parseHeaders($headerString)
   {
      $headers = array();
      foreach (explode("\r\n", $headerString) as $line) {
         if (strpos($line, ':') !== false) {
            list($key, $value) = explode(':', $line, 2);
            $headers[trim($key)] = trim($value);
         }
      }
      return $headers;
   }
}
