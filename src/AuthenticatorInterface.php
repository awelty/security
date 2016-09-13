<?php

namespace Awelty\Component\Security;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

interface AuthenticatorInterface
{
    /**
     * Sign a request to authenticate
     * @param RequestInterface $request
     * @return Request
     */
    public function signRequest(RequestInterface $request);
}
