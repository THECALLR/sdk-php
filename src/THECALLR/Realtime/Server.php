<?php

namespace THECALLR\Realtime;

/**
 * @uses \THECALLR\Realtime\Request
 * @uses \THECALLR\Realtime\Response
 * @uses \THECALLR\Realtime\CallFlow
 * @author Florent CHAUVEAU <fc@thecallr.com>
 * @see http://thecallr.com/docs/real-time/
 */
class Server {
	/** sent as http header Content-Type */
	const CONTENT_TYPE = 'application/json';

	/** @var \THECALLR\Realtime\CallFlow[] $_cf Registered call flows */
	private $_cf;
	/** @var callable|null $_rawInputHandler Raw input callback */
	private $_rawInputHandler;
	/** @var callable|null $_rawOutputHandler Raw output callback */
	private $_rawOutputHandler;

	/**
	 * Registers a raw input callback
	 * @param callable $callback
	 */
	public function setRawInputHandler(callable $callback) {
		$this->_rawInputHandler = $callback;
	}

	/**
	 * Registers a raw output callback
	 * @param callable $callback
	 */
	public function setRawOutputHandler(callable $callback) {
		$this->_rawOutputHandler = $callback;
	}

	/**
	 * Registers a CallFlow in the server
	 * @param string $hash Hash of a REALTIME10 app to attach to the CallFlow. You can use '*' to match against anything (fallback)
	 * @param \THECALLR\Realtime\CallFlow $callFlow Call Flow to execute when this $hash is received
	 */
	public function registerCallFlow($hash, CallFlow $cf) {
		$this->_cf[$hash] = $cf;
	}

	/**
	 * Starts the server.
	 */
	public function start() {
		try {
			/* raw input handler */
			$raw = file_get_contents(php_sapi_name() === 'cli' ? 'php://stdin' : 'php://input');
			if ($this->_rawInputHandler !== null) {
				call_user_func($this->_rawInputHandler, $raw);
			}
			/* parse the incoming request */
			$request = new Request($raw);
			/* find the CallFlow */
			if (array_key_exists($request->app, $this->_cf)) {
				$cf = $this->_cf[$request->app];
			} elseif (array_key_exists('*', $this->_cf)) {
				$cf = $this->_cf['*'];
			} else {
				throw new \Exception('CallFlow Not Found', 404);
			}
			/* save request variables into $cf */
			$cf->variables = $request->variables;
			/* call the previous callback */
			$label = $cf->callback($request);
			/* make sure the result is a string */
			if (!is_string($label)) {
				throw new \Exception('Missing Execute Label');
			}
			/* call the next label */
			$response = $cf->execute($label);
			/* output response */
			$output = $response->getJSON();
		} catch (\Exception $e) {
			/* if an exception is thrown, we reply with an HTTP error */
			$output = $this->_error($e->getMessage(), $e->getCode());
		}
		if ($this->_rawOutputHandler !== null) {
			call_user_func($this->_rawOutputHandler, $output);
		}
		header('Content-Type: '.self::CONTENT_TYPE);
		echo $output;
	}

	/**
	 * @internal
	 * Returns an HTTP error. Invoked when an Exception is thrown.
	 * @param string $message HTTP message
	 * @param int $code HTTP code
	 */
	private function _error($message = 'Real-time Server Error', $code = 500) {
		/* make sure code is int >= 400 */
		if (!is_int($code) || $code < 400) $code = 500;
		/* HTTP header */
		header("HTTP/1.1 {$code} {$message}", true, $code);
		/* Output error (for debugging purposes) */
		return "{\"error\": {\"code\": {$code}, \"message\": \"{$message}\"}}";
	}
}
