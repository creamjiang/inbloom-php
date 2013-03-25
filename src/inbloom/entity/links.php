<?php

namespace InBloom\Entity;

use InBloom\Api\Request;

class Links
{
    protected $_source = null;

    protected $_entities = array();

    protected $_links = array();

    public function __construct($source, $links = array())
    {
        $this->_source = $source;
        $api = $source->getApi();

        foreach($links as $l)
        {
            if($path = $api->getApiPath($l['href']))
            {
                $this->_links[$l['rel']] = $path;
            }
        }
    }

    public function getAvailable()
    {
        return array_keys($this->_entities);
    }

    public function getLinks()
    {
        return $this->_links;
    }

    public function __call($value, $args)
    {
        return $this->__get($value);
    }

    public function __get($value)
    {
        if(isset($this->_entities[$value]))
        {
            return $this->_entities[$value];
        }
        else if(isset($this->_links[$value]))
        {
            $link = $this->_links[$value];

            return $this->_entities[$value] = $this->_source->getApi()->fetch($link);
        }

        return null;
    }
}
