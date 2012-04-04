<?php

require_once 'source/HttpClient.php';

// GET using CURL driver.
$client = new HttpClient('curl');
$client->setHeaders(array('User-Agent' => 'MyCustomAgent/1.0'));
$response = $client->get('https://yahoo.com');
print_r($response);

// POST using Socket driver.
$client = new HttpClient('socket');
$response = $client->post('https://yahoo.com/', array('name' => 'Test'));
print_r($response);

// PUT Request.
$response = $client->put('https://api.yahoo.com/resource/1 ', array(
   'name' => 'Updated Name'
), array(
   'Content-Type' => 'application/json'
));
print_r($response);

// DELETE Request.
$response = $client->delete('https://api.yahoo.com/resource/1 ', array(
   'Authorization' => 'Bearer token123'
));
print_r($response);
