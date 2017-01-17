<?php
/**
 * List your Voice Apps (CLI)
 */

require 'vendor/autoload.php';

if (count($argv) < 3) {
    die("{$argv[0]} api_login api_password\n");
}

$api = new CALLR\API\Client;
$api->setAuth(new CALLR\API\Authentication\LoginPasswordAuth($argv[1], $argv[2]));

$result = $api->call('apps.get_list', [true]);
var_dump($result);
