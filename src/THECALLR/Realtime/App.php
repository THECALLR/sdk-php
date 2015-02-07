<?php

namespace THECALLR\Realtime;

/**
 * @uses \THECALLR\Realtime\Request
 * @uses \THECALLR\Realtime\Response
 * @author Florent CHAUVEAU <fc@thecallr.com>
 */
class App {
	/** $var $_cid To generate command ids */
	private $_cid = 0;
	/** @var array[] $_labels Defined labels */
	private $_labels = [];
	/** @var object $variables Variables */
	public $variables;

	/**
	 * Define a handler for inbound calls
	 * @param callable $callback Callback
	 */
	public function newInboundCall(callable $callback) {
		$this->_labels['_start_inbound'] = ['callback' => $callback, 'id' => $this->_cid++];
	}

	/**
	 * Define a handler for outbound calls
	 * @param callable $callback Callback
	 */
	public function newOutboundCall(callable $callback) {
		$this->_labels['_start_outbound'] = ['callback' => $callback, 'id' => $this->_cid++];
	}

	/**
	 * Define a new label (a command to send, and a callback when a response is received)
	 * @param string $label Label name
	 * @param string $command Realtime command
	 * @param mixed[] $params Realtime command params
	 * @param callable $callback Callback, fired when a response is received
	 */
	public function define($label, $command, array $params = [], callable $callback) {
		$this->_labels[$label] = ['command'	 => $command,
								  'params'	 => $params,
								  'callback' => $callback,
								  'id'		 => $this->_cid++];
	}

	/**
	 * @internal
	 * Execute label
	 * @param string $label The label to execute
	 * @return \THECALLR\Realtime\Response Realtime response
	 */
	public function execute($labelKey) {
		/* special label _hangup */
		if ($labelKey === '_hangup') {
			$response = new Response();
			$response->command = 'hangup';
		} else {
			/* do we know this label? */
			if (!array_key_exists($labelKey, $this->_labels)) {
				throw new \Exception('Label Not Found', 404);
			}
			$label =& $this->_labels[$labelKey];
			/* craft a new Response */
			$response = new Response($label['id']);
			$response->command = $label['command'];
			$response->params = $label['params'];
			/* check $this->variables type (may be altered by previous callback) */
			if (!is_object($this->variables)) $this->variables = new stdClass;
			/* replace {var} with $variables in params value */
			foreach ($response->params as $key => &$value) {
				foreach ($this->variables as $varname => $varvalue) {
					$value = str_replace('{'.$varname.'}', $varvalue, $value);
				}
			}
			/* set variables */
			$response->variables = $this->variables;
			/* save current label */
			$response->variables->_label = $labelKey;
		}
		return $response;
	}

	/**
	 * @internal
	 * @param \THECALLR\Realtime\Request $request The incoming request
	 * @return string The label to execute
	 */
	public function callback(Request $request) {
		/* do we have a previous label? */
		if (property_exists($request->variables, '_label')) {
			/* previous label */
			$label = $request->variables->_label;
		} else {
			/* inbound or outbound call? */
			$label = $request->request_hash === null ? '_start_inbound' : '_start_outbound';
		}
		/* do we know this label? */
		if (!array_key_exists($label, $this->_labels)) {
			throw new \Exception('Label Not Found', 404);
		}
		/* callback */
		$callback = $this->_labels[$label]['callback'];
		/* special handling for _start_* */
		if ($label === '_start_inbound' || $label === '_start_outbound') {
			return call_user_func($callback, $request);
		}
		/* error/result parsing */
		$error = $result = null;
		if ($request->command_error) {
			$error = $request->command_error;
		} else {
			$result = $request->command_result;
		}
		/* execute callback */
		return call_user_func($callback, $result, $error, $request);
	}
}
