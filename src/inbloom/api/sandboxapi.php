<?php

namespace InBloom\Api;

class SandboxApi extends Api
{
    public function __construct($token)
    {
        parent::__construct($token, 'api.sandbox.inbloom.org');
    }
}
