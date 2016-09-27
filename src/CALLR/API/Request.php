<?php

namespace CALLR\API;

/**
 * JSON-RPC 2.0 Request
 * @author Florent CHAUVEAU <fc@callr.com>
 */
class Request
{
    const JSON_RPC_VERSION = '2.0';

    public $id = 0;
    public $method;
    public $params = [];


    /**
     * Send the request!
     * @param string $url Endpoint URL
     * @param string $auth Endpoint HTTP Basic Authentication
     * @param string[] $headers HTTP headers (array of "Key: Value" strings)
     * @param bool $raw_response Returns the raw response
     * @param string $proxy HTTP proxy to use
     * @return \CALLR\API\Response Response object
     * @throws \CALLR\API\Exception\LocalException
     */
    public function send(
        $url,
        $auth = null,
        array $headers = [],
        $proxy = null,
        $raw_response = false
    ) {
        /* JSON-RPC request */
        $request = new \stdClass;
        $request->id = (int) $this->id;
        $request->method = (string) $this->method;
        $request->params = (array) $this->convertParams();
        $request->jsonrpc = self::JSON_RPC_VERSION;

        /* content type */
        $headers[] = 'Content-Type: application/json-rpc; charset=utf-8';
        $headers[] = 'Expect:'; // avoid lighttpd bug

        /* curl */
        $c = curl_init($url);

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_FAILONERROR, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($request));
        curl_setopt($c, CURLOPT_FORBID_REUSE, false);

        if (!empty($auth)) {
            curl_setopt($c, CURLOPT_USERPWD, $auth);
        }

        if (is_string($proxy)) {
            curl_setopt($c, CURLOPT_PROXY, $proxy);
        }

        $data = curl_exec($c);

        /* curl error */
        if ($data === false) {
            throw new Exception\LocalException('CURL_ERROR: '.curl_error($c), curl_errno($c));
        }

        curl_close($c);

        /* response */
        return $raw_response ? $data : new Response($data);
    }

    private function convertParams()
    {
        foreach ($this->params as &$value) {
            if ($value instanceof \CALLR\Objects\Object) {
                $value = $value->export();
            }
        }
        return $this->params;
    }
}
