<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

// load environmental variables
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$http_client = new Client([
    'base_uri' => 'https://api.openai.com',
    'timeout'  => 10.0,
]);

// get the promps from command line input
echo "Enter your prompt: ";
$handle = fopen ("php://stdin","r");
$prompt = trim(fgets($handle)) ?? '';
fclose($handle);
if (!$prompt) die("Please, enter your prompt\n");

// query the API
$response = $http_client->post('/v1/chat/completions', [
    'headers' => [
        'Authorization' => 'Bearer ' . getenv('OPENAI_API_KEY'),
        'Content-Type' => 'application/json',
    ],
    'json' => [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ]
    ],
]);
$body = json_decode((string)$response->getBody(), true);

// output response
if (!isset($body['choices'][0]['message']['content'])) {
    die('Error, while making request. ' . var_export($body, true));
}

echo $body['choices'][0]['message']['content'] . PHP_EOL;
