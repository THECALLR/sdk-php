<?php

/** If using Composer
require 'vendor/autoload.php';
*/

require '../Realtime/App.php';
require '../Realtime/Server.php';
require '../Realtime/Request.php';
require '../Realtime/Response.php';

$app = new \THECALLR\Realtime\App;

$app->newInboundCall(function(\THECALLR\Realtime\Request $request) {
	// your code
	return 'ask_age';
});

$app->newOutboundCall(function(\THECALLR\Realtime\Request $request) {
	// your code
	return 'ask_age';
});

$app->define('ask_age', 'read', ['media_id'   => 'TTS|TTS_FR-FR_AUDREY|Quel est votre age?',
	          		   			 'max_digits' => 2], function($result, $error, \THECALLR\Realtime\Request $request) use ($app) {
	// save age to db?
	$app->variables->age = $result;
	$app->variables->timeout = 10;
	return 'say_age';
});

$app->define('say_age', 'play', ['media_id' => 'TTS_FR-FR_AUDREY|Votre age est {age} {age}', 'timeout' => '{timeout}'], function($result, $error, \THECALLR\Realtime\Request $request) {
	return '_hangup'; // special label to hangup
});

$rt = new \THECALLR\Realtime\Server;
$rt->registerApp('*', $app); // any hash!
$rt->registerApp('DEADBEEF', $app);
$rt->registerApp('THECALLR', $app);
$rt->start();
