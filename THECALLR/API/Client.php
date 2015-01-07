<?php

namespace THECALLR\API;

class Client {
	private $_auth;
	private $_url = "https://api.thecallr.com";
	private $_headers = [];

	public function setURL($url) {
		$this->_url = $url;
	}

	public function setAuthCredentials($username, $password) {
		$this->_auth = "{$username}:{$password}";
	}

	public function setCustomHeaders(array $headers) {
		$this->_headers = $headers;
	}

	/*
	 * Call any method
	 */
	public function call($method, $params = []) {
		if (!is_string($method)) {
			throw new \THECALLR\API\Exception\LocalException('METHOD_TYPE_ERROR');
		}
		if (!is_array($params)) {
			throw new \THECALLR\API\Exception\LocalException('PARAMS_TYPE_ERROR');
		}
		$request = new Request();
		$request->method = $method;
		$request->params = $params;
		$response = $request->send($this->_url, $this->_auth, $this->_headers);
		if ($response->isError()) {
			throw $response->error;
		}
		return $response->result;
	}
}

class Request {
	public $id = 42;
	public $jsonrpc = '2.0';
	public $method;
	public $params = [];

	public function send($url, $auth = null, $headers = []) {
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
		if ($data === FALSE) {
			throw new \THECALLR\API\Exception\LocalException('CURL_ERROR: '.curl_error($c), curl_errno($c));
		}
		curl_close($c);
		return new Response($data);
	}
}

class Response {
	public $jsonrpc;
	public $id;
	public $result;
	public $error;

	function __construct($data) {
		$data = json_decode($data);
		if ($data === FALSE) {
			throw new \THECALLR\API\Exception\LocalException('JSON_DECODE_ERROR', json_last_error());
		}
		/* validation */
		if (!is_object($data)) {
			throw new \THECALLR\API\Exception\LocalException('RESPONSE_NOT_AN_OBJECT');
		}
		if (!property_exists($data, 'id') || !property_exists($data,'jsonrpc') ||
			(!property_exists($data, 'result') && !property_exists($data, 'error'))) {
			throw new \THECALLR\API\Exception\LocalException('RESPONSE_MISSING_PROPERTY');
		}
		/* response */
		if (property_exists($data, 'error')) {
			$this->error = new \THECALLR\API\Exception\RemoteException($data->error);
		} else {
			$this->result = $data->result;
		}
		$this->id = $data->id;
		$this->jsonrpc = $data->jsonrpc;
	}

	public function isError() {
		return $this->error !== null;
	}
}