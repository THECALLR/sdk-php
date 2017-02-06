<?php
/**
 * Send an SMS (CLI)
 */

require 'vendor/autoload.php';

if (count($argv) < 5) {
    die("{$argv[0]} api_login api_password to text\n");
}

$options = new stdClass;
$options->user_data = 'sdktest';

$api = new CALLR\API\Client;
$api->setAuth(new CALLR\API\Authentication\LoginPasswordAuth($argv[1], $argv[2]));

$hash = $api->call('sms.send', ['SMS', $argv[3], $argv[4], $options]);
var_dump($hash);
