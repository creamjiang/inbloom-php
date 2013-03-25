<?php

namespace InBloom\Entity;

class Generic extends Entity
{

    public function __construct($api, $data = array())
    {
        parent::__construct($api, $data);

        //calculate urlRoot (this is pretty lousy)
        $this->urlRoot = isset($data['entityType']) ? $data['entityType'].'s' : 'unknown';
    }
}
