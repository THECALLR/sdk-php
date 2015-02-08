<?php

namespace THECALLR\API;

/**
 * JSON-RPC 2.0 Client
 * @author Florent CHAUVEAU <fc@thecallr.com>
 */
class Client
{
    private $auth;
    private $url = "https://api.thecallr.com";
    private $headers = [];

    /**
     * Change API endpoint.
     * @param string $url API endpoint
     * @return void
     */
    public function setURL($url)
    {
        $this->url = $url;
    }

    /**
     * Set API credentials (username, password).
     * @param string $username Username
     * @param string $password Password
     * @return void
     */
    public function setAuthCredentials($username, $password)
    {
        $this->auth = "{$username}:{$password}";
    }

    /**
     * Set API token.
     * @param string $token API token
     * @return void
     */
    public function setAuthToken($token)
    {
        $this->auth = "_token:{$token}";
    }

    /**
     * Set customer HTTP headers.
     * @param array $headers HTTP headers (key/value)
     * @return void
     */
    public function setCustomHeaders(array $headers)
    {
        foreach ($headers as $k => &$v) {
            if (strpos($v, ':') === false) {
                $v = $k.': '.$v;
            }
        }
        $this->headers = $headers;
    }

    /**
     * @param string $method JSON-RPC method
     * @param array $params JSON-RPC parameters
     * @return mixed API response
     * @throws \THECALLR\API\Exception\LocalException
     * @throws \THECALLR\API\Exception\RemoteException
     */
    public function call($method, array $params = [], $id = null)
    {
        if (!is_string($method)) {
            throw new Exception\LocalException('METHOD_TYPE_ERROR');
        }
        if (!is_array($params)) {
            throw new Exception\LocalException('PARAMS_TYPE_ERROR');
        }
        if ($id === null) {
            $id = (int) mt_rand(1, 1024);
        }
        if (!is_int($id)) {
            throw new Exception\LocalException('ID_TYPE_ERROR');
        }
        $request = new Request;
        $request->id = $id;
        $request->method = $method;
        $request->params = $params;
        $response = $request->send($this->url, $this->auth, $this->headers);
        if ($response->isError()) {
            throw $response->error;
        }
        return $response->result;
    }
}
