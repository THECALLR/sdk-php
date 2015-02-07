<?php
/**
 * Simple realtime example
 */

/* Composer */
require 'vendor/autoload.php';

/* Classes used here */
use \THECALLR\Realtime\App;
use \THECALLR\Realtime\Request;
use \THECALLR\Realtime\Server;

/* This will represent the call flow */
// TODO: rename flow?
$app = new App;

/* When a call is inbound (a DID is being called),
   this callback will be called */
$app->newInboundCall(function(Request $request) {
	// your code

	/* your callback **MUST** return a label to execute */
	return 'ask_age';
});

/* When an outbound call is answered,
   this callback will be called */
$app->newOutboundCall(function(Request $request) {
	// your code

	/* label to execute */
	return 'ask_age';
});

/* Define a label with a command and its parameters,
   along with the **async** result callback */
$app->define('ask_age', 'read', ['media_id'   => 'TTS|TTS-EN-GB_SERENA|Hello there. How old are you?',
	          		   			 'max_digits' => 3,
	          		   			 'attempts'   => 3,
	          		   			 'timeout_ms' => 5000], function($result, $error, Request $request) use ($app) {
	// your code

    /* if the 'read' command succeeds, the result will be in $result, and $error will be null.
       if it fails, the error will be in $error, and result will be null */

    /* here we store some variables in the call
       they can be used in subsequent labels */
	$app->variables->result = $result;
	$app->variables->error = $error;
	$app->variables->age = $result;
	/* label to execute */
	return 'say_age';
});

/* This label is using the $age variable store above */
$app->define('say_age', 'play', ['media_id' => 'TTS|TTS-EN-GB_SERENA|You are {age} years old.'], function($result, $error, Request $request) {
	/* special label to hangup */
	return '_hangup';
});

/* Realtime Server */
$rt = new Server;
/* Match your app against a hash (a REALTIME10 App hash) */
//$rt->registerApp('DEADBEEF', $app);
/* Or any hash! */
$rt->registerApp('*', $app);
/* Start */
$rt->start();
