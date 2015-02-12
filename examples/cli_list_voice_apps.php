<?php
/**
 * List your Voice Apps (CLI)
 */

require 'vendor/autoload.php';

if (count($argv) < 3) {
    die("{$argv[0]} api_login api_password\n");
}

$login      = $argv[1];
$password   = $argv[2];

$api = new \THECALLR\API\Client;
$api->setAuthCredentials($login, $password);

$result = $api->call('apps.get_list', [true]);
var_dump($result);
