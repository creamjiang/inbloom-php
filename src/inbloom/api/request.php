<?php

namespace InBloom\Api;

class Request
{
    protected $_api;

    protected $_path;

    protected $_response = null;

    protected $_data = array();

    protected $_method = 'GET';

    public function __construct(\InBloom\Api\Api $api, $path, $data = array(), $method = 'GET')
    {
        $this->_api = $api;
        $this->_path = $path;
        $this->_data = $data;
        $this->_method = $method;
    }

    public function getResponse()
    {
        if(!$this->_response)
        {
            $this->_response = $this->_api->sendRequest($this);
        }

        return $this->_response;
    }

    public function getApi()
    {
        return $this->_api;
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    /* Convenience method to get response and entity */
    public function getEntity()
    {
        return $this->getResponse()->getEntity();
    }

    public function duplicate($path = null, $data = null, $method = null)
    {
        $path = $path ?: $this->_path;
        $data = $data ?: $this->_data;
        $method = $method ?: $this->_method;

        return new self($this->_api, $this->_path, $this->_data, $this->_method);
    }
}
