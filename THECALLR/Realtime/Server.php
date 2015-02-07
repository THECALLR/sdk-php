<?php

namespace THECALLR\Realtime;

/**
 * @uses \THECALLR\Realtime\App
 * @uses \THECALLR\Realtime\Request
 * @uses \THECALLR\Realtime\Response
 * @author Florent CHAUVEAU <fc@thecallr.com>
 * @see http://thecallr.com/docs/real-time/
 */
class Server {
	/** @var \THECALLR\Realtime\App[] $_apps Registered apps */
	private $_apps;

	/**
	 * Registers an app in the server
	 * @param string $hash Hash of a REALTIME10 app to register. You can use '*' to match everything (fallback)
	 * @param \THECALLR\Realtime\App $app Realtime App to execute with the hash.
	 */
	public function registerApp($hash, App $app) {
		$this->_apps[$hash] = $app;
	}

	/**
	 * Starts the server.
	 */
	public function start() {
		try {
			/* parse the incoming request */
			$request = new Request();
			/* find the app */
			if (array_key_exists($request->app, $this->_apps)) {
				$app = $this->_apps[$request->app];
			} elseif (array_key_exists('*', $this->_apps)) {
				$app = $this->_apps['*'];
			} else {
				throw new \Exception('App Not Found', 404);
			}
			/* save request variables into $app */
			$app->variables = $request->variables;
			/* call the previous callback */
			$label = $app->callback($request);
			/* make sure the result is a string */
			if (!is_string($label)) {
				throw new \Exception('Missing Execute');
			}
			/* call the next label */
			$response = $app->execute($label);
			/* output response */
			$response->output();
		} catch (\Exception $e) {
			/* if an exception is thrown, we reply with an HTTP error */
			$this->_error($e->getMessage(), $e->getCode());
		}
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
		/* Output error (easier for debugging purposes) */
		echo "{\"error\": {\"code\": {$code}, \"message\": \"{$message}\"}}";
		/* we stop here */
		exit;
	}
}
