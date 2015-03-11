--TEST--
Test CurlDriver GET request
--SKIPIF--
<?php
if (!extension_loaded('curl')) die('skip curl extension not available');
require_once 'Drivers/CurlDriver.php';
?>
--FILE--
<?php
require_once 'Drivers/CurlDriver.php';

$driver = new CurlDriver();
$response = $driver->get('http://example.com');

var_dump(isset($response['status']));
var_dump(isset($response['headers']));
var_dump(isset($response['body']));
var_dump($response['status'] === 200);
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
