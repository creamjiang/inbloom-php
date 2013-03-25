<?php

namespace InBloom\Api;

abstract class Api
{
    protected $token = null;

    protected $apiUrl = null;

    public function __construct($token, $host)
    {
        $this->token = $token;
        $this->apiUrl = 'https://'.$host.'/api/rest/v1.1/';

    }

    public function getApiPath($url)
    {
        if(substr($url, 0, strlen($this->apiUrl)) == $this->apiUrl)
        {
            return substr($url, strlen($this->apiUrl));
        }

        return null;
    }

    public function fetch($path)
    {
        $call = $this->request($path);

        return $call->getEntity();
    }

    public function request($path, $data = array(), $method = 'GET')
    {
        return new Request($this, $path, $data, $method);
    }

    public function sendRequest(Request $request)
    {
        $url = $this->apiUrl.$request->getPath();

        $headers = array(
            'Accept: application/vnd.slc+json',
            'Content-Type: application/vnd.slc+json',
            'Authorization: bearer '.$this->token,
        );

        $context = stream_context_create(array(
            'http' => array(
                'method' => $request->getMethod(),
                'header' => $headers,
                'ignore_errors' => true,
                'content' => json_encode($request->getData()),
            ),
        ));

        $result = file_get_contents($url, false, $context);


        return new Response($request, $result, $http_response_header);
    }

    public function send($path, $data = array(), $method = 'GET')
    {
        return $this->sendRequest($this->request($path, $data, $method));
    }


    public function __call($method, $arguments)
    {
        if(in_array($method, array('post', 'get', 'put', 'delete', 'patch')))
        {
            $path = $arguments[0];
            $data = isset($arguments[1]) ? $arguments[1] : array();
            return $this->send($path, $data, strtoupper($method));
        }
    }
}
