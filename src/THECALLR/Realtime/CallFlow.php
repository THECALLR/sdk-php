<?php

namespace THECALLR\Realtime;

/**
 * Call Flow (Real-time commands and callbacks)
 * @uses \THECALLR\Realtime\Request
 * @uses \THECALLR\Realtime\Response
 * @author Florent CHAUVEAU <fc@thecallr.com>
 */
class CallFlow
{
    /** @var array[] $labels Defined labels */
    private $labels = [];
    /** @var callable|null onInboundCall */
    private $onInboundHandler = null;
    /** @var callable|null onOutboundCall */
    private $onOutboundHandler = null;
    /** @var callable|null onHangup */
    private $onHangupHandler = null;
    /** @var \THECALLR\Realtime\Request Current Request in the call flow */
    private $currentRequest = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        /* special label to hangup */
        $this->define(
            '_hangup',
            function() {
                return Command::hangup();
            }
        );
    }

    /**
     * Get variable from Call Flow
     * @param  string $key Key
     * @return mixed       Value
     */
    public function getVariable($key)
    {
        $key = (string) $key;
        return $this->hasVariable($key) ? $this->currentRequest->variables->$key : null;
    }

    /**
     * Set variable in Call Flow
     * @param string $key   Key
     * @param mixed  $value Value
     */
    public function setVariable($key, $value)
    {
        $key = (string) $key;
        $this->currentRequest->variables->$key = $value;
        return true;
    }

    /**
     * Has variable
     * @param  string  $key Key
     * @return boolean      Does the variable exist?
     */
    public function hasVariable($key)
    {
        return property_exists($this->currentRequest->variables, $key);
    }

    /**
     * Get current request in the Call Flow
     * @return \THECALLR\Realtime\Request Current Request
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    /**
     * Is the call hangup?
     * @return boolean Call hangup?
     */
    public function isHangup()
    {
        return $this->currentRequest->call_status === 'HANGUP';
    }

    /**
     * Define a handler for inbound calls
     * @param callable $callback Callback
     */
    public function onInboundCall(callable $callback)
    {
        $this->onInboundHandler = $callback;
    }

    /**
     * Define a handler for outbound calls
     * @param callable $callback Callback
     */
    public function onOutboundCall(callable $callback)
    {
        $this->onOutboundHandler = $callback;
    }

    /**
     * Define a handler when the call is hung up
     * The callback is called after calling the command callback
     * @param callable $callback Callback
     */
    public function onHangup(callable $callback)
    {
        $this->onHangupHandler = $callback;
    }

    /**
     * Define a new label (a command to send, and a callback when a response is received)
     * @param string $label Label name
     * @param callable $before Callback that must return a \THECALLR\Realtime\Command
     * @param callable $after Callback invoked when a response a received, must return the next label to execute
     */
    public function define($label, callable $before, callable $after = null)
    {
        $this->labels[$label] = ['before' => $before,
                                 'after'  => $after];
    }

    /**
     * @internal
     * Execute label
     * @param string $label The label to execute
     * @param  \THECALLR\Realtime\Request $request Real-time request
     * @return \THECALLR\Realtime\Response Real-time response
     */
    public function execute($labelKey)
    {
        /* do we know this label? */
        if (!array_key_exists($labelKey, $this->labels)) {
            throw new \Exception("Label '{$labelKey}' Not Found", 404);
        }
        $label =& $this->labels[$labelKey];
        /* get the command */
        $command = call_user_func($label['before'], $this);
        if (!($command instanceof Command)) {
            throw new \Exception("Label '{$labelKey}' before() did not return a Command");
        }
        /* craft a new Response */
        $response = new Response($labelKey);
        $response->command = $command->command;
        $response->params = $command->params;
        $response->variables = $this->currentRequest->variables;
        return $response;
    }

    /**
     * @internal
     * Call the previous command callback
     * @param \THECALLR\Realtime\Request $request The incoming request
     * @return string The label to execute
     */
    public function callback(Request $request)
    {
        $this->currentRequest = $request;
        /* call status */
        if ($request->command_id) {
            /* previous label */
            $label =& $request->command_id;
            /* do we know this label? */
            if (!array_key_exists($label, $this->labels)) {
                throw new \Exception("Label '{$label}' Not Found", 404);
            }
            /* callback */
            $callback = $this->labels[$label]['after'];
            /* no callback? return null */
            if ($callback === null) {
                $this->checkCallStatus();
                return null;
            }
            /* error/result parsing */
            $error = $result = null;
            // TODO: also parse result for some command (play, read...)
            if ($request->command_error) {
                $error = $request->command_error;
            } else {
                $result = $request->command_result;
            }
            /* execute callback */
            $nextLabel = call_user_func($callback, $result, $error, $this);
        } else {
            if ($request->request_hash === null) {
                /* inbound call */
                if ($this->onInboundHandler === null) {
                    throw new \Exception('Missing onInboundCall');
                }
                $callback =& $this->onInboundHandler;
            } else {
                /* outbound call */
                if ($this->onOutboundHandler === null) {
                    throw new \Exception('Missing onOutboundCall');
                }
                $callback =& $this->onOutboundHandler;
            }
            $nextLabel = call_user_func($callback, $this);
        }
        /* nextLabel */
        if (!is_string($nextLabel) || !strlen($nextLabel)) {
            throw new \Exception("Missing Next Label In '{$label}'");
        }
        $this->checkCallStatus($nextLabel);
        return $nextLabel;
    }

    private function checkCallStatus(&$nextLabel = null)
    {
        if ($this->isHangup()) {
            if ($this->onHangupHandler !== null) {
                call_user_func($this->onHangupHandler, $this);
            }
            /* if the call is HANGUP, there is no nextLabel */
            $nextLabel = null;
        }
    }
}
