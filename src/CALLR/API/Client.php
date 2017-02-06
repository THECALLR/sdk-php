<?php
namespace CALLR\API;

use CALLR\API\Authentication;

/**
 * JSON-RPC 2.0 Client
 * @author Florent CHAUVEAU <fc@callr.com>
 *
 * @todo Add "sandbox" mode for urls ?
 */
class Client
{
    const API_URL = 'https://api.callr.com/json-rpc/v1.1/';
    const SDK_VERSION = '0.10';

    /** @var string "login:password" */
    private $auth;
    /** @var string API URL */
    private $url;
    /** @var string[] HTTP Headers */
    private $headers = [];
    /** @var string proxy */
    private $proxy = null;

    /**
     * Change API endpoint.
     * @param string $url API endpoint
     * @deprecated 0.10 Set in stone via the constant
     */
    public function setURL($url)
    {
        trigger_error('setURL is deprecated. Do not change the URL (or extend client to be able to do so)', E_USER_DEPRECATED);
        $this->url = $url;
    }

    /**
     * Set API credentials (username, password).
     * @param string $username Username
     * @param string $password Password
     * @deprecated 0.10 Use setAuth with proper auth instead
     */
    public function setAuthCredentials($username, $password)
    {
        trigger_error('setAuthCredentials is deprecated. Use setAuth with proper authentifier', E_USER_DEPRECATED);
        $this->setAuth(new Authentication\LoginPasswordAuth($username, $password));
    }

    /**
     * Set API auth token.
     * @param string $token API token
     * @deprecated 0.10 Use setAuth with proper auth instead
     */
    public function setAuthToken($token)
    {
        trigger_error('setAuthToken is deprecated. Use setAuth with proper authentifier', E_USER_DEPRECATED);
        $this->setAuth(new Authentication\ApiKeyAuth($token));
    }

    public function setAuth(Authentication\AuthenticationInterface $auth)
    {
        $this->auth = $auth;
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
            $this->url ?: self::API_URL,
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
            $this->url ?: self::API_URL,
            $this->auth,
            $this->headers,
            $this->proxy,
            true
        );
    }
}
