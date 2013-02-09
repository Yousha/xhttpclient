--TEST--
Test SocketDriver GET request
--SKIPIF--
<?php
require_once 'Drivers/SocketDriver.php';
?>
--FILE--
<?php
require_once 'Drivers/SocketDriver.php';

$driver = new SocketDriver();
$driver->setTimeout(10);
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
