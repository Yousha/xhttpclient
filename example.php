<?php

require_once 'source/HttpClient.php';
require_once 'source/HttpCode.php';

$options = array(
   'timeout' => 10,
   'default_headers' => array(
      'User-Agent' => 'MyCustomAgent/2.0',
      'Accept' => 'application/json'
   )
);

try {
   // GET using CURL driver.
   $client = new HttpClient('curl', $options);
   $response = $client->get('https://yahoo.com');
   echo "GET Response (Status: {$response['status']}):\n";
   print_r($response);
   echo "\n";

   $response = $client->get('https://yahoo.com');
   $statusText = HttpCode::getMessage($response['status']);
   echo "GET Response (Status: {$response['status']} - $statusText):\n";

   // POST using Socket driver.
   $client = new HttpClient('socket', $options);
   $response = $client->post('https://yahoo.com', array('name' => 'Test'));
   echo "POST Response (Status: {$response['status']}):\n";
   print_r($response);
   echo "\n";

   // PUT Request.
   $response = $client->put('https://api.yahoo.com/resource/1', array(
      'name' => 'Updated Name'
   ), array(
      'Content-Type' => 'application/json'
   ));
   echo "PUT Response (Status: {$response['status']}):\n";
   print_r($response);
   echo "\n";

   // DELETE Request.
   $response = $client->delete('https://api.yahoo.com/resource/1', array(
      'Authorization' => 'Bearer token123'
   ));
   echo "DELETE Response (Status: {$response['status']}):\n";
   print_r($response);
   echo "\n";
} catch (HttpClientException $e) {
   echo "HttpClientException caught:\n";
   echo "Message: " . $e->getMessage() . "\n";
   echo "Code: " . $e->getCode() . "\n";
   echo "Context:\n";
   print_r($e->getContext());
   echo "\n";
   echo "Full Trace:\n" . $e . "\n";
}
