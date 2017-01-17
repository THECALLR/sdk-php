<?php
/**
 * Create a REALTIME10 Voice App (CLI)
 */

require 'vendor/autoload.php';

if (count($argv) < 4) {
    die("{$argv[0]} api_login api_password realtime_url\n");
}

$api = new CALLR\API\Client;
$api->setAuth(new CALLR\API\Authentication\LoginPasswordAuth($argv[1], $argv[2]));

$app = new CALLR\Objects\App\Realtime10($api);
$app->name = 'REALTIME-TEST';
$app->p->url = $argv[3];

$app->create();
var_dump($app);
