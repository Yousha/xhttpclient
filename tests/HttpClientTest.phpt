--TEST--
Test HttpClient with both drivers
--SKIPIF--
<?php
if (!extension_loaded('curl')) die('skip curl extension not available');
require_once 'HttpClient.php';
?>
--FILE--
<?php
require_once 'HttpClient.php';

// Test CURL driver
$client = new HttpClient('curl');
$curlResponse = $client->get('http://example.com');

// Test Socket driver
$client = new HttpClient('socket');
$socketResponse = $client->get('http://example.com');

var_dump($curlResponse['status'] === 200);
var_dump($socketResponse['status'] === 200);
var_dump(!empty($curlResponse['body']));
var_dump(!empty($socketResponse['body']));
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
