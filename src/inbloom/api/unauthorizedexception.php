<?php

namespace InBloom\Api;

class UnauthorizedException extends \Exception
{
    protected $_request = null;

    public function __construct(Request $request, $exception = '', $code = null, \Exception $previous = null)
    {
        $exception = $exception ?: 'Unauthorized call made to inBloom.  Be sure you are logged in and your session has not expired';

        parent::__construct($exception, $code, $previous);

        $this->_request = $request;
    }

    public function getRequest()
    {
        return $this->_request;
    }
}
