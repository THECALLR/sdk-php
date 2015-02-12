<?php

namespace THECALLR\API;

/**
 * JSON-RPC 2.0 Client
 * @author Florent CHAUVEAU <fc@thecallr.com>
 */
class Client
{
    /** @var string "login:password" */
    private $auth;
    /** @var string API URL */
    private $url = "https://api.thecallr.com";
    /** @var string[] HTTP Headers */
    private $headers = [];

    /**
     * Change API endpoint.
     * @param string $url API endpoint
     */
    public function setURL($url)
    {
        $this->url = $url;
    }

    /**
     * Set API credentials (username, password).
     * @param string $username Username
     * @param string $password Password
     */
    public function setAuthCredentials($username, $password)
    {
        $this->auth = "{$username}:{$password}";
    }

    /**
     * Set API auth token.
     * @param string $token API token
     */
    public function setAuthToken($token)
    {
        $this->auth = "_token:{$token}";
    }

    /**
     * Set custom HTTP headers.
     * @param array $headers {
     *     @var  string $key HTTP header key
     *     @var  string $value HTTP header value
     * }
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
     * @param mixed[] $params JSON-RPC parameters
     * @return mixed API response
     * @throws \THECALLR\API\Exception\LocalException
     * @throws \THECALLR\API\Exception\RemoteException
     */
    public function call($method, array $params = [], $id = null)
    {
        if (!is_string($method)) {
            throw new Exception\LocalException('METHOD_TYPE_ERROR');
        }
        if ($id === null) {
            $id = (int) mt_rand(1, 1024);
        } elseif (!is_int($id)) {
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
