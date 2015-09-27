<?php

if (!defined('COOKIEJAR_PHP')) {
   define('COOKIEJAR_PHP', true);
} else {
   return;
}

/**
 * Simple Cookie jar to store and send cookies across requests.
 */
final class CookieJar
{
   /**
    * @var array Associative array of cookies: [domain][path][name] = value
    */
   private $cookies = array();

   /**
    * Parses raw Set-Cookie headers and stores them.
    *
    * @param string $host The domain from which the cookie was set
    * @param string $path The path associated with the cookie
    * @param array  $headers Array of 'Set-Cookie' header lines
    */
   public function addFromHeaders($host, $path, $headers)
   {
      foreach ($headers as $header) {
         if (strtolower(substr($header, 0, 11)) === 'set-cookie:') {
            $cookieLine = trim(substr($header, 11));
            $parts = explode(';', $cookieLine);

            if (empty($parts[0])) continue;

            list($name, $value) = explode('=', $parts[0], 2);
            $name = trim($name);
            $value = trim($value);

            // Default domain is current host
            $domain = $host;

            // Default path is current request path
            $cookiePath = $path;

            // Parse attributes
            foreach ($parts as $part) {
               $attr = explode('=', trim($part), 2);
               $key = strtolower($attr[0]);

               if ($key === 'domain' && isset($attr[1])) {
                  $domain = strtolower(trim($attr[1]));
               } elseif ($key === 'path') {
                  $cookiePath = trim($attr[1]);
               }
            }

            // Store cookie by domain and path
            if (!isset($this->cookies[$domain])) {
               $this->cookies[$domain] = array();
            }

            if (!isset($this->cookies[$domain][$cookiePath])) {
               $this->cookies[$domain][$cookiePath] = array();
            }

            $this->cookies[$domain][$cookiePath][$name] = urldecode($value);
         }
      }
   }

   /**
    * Returns applicable cookies for a given request URL.
    *
    * @param string $url
    * @return array Cookies to include in the request
    */
   public function getCookiesForUrl($url)
   {
      $parsed = parse_url($url);
      if (!$parsed || !isset($parsed['host'])) return array();

      $host = strtolower($parsed['host']);
      $path = isset($parsed['path']) ? $parsed['path'] : '/';

      $cookies = array();

      foreach ($this->cookies as $domain => $paths) {
         if ($this->matchesDomain($host, $domain)) {
            foreach ($paths as $cookiePath => $cookieData) {
               if ($this->matchesPath($path, $cookiePath)) {
                  foreach ($cookieData as $name => $value) {
                     $cookies[$name] = $value;
                  }
               }
            }
         }
      }

      return $cookies;
   }

   /**
    * Domain match logic (including subdomains).
    */
   private function matchesDomain($requestHost, $cookieDomain)
   {
      // Exact or subdomain match
      return $requestHost === $cookieDomain ||
         substr($requestHost, -strlen($cookieDomain) - 1) === '.' . $cookieDomain;
   }

   /**
    * Path match logic (prefix match).
    */
   private function matchesPath($requestPath, $cookiePath)
   {
      return strpos($requestPath . '/', $cookiePath . '/') === 0;
   }

   /**
    * Clears all stored cookies.
    */
   public function clear()
   {
      $this->cookies = array();
   }
}
