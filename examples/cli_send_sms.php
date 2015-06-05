<?php
/**
 * Send an SMS (CLI)
 */

require 'vendor/autoload.php';

if (count($argv) < 5) {
    die("{$argv[0]} api_login api_password to text\n");
}

$login      = $argv[1];
$password   = $argv[2];
$to         = $argv[3];
$text       = $argv[4];

$from       = 'CALLR';

$options = new stdClass;
$options->user_data = 'sdktest';

$api = new \CALLR\API\Client;
$api->setAuthCredentials($login, $password);

$hash = $api->call('sms.send', [$from, $to, $text, $options]);
var_dump($hash);
