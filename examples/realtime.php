<?php
/**
 * Simple Real-time example
 * @see http://thecallr.com/docs/real-time/
 */

/* Composer */
require 'vendor/autoload.php';

/* Not using Composer? Uncomment below */
// require '../src/CALLR/Realtime/Server.php';
// require '../src/CALLR/Realtime/Request.php';
// require '../src/CALLR/Realtime/Response.php';
// require '../src/CALLR/Realtime/CallFlow.php';
// require '../src/CALLR/Realtime/Command.php';
// require '../src/CALLR/Realtime/Command/Params.php';
// require '../src/CALLR/Realtime/Command/ConferenceParams.php';

/* Classes used here */
use \CALLR\Realtime\Server;
use \CALLR\Realtime\Command;
use \CALLR\Realtime\CallFlow;
use \CALLR\Realtime\Command\ConferenceParams;

/* Recommended */
date_default_timezone_set('UTC');
set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) {
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

/* Create a new call flow */
$flow = new CallFlow;

/* When a call is inbound (a DID is being called),
   this callback will be called */
$flow->onInboundCall(function (CallFlow $flow) {
    // your code

    /* your callback **MUST** return a label to execute */
    return 'ask_age';
});

/* When an outbound call is answered,
   this callback will be called */
$flow->onOutboundCall(function (CallFlow $flow) {
    // your code

    /* label to execute */
    return 'ask_age';
});

/* When an call is hung up,
   this callback will be called */
$flow->onHangup(function (CallFlow $flow) {
    // your code
});

/* Define a label with a command and its parameters,
   along with the **async** result callback */
$flow->define(
    'ask_age',
    function (CallFlow $flow) {
        return Command::read('TTS|TTS_EN-GB_SERENA|Hello there. How old are you?', 3, 2, 5000);
    },
    function ($result, $error, CallFlow $flow) {
        // your code

        /* if the 'read' command succeeds, the result will be in $result, and $error will be null.
           if it fails, the error will be in $error, and result will be null */

        /* we can check if the call is hang up */
        if (!$flow->isHangup()) {
            /* here we store some variables in the call
               they can be used in subsequent labels */
            $flow->setVariable('age', $result);
            /* label to execute */
            return 'say_age';
        }
    }
);

/* This label is using the $age variable store above */
$flow->define(
    'say_age',
    function (CallFlow $flow) {
        return Command::play("TTS|TTS_EN-GB_SERENA|You are {$flow->getVariable('age')} years old.");
    },
    function ($result, $error, CallFlow $flow) {
        /* '_hangup' is a special label to hangup */
        return $flow->getVariable('age') >= 18 ? 'conference' : '_hangup';
    }
);

$flow->define(
    'conference',
    function (CallFlow $flow) {
        /* conference params */
        $params = new ConferenceParams;
        $params->autoLeaveWhenAlone = true;
        /* create a conference room based on your age */
        return Command::conference($flow->getVariable('age'), $params);
    },
    function ($result, $error, CallFlow $flow) {
        /* '_hangup' is a special label to hangup */
        return '_hangup';
    }
);

/* Real-time Server */
$server = new Server;

/* Register a callback to receive raw input. Useful for debugging. */
$server->setRawInputHandler(function ($data) {
    $data = date('c').' <<<< '.$data."\n";
    file_put_contents('/tmp/RT_DEBUG', $data, FILE_APPEND);
});

/* Register a callback to receive raw output. Useful for debugging. */
$server->setRawOutputHandler(function ($data) {
    $data = date('c').' >>>> '.$data."\n";
    file_put_contents('/tmp/RT_DEBUG', $data, FILE_APPEND);
});

/* Match your call flow against a REALTIME10 app hash */
//$server->registerCallFlow('DEADBEEF', $flow);

/* Or any hash! */
$server->registerCallFlow('*', $flow);

/* Start */
$server->start();
