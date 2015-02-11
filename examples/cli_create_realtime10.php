<?php
/**
 * Create a REALTIME10 Voice App (CLI)
 */

require 'vendor/autoload.php';

if (count($argv) < 4) {
    die("{$argv[0]} api_login api_password realtime_url\n");
}

$login      = $argv[1];
$password   = $argv[2];
$URL        = $argv[3];

$api = new \THECALLR\API\Client;
$api->setAuthCredentials($login, $password);

$app = new \THECALLR\Objects\App\Realtime10($api);
$app->name = 'REALTIME-TEST';
$app->p->url = $URL;

$app->create();
var_dump($app);
