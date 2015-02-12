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
    /** @var object $variables Call variables */
    public $variables;

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
    public function execute($labelKey, Request $request)
    {
        /* do we know this label? */
        if (!array_key_exists($labelKey, $this->labels)) {
            throw new \Exception("Label '{$labelKey}' Not Found", 404);
        }
        $label =& $this->labels[$labelKey];
        /* get the command */
        $command = call_user_func($label['before'], $request, $this);
        if (!($command instanceof Command)) {
            throw new \Exception("Label '{$labelKey}' before() did not return a Command");
        }
        /* craft a new Response */
        $response = new Response($labelKey);
        $response->command = $command->command;
        $response->params = $command->params;
        $response->variables = $this->variables;
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
        /* save request variables into $this */
        $this->variables = $request->variables;
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
            $nextLabel = call_user_func($callback, $result, $error, $request, $this);
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
            $nextLabel = call_user_func($callback, $request, $this);
        }
        /* nextLabel */
        if (!is_string($nextLabel) || !strlen($nextLabel)) {
            throw new \Exception("Missing Next Label In '{$label}'");
        }
        /* HANGUP handling */
        if ($request->call_status === 'HANGUP') {
            if ($this->onHangupHandler !== null) {
                call_user_func($this->onHangupHandler, $request, $this);
            }
            /* if the call is HANGUP, there is no nextLabel */
            $nextLabel = null;
        }
        return $nextLabel;
    }
}
