<?php

if (!defined('SOCKETDRIVER_PHP')) {
   define('SOCKETDRIVER_PHP', true);
} else {
   return;
}

/**
 * Socket based driver.
 */
final class SocketDriver implements DriverInterface
{
   private $headers = array();
   private $timeout = 30;

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
   }

   public function setHeaders($headers)
   {
      $this->headers = $headers;
   }

   private function sendRequest($method, $url, $data = null, $headers = array())
   {
      $parsed = parse_url($url);
      $host = $parsed['host'];
      $port = isset($parsed['port']) ? $parsed['port'] : ($parsed['scheme'] === 'https' ? 443 : 80);
      $path = isset($parsed['path']) ? $parsed['path'] : '/';
      $query = isset($parsed['query']) ? $parsed['query'] : '';

      if ($method === 'GET' && !empty($data)) {
         $query .= (empty($query) ? '' : '&') . http_build_query($data);
      }

      $path .= (empty($query) ? '' : "?{$query}");

      $scheme = ($parsed['scheme'] === 'https') ? 'ssl://' : '';
      $fp = fsockopen($scheme . $host, $port, $errno, $errstr, $this->timeout);

      if (!$fp) {
         throw new HttpClientException("Socket error: {$errstr}", $errno);
      }

      $headers = array_merge($this->headers, $headers);
      $request = "$method $path HTTP/1.1\r\n";
      $request .= "Host: $host\r\n";
      $request .= "Connection: Close\r\n";

      foreach ($headers as $key => $value) {
         $request .= "$key: $value\r\n";
      }

      if ($method === 'POST') {
         $postData = http_build_query($data);
         $request .= "Content-Type: application/x-www-form-urlencoded\r\n";
         $request .= "Content-Length: " . strlen($postData) . "\r\n\r\n";
         $request .= $postData;
      } else {
         $request .= "\r\n";
      }

      fwrite($fp, $request);
      $response = '';

      while (!feof($fp)) {
         $response .= fread($fp, 4096);
      }

      fclose($fp);
      list($headers, $body) = explode("\r\n\r\n", $response, 2);
      $statusCode = $this->parseStatusCode($headers);
      return array(
         'status' => $statusCode,
         'headers' => $this->parseHeaders($headers),
         'body' => $body
      );
   }

   private function parseStatusCode($headerString)
   {
      preg_match('/HTTP\/1\.1 (\d+)/', $headerString, $matches);
      return isset($matches[1]) ? (int)$matches[1] : 500;
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
