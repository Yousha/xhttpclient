X HTTP Client (PHP 5.0)
==============================

A self-contained HTTP client for PHP 5.0 that supports cURL and Socket drivers.

Usage
===============

- Download all files into your project directory, then:

require_once 'HttpClient.php';

// Create client with CURL driver.
$client = new HttpClient('curl');

// Or with Socket driver.
$client = new HttpClient('socket');

// Set timeout (seconds).
$client->setTimeout(10);

// Set default headers.
$client->setHeaders(array(
    'User-Agent' => 'MyApp/1.0',
    'Accept' => 'application/json'
));

// GET request.
$response = $client->get('http://example.com/api');

// POST request.
$response = $client->post('http://example.com/api', array(
    'param1' => 'value1',
    'param2' => 'value2'
));

Requirements
===============

- PHP 5.0
- PHP cURL extension OR PHP socket extension.

License
===============

Use freely!
