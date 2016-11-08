<?php

/**
 * Habitica
 * PHP class provide you access to habitica api get or add data to habitica client
 * @api version 3.0
 *
 */

class Habitica
{
    private $uid;
    private $token;
    private $endpoint = 'https://habitica.com/api/v3/';
    private $headers;
    private $allowedMethods = ['get', 'head', 'put', 'post', 'patch', 'delete'];

    public function __construct($uid, $token)
    {
        $this->uid = $uid;
        $this->token = $token;
        $this->headers = [
            "x-api-user:". $this->uid,
            "x-api-key:". $this->token
        ];
    }

    public function request($resource, $arguments = [], $method = 'GET')
    {
        if (!$this->uid && !$this->token) {
            return 'Please provide an API key.';
        }

        return $this->makeRequest($resource, $arguments, strtolower($method));
    }

    public function makeRequest($resource, $arguments, $method)
    {
        $options = $this->getOptions($method, $arguments);
        $url = $this->endpoint . $resource;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);

        switch ($method) {
            case 'post':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $options['query']);
                break;
            case 'get':
                curl_setopt($curl, CURLOPT_URL, $url);
                break;
            case 'delete':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'patch':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
                break;
            case 'put':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
        }

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    private function getOptions($method, $arguments)
    {
        if (count($arguments) < 1) {
            return $this->options;
        }

        if ($method == 'get' || $method === 'post') {
            $arguments = http_build_query($arguments, '', '&');
            $this->options['query'] = $arguments;
        } else {
            $this->options['json'] = $arguments;
        }

        return $this->options;
    }

    public function __call($method, $arguments)
    {
        if (count($arguments) < 1) {
            return 'Magic request methods require a URI and optional options array';
        }

        if (! in_array($method, $this->allowedMethods)) {
            return 'Method "' . $method . '" is not supported.';
        }

        $resource = $arguments[0];
        $options = isset($arguments[1]) ? $arguments[1] : [];
        return $this->request($resource, $options, $method);
    }
}
