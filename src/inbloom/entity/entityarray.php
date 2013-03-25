<?php

namespace InBloom\Entity;

class EntityArray extends \ArrayObject
{
    protected $_request = null;

    /*
        Adding chaining support to append
    */
    public function append($o)
    {
        parent::append($o);

        return $this;
    }

    /*
        Chaining method alias to append
    */
    public function add($o)
    {
        return $this->append($o);
    }

    public function addAll($objs)
    {
        foreach($objs as $o)
        {
            $this->append($o);
        }

        return $this;
    }

    public function get($id)
    {
        foreach($this as $item)
        {
            if($item->getId() === $id)
            {
                return $item;
            }
        }
        return null;
    }

    public function at($index)
    {
        return $this->offsetGet($index);
    }

    public function getData()
    {
        $data = array();

        foreach($this as $item)
        {
            $data[] = $item->getData();
        }

        return $data;
    }

    public function toJSON()
    {
        return json_encode($this->getData());
    }


    /*
        Getters and Setters
    */
    public function getRequest()
    {
        return $this->_request;
    }

    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

}
