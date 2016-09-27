<?php

namespace CALLR\API;

/**
 * JSON-RPC 2.0 Client
 * @author Florent CHAUVEAU <fc@callr.com>
 */
class Client
{
    /** @var string "login:password" */
    private $auth;
    /** @var string API URL */
    private $url = "https://api.thecallr.com";
    /** @var string[] HTTP Headers */
    private $headers = [];
    /** @var string proxy */
    private $proxy = null;

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
     * Set HTTP proxy
     * @param string $proxy (http://proxy.url:port)
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @param string $method JSON-RPC method
     * @param mixed[] $params JSON-RPC parameters
     * @return mixed API response
     * @throws \CALLR\API\Exception\LocalException
     * @throws \CALLR\API\Exception\RemoteException
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

        $response = $request->send(
            $this->url,
            $this->auth,
            $this->headers,
            $this->proxy
        );

        if ($response->isError()) {
            throw $response->error;
        }

        return $response->result;
    }

    public function callRaw($method, array $params = [], $id = null)
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

        return $request->send(
            $this->url,
            $this->auth,
            $this->headers,
            $this->proxy,
            true
        );
    }
}
