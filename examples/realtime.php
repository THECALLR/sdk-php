<?php
/**
 * Simple Real-time example
 * @see http://thecallr.com/docs/real-time/
 */

/* Composer */
// require 'vendor/autoload.php';

/* Not using Composer? Uncomment below */
require '../src/THECALLR/Realtime/Server.php';
require '../src/THECALLR/Realtime/Request.php';
require '../src/THECALLR/Realtime/Response.php';
require '../src/THECALLR/Realtime/CallFlow.php';
require '../src/THECALLR/Realtime/Command.php';

/* Classes used here */
use \THECALLR\Realtime\Server;
use \THECALLR\Realtime\Request;
use \THECALLR\Realtime\Command;
use \THECALLR\Realtime\CallFlow;

/* Recommended */
date_default_timezone_set('UTC');
set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) {
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

/* Create a new call flow */
$flow = new CallFlow;

/* When a call is inbound (a DID is being called),
   this callback will be called */
$flow->onInboundCall(function (Request $request) {
    // your code

    /* your callback **MUST** return a label to execute */
    return 'ask_age';
});

/* When an outbound call is answered,
   this callback will be called */
$flow->onOutboundCall(function (Request $request) {
    // your code

    /* label to execute */
    return 'ask_age';
});

/* Define a label with a command and its parameters,
   along with the **async** result callback */
$flow->define(
    'ask_age',
    Command::read('TTS|TTS_EN-GB_SERENA|Hello there. How old are you?', 3, 2, 5000),
    function ($result, $error, Request $request) use ($flow) {
        // your code

        /* if the 'read' command succeeds, the result will be in $result, and $error will be null.
           if it fails, the error will be in $error, and result will be null */

        /* here we store some variables in the call
           they can be used in subsequent labels */
        $flow->variables->result = $result;
        $flow->variables->error = $error;
        $flow->variables->age = $result;
        /* label to execute */
        return 'say_age';
    }
);

/* This label is using the $age variable store above */
$flow->define(
    'say_age',
    Command::play('TTS|TTS_EN-GB_SERENA|You are {age} years old.'),
    function ($result, $error, Request $request) {
        /* special label to hangup */
        return '_hangup';
    }
);

/* Real-time Server */
$server = new Server;

/* Register a callback to receive raw input. Useful for debugging. */
$server->setRawInputHandler(function ($data) {
    $data = date('c').' <<<< '.$data."\n";
    // file_put_contents('/tmp/RT_DEBUG', $data, FILE_APPEND);
});

/* Register a callback to receive raw output. Useful for debugging. */
$server->setRawOutputHandler(function ($data) {
    $data = date('c').' >>>> '.$data."\n";
    // file_put_contents('/tmp/RT_DEBUG', $data, FILE_APPEND);
});

/* Match your call flow against a REALTIME10 app hash */
//$server->registerCallFlow('DEADBEEF', $flow);

/* Or any hash! */
$server->registerCallFlow('*', $flow);

/* Start */
$server->start();
