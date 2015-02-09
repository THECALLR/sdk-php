<?php

namespace THECALLR\Realtime;

/**
 * @uses \THECALLR\Realtime\Request
 * @uses \THECALLR\Realtime\Response
 * @uses \THECALLR\Realtime\CallFlow
 * @author Florent CHAUVEAU <fc@thecallr.com>
 * @see http://thecallr.com/docs/real-time/
 */
class Server
{
    /** sent as http header Content-Type */
    const CONTENT_TYPE = 'application/json';

    /** @private @var \THECALLR\Realtime\CallFlow[] $cf Registered call flows */
    private $cf;
    /** @private @var callable|null $rawInputHandler Raw input callback */
    private $rawInputHandler;
    /** @private @var callable|null $rawOutputHandler Raw output callback */
    private $rawOutputHandler;

    public function __construct()
    {
        /* start output buffering */
        ob_start();
    }

    /**
     * Set a raw input callback
     * @param callable $callback
     */
    public function setRawInputHandler(callable $callback)
    {
        $this->rawInputHandler = $callback;
    }

    /**
     * Set a raw output callback
     * @param callable $callback
     */
    public function setRawOutputHandler(callable $callback)
    {
        $this->rawOutputHandler = $callback;
    }

    /**
     * Registers a CallFlow in the server
     * @param string $hash Hash of a REALTIME10 app to attach to the CallFlow.
     *                     You can use '*' to match against anything (fallback).
     * @param \THECALLR\Realtime\CallFlow $callFlow Call Flow to execute when this $hash is received
     */
    public function registerCallFlow($hash, CallFlow $cf)
    {
        $this->cf[$hash] = $cf;
    }

    /**
     * Starts the server.
     */
    public function start()
    {
        try {
            /* raw input handler */
            $raw = file_get_contents(php_sapi_name() === 'cli' ? 'php://stdin' : 'php://input');
            if ($this->rawInputHandler !== null) {
                call_user_func($this->rawInputHandler, $raw);
            }
            /* parse the incoming request */
            $request = new Request($raw);
            /* find the CallFlow */
            if (array_key_exists($request->app, $this->cf)) {
                $cf = $this->cf[$request->app];
            } elseif (array_key_exists('*', $this->cf)) {
                $cf = $this->cf['*'];
            } else {
                throw new \Exception("CallFlow for '{$request->app}' Not Found", 404);
            }
            /* save request variables into $cf */
            $cf->variables = $request->variables;
            /* call the previous callback */
            $label = $cf->callback($request);
            /* call the next label */
            $response = $cf->execute($label);
            /* output response */
            echo $response->getJSON();
        } catch (\Exception $e) {
            /* if an exception is thrown, we reply with an HTTP error */
            echo $this->httpError($e->getMessage(), $e->getCode());
        }
        $output = ob_get_contents();
        /* raw output handler */
        if ($this->rawOutputHandler !== null) {
            call_user_func($this->rawOutputHandler, $output);
        }
        /* Content-Type */
        header('Content-Type: '.self::CONTENT_TYPE);
        /* flush output buffer */
        ob_end_flush();
    }

    /**
     * @internal
     * Returns an HTTP error. Invoked when an Exception is thrown.
     * @param string $message HTTP message
     * @param int $code HTTP code
     */
    private function httpError($message = 'Real-time Server Error', $code = 500)
    {
        /* make sure code is int >= 400 */
        if (!is_int($code) || $code < 400) {
            $code = 500;
        }
        /* HTTP header */
        header("HTTP/1.1 {$code} {$message}", true, $code);
        /* Output error (for debugging purposes) */
        return "{\"error\": {\"code\": {$code}, \"message\": \"{$message}\"}}";
    }
}
