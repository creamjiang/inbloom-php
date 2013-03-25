<?php

namespace InBloom\Api;

use InBloom\Entity\Entity;
use InBloom\Entity\EntityArray;

class Response
{
    private $_rawData = null;

    private $_jsonData = null;

    private $_errorCode = null;

    private $_errorMessage = null;

    private $_errorType = null;

    private $_headers = array();

    private $_statusCode = null;

    private $_statusMessage = null;

    private $_request = null;

    protected $_entity = null;

    public function __construct(Request $request, $data, $headers)
    {
        $this->_request = $request;
        $this->_rawData = trim($data);
        $this->_process($headers);
    }

    private function _process($headers)
    {
        $j = $this->_jsonData = json_decode($this->_rawData, true);

        if(isset($j['type']) && isset($j['code']) && isset($j['message']))
        {
            $this->_errorType = $j['type'];
            $this->_errorCode = $j['code'];
            $this->_errorMessage = $j['message'];
        }

        $this->_headers = array();

        foreach($headers as $h)
        {
            $split = explode(':', $h, 2);

            if(count($split) == 1)
            {
                $status = explode(' ', $h, 3);
                //likely the first result, which has the status code too
                $this->_headers[] = $h;
                $this->_statusCode = (int) $status[1];
                $this->_statusMessage = $status[2];
            }
            else
            {
                $this->_headers[$split[0]] = $split[1];
            }
        }

        if($this->_statusCode == 401)
        {
            throw new UnauthorizedException($this->getRequest());
        }
    }

    public function getRawData()
    {
        return $this->_rawData;
    }

    public function getJsonData()
    {
        return $this->_jsonData;
    }

    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    public function getErrorType()
    {
        return $this->_errorType;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function getHeader($name)
    {
        return isset($this->_headers[$name]) ? $this->_headers[$name] : null;
    }

    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    public function getStatusMessage()
    {
        return $this->_statusMessage;
    }

    public function isError()
    {
        return !!$this->_errorCode;
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function getEntity()
    {
        if(!$this->_entity)
        {
            //this is kind of hacky to determine if we get an object back or array of objects
            $rawData = $this->getRawData();
            $multiple = $rawData[0] == '[';

            $data = $this->getJsonData();

            if($multiple)
            {
                $this->_entity = new EntityArray();

                foreach($data as $obj)
                {
                    $this->_entity->add($this->_createEntity($obj));
                }
            }
            else
            {
                $this->_entity = $this->_createEntity($data);
            }
        }

        $this->_entity->setRequest($this->getRequest());

        return $this->_entity;
    }

    protected function _createEntity($data)
    {
        return Entity::make($this->getRequest()->getApi(), $data);
    }
}

