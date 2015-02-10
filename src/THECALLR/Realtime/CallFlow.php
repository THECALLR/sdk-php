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
    /** @var object $variables Call variables */
    public $variables;

    /**
     * Define a handler for inbound calls
     * @param callable $callback Callback
     */
    public function onInboundCall(callable $callback)
    {
        $this->labels['__inbound_call'] = ['callback' => $callback];
    }

    /**
     * Define a handler for outbound calls
     * @param callable $callback Callback
     */
    public function onOutboundCall(callable $callback)
    {
        $this->labels['__outbound_call'] = ['callback' => $callback];
    }

    public function onHangup(callable $callback)
    {
        $this->labels['__hangup'] = ['callback' => $callback];
    }

    /**
     * Define a new label (a command to send, and a callback when a response is received)
     * @param string $label Label name
     * @param \THECALLR\Realtime\Command $command Realtime command
     * @param callable $callback Callback, fired when a response is received
     */
    public function define($label, Command $command, callable $callback)
    {
        $this->labels[$label] = ['command'  => $command,
                                 'callback' => $callback];
    }

    /**
     * @internal
     * Execute label
     * @param string $label The label to execute
     * @return \THECALLR\Realtime\Response Real-time response
     */
    public function execute($labelKey)
    {
        /* special label '_hangup' */
        if ($labelKey === '_hangup') {
            $response = new Response($labelKey);
            $response->command = 'hangup';
            return $response;
        }
        /* do we know this label? */
        if (!array_key_exists($labelKey, $this->labels)) {
            throw new \Exception("Label '{$labelKey}' Not Found", 404);
        }
        $label =& $this->labels[$labelKey];
        /* craft a new Response */
        $response = new Response($labelKey);
        $response->command = $label['command']->command;
        $response->params = $label['command']->params;
        /* check $this->variables type (may be altered by previous callback) */
        if (!is_object($this->variables)) {
            $this->variables = new \stdClass;
        }
        /* replace {var} with $variables in params value */
        foreach ($response->params as &$value) {
            /* only replace {var} if $value is a string and contains '{' */
            if (is_string($value) && strpos($value, '{') !== false) {
                foreach ($this->variables as $varname => $varvalue) {
                    $value = str_replace('{'.$varname.'}', $varvalue, $value);
                }
            }
        }
        /* set variables */
        $response->variables = $this->variables;
        /* return Response object */
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
        if ($request->call_status === 'HANGUP') {
            // TODO: also call the previous callback first!
            if (array_key_exists('__hangup', $this->labels)) {
                call_user_func($this->labels['__hangup']['callback'], $request);
            }
            return null;
        } elseif ($request->command_id) {
            /* previous label */
            $label =& $request->command_id;
        } else {
            /* inbound or outbound call? */
            $label = $request->request_hash === null ? '__inbound_call' : '__outbound_call';
        }
        /* do we know this label? */
        if (!array_key_exists($label, $this->labels)) {
            throw new \Exception("Label '{$label}' Not Found", 404);
        }
        /* callback */
        $callback = $this->labels[$label]['callback'];
        /* special handling for _start_* */
        if ($label === '__inbound_call' || $label === '__outbound_call') {
            $nextLabel = call_user_func($callback, $request);
            if (!is_string($nextLabel) || !strlen($nextLabel)) {
                throw new \Exception("Missing Next Label In '{$label}'");
            }
            return $nextLabel;
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
        $nextLabel = call_user_func($callback, $result, $error, $request);
        if (!is_string($nextLabel) || !strlen($nextLabel)) {
            throw new \Exception("Missing Next Label In '{$label}'");
        }
        return $nextLabel;
    }
}
