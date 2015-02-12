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

$from       = 'THECALLR';

$options = new \THECALLR\Objects\Method\SMSSendOptions;
$options->user_data = 'sdk-test';

$api = new \THECALLR\API\Client;
$api->setAuthCredentials($login, $password);

$hash = $api->call('sms.send', [$from, $to, $text, $options]);
var_dump($hash);
