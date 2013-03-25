<?php

namespace InBloom\Entity;

use InBloom\Api\Api;

abstract class Entity
{
    private $_id = null;

    protected $_api = null;

    protected $_urlRoot = null;

    protected $_data = array();

    protected $_origData = array();

    protected $_changed = array();

    protected $_links = null;

    protected $_request = null;

    public function __construct(Api $api, $data = array())
    {
        $this->_api = $api;
        $this->_data = $data;

        if(isset($this->_data['links']))
        {
            $this->_links = new Links($this, $this->_data['links']);
            unset($this->_data['links']);
        }

        if(isset($this->_data['id']))
        {
            $this->_id = $this->_data['id'];
        }

        $this->_origData = $this->_data;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getApi()
    {
        return $this->_api;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getLinks()
    {
        return $this->_links;
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

    public function __get($key)
    {
        if(isset($this->_data[$key]))
        {
            return $this->_data[$key];
        }

        return null;
    }

    public function __set($key, $value)
    {
        if(isset($this->_data[$key]) && $this->_data[$key] != $value)
        {
            $this->_changed[$key] = $value;
        }
        $this->_data[$key] = $value;
    }

    public function toJSON()
    {
        return json_encode($this->getData());
    }

    public static function make(Api $api, $data)
    {
        $type = isset($data['entityType']) ? $data['entityType'] : '';

        if(class_exists($class = '\\InBloom\\Entity\\'.ucwords($type)))
        {
            return new $class($api, $data);
        }
        else
        {
            return new Generic($api, $data);
        }
    }

    public function save($url = null, $full = false)
    {

        //here's where the magic happens

        //Exists, PUT/PATCH (I'm favoring patch, $full overrides it though)
        if($this->_id)
        {
            $targetURL = $url ?: $this->_urlRoot.'/'.$this->_id;

            if($full)
            {
                $response = $this->_api->send($targetURL, $this->_data, 'PUT');
            }
            else if(!count($this->_changed)) //PATCH with no values
            {
                return false;
            }
            else
            {
                $response = $this->_api->send($targetURL, $this->_changed, 'PATCH');
            }

        }
        else //Does not exist, POST
        {
            $targetURL = $url ?: $this->_urlRoot;

            $response = $this->_api->send($targetURL, $this->_data, 'POST');

            //grab the new id
            $loc = explode('/', $response->getHeader('Content-Location'));
            $id = $loc[count($loc) - 1];

            $this->_id = $id;
        }

        //assuming all is well, reset the values as though this is an all new item
        if(!$response->isError())
        {
            $this->clean();
        }

        return $response;
    }

    public function delete()
    {
        if($this->_id)
        {
            $targetURL = $this->_urlRoot.'/'.$this->_id;

            $response = $this->_api->send($targetURL, null, 'DELETE');
            return $response;
        }

        return false;
    }

    public function clean()
    {
        $this->_origData = $this->_data;
        $this->_changed = array();
    }

    public function reset()
    {
        $this->_data = $this->_origData;
        $this->_changed = array();
    }


    public function __isset($name)
    {
        echo "Is '$name' set?\n";
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        echo "Unsetting '$name'\n";
        unset($this->data[$name]);
    }

    /*
        Convinience method for $entity->getLinks()->{call}
    */
    public function __call($method, $args)
    {
        if($this->_links)
        {
            return $this->_links->__call($method, $args);
        }
    }
}
