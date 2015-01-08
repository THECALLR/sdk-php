<?php

namespace THECALLR\API;

/**
 * JSON-RPC 2.0 Request
 */
class Request {
	public $id = 0;
	public $jsonrpc = '2.0';
	public $method;
	public $params = [];

	/**
	 * Send the request!
	 * @param string $url Endpoint URL
	 * @param string $auth Endpoint HTTP Basic Authentication
	 * @param array $headers HTTP headers (cURL format)
	 * @return \THECALLR\API\Response Response object
	 * @throws \THECALLR\API\Exception\LocalException
	 */
	public function send($url, $auth = null, array $headers = []) {
		/* content type */
		$headers[] = 'Content-Type: application/json-rpc; charset=utf-8';
		/* curl */
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_FAILONERROR, true);
		curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($this));
		curl_setopt($c, CURLOPT_FORBID_REUSE, false);
		if (!empty($auth)) {
			curl_setopt($c, CURLOPT_USERPWD, $auth);
		}
		$data = curl_exec($c);
		/* curl error */
		if ($data === false) {
			throw new Exception\LocalException('CURL_ERROR: '.curl_error($c), curl_errno($c));
		}
		curl_close($c);
		/* response */
		return new Response($data);
	}
}