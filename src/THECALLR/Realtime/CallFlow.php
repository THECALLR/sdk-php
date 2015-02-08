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
    /** $var $cid To generate command ids */
    private $cid = 0;
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
        $this->labels['_start_inbound'] = ['callback' => $callback, 'id' => $this->cid++];
    }

    /**
     * Define a handler for outbound calls
     * @param callable $callback Callback
     */
    public function onOutboundCall(callable $callback)
    {
        $this->labels['_start_outbound'] = ['callback' => $callback, 'id' => $this->cid++];
    }

    /**
     * Define a new label (a command to send, and a callback when a response is received)
     * @param string $label Label name
     * @param mixed[] $command Realtime command
     * @param callable $callback Callback, fired when a response is received
     */
    public function define($label, Command $command, callable $callback)
    {
        $this->labels[$label] = ['command'  => $command,
                                 'callback' => $callback,
                                 'id'       => $this->cid++];
    }

    /**
     * @internal
     * Execute label
     * @param string $label The label to execute
     * @return \THECALLR\Realtime\Response Real-time response
     */
    public function execute($labelKey)
    {
        /* special label _hangup */
        if ($labelKey === '_hangup') {
            $response = new Response();
            $response->command = 'hangup';
            return $response;
        }
        /* do we know this label? */
        if (!array_key_exists($labelKey, $this->labels)) {
            throw new \Exception('Label Not Found', 404);
        }
        $label =& $this->labels[$labelKey];
        /* craft a new Response */
        $response = new Response($label['id']);
        $response->command = $label['command']->command;
        $response->params = $label['command']->params;
        /* check $this->variables type (may be altered by previous callback) */
        if (!is_object($this->variables)) {
            $this->variables = new \stdClass;
        }
        /* replace {var} with $variables in params value */
        foreach ($response->params as &$value) {
            foreach ($this->variables as $varname => $varvalue) {
                $value = str_replace('{'.$varname.'}', $varvalue, $value);
            }
        }
        /* set variables */
        $response->variables = $this->variables;
        /* save current label */
        $response->variables->_label = $labelKey;
        return $response;
    }

    /**
     * @internal
     * @param \THECALLR\Realtime\Request $request The incoming request
     * @return string The label to execute
     */
    public function callback(Request $request)
    {
        /* do we have a previous label? */
        if (property_exists($request->variables, '_label')) {
            /* previous label */
            $label = $request->variables->_label;
        } else {
            /* inbound or outbound call? */
            $label = $request->request_hash === null ? '_start_inbound' : '_start_outbound';
        }
        /* do we know this label? */
        if (!array_key_exists($label, $this->labels)) {
            throw new \Exception('Label Not Found', 404);
        }
        /* callback */
        $callback = $this->labels[$label]['callback'];
        /* special handling for _start_* */
        if ($label === '_start_inbound' || $label === '_start_outbound') {
            return call_user_func($callback, $request);
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
        return call_user_func($callback, $result, $error, $request);
    }
}