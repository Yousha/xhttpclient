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
   private $userAgent = 'PHP-HttpClient/1.0';

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
      $parsed = parse_url($url);

      if (!$parsed || !isset($parsed['host'])) {
         $e = new HttpClientException("Invalid URL", 0);
         $e->setContext(array(
            'url' => $url,
            'request_method' => $method
         ));
         throw $e;
      }

      $host = $parsed['host'];
      $port = isset($parsed['port']) ? $parsed['port'] : ($parsed['scheme'] === 'https' ? 443 : 80);
      $path = isset($parsed['path']) ? $parsed['path'] : '/';
      $query = isset($parsed['query']) ? $parsed['query'] : '';

      if ($method === 'GET' && $data !== null) {
         $query .= (empty($query) ? '' : '&') . http_build_query($data);
      }

      $path .= (empty($query) ? '' : "?$query");

      $scheme = ($parsed['scheme'] === 'https') ? 'ssl://' : '';
      $fp = @fsockopen($scheme . $host, $port, $errno, $errstr, $this->timeout);

      if (!$fp) {
         $e = new HttpClientException("Socket connection failed: $errstr", $errno);
         $e->setContext(array(
            'url' => $url,
            'host' => $host,
            'port' => $port,
            'request_method' => $method
         ));
         throw $e;
      }

      $headers = array_merge($this->headers, $headers);

      if (!isset($headers['User-Agent'])) {
         $headers['User-Agent'] = $this->userAgent;
      }

      $request = "$method $path HTTP/1.1\r\n";
      $request .= "Host: $host\r\n";
      $request .= "Connection: close\r\n";

      foreach ($headers as $key => $value) {
         $request .= "$key: $value\r\n";
      }

      if ($method === 'POST') {
         $postData = is_array($data) ? http_build_query($data) : $data;
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

      list($headerString, $body) = explode("\r\n\r\n", $response, 2);

      $statusCode = $this->parseStatusCode($headerString);
      $headers = $this->parseHeaders($headerString);

      if (isset($headers['Transfer-Encoding']) && strtolower($headers['Transfer-Encoding']) === 'chunked') {
         $body = $this->decodeChunkedBody($body);
      }

      return array(
         'status' => $statusCode,
         'headers' => $headers,
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

   private function decodeChunkedBody($body)
   {
      $decoded = '';
      $pos = 0;

      while ($pos < strlen($body)) {
         $hex = '';
         while (true) {
            $c = substr($body, $pos++, 1);
            if ($c === '' || $c === "\r") continue;
            if ($c === "\n") break;
            $hex .= $c;
         }

         $length = hexdec($hex);
         if ($length === 0) break;

         $decoded .= substr($body, $pos, $length);
         $pos += $length;

         while ($pos < strlen($body) && (substr($body, $pos, 1) === "\r" || substr($body, $pos, 1) === "\n")) {
            $pos++;
         }
      }

      return $decoded;
   }
}
