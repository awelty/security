<?php

namespace Awelty\Component\Security;

use Psr\Http\Message\RequestInterface;

/**
 * Provider of security Middleware
 */
final class Middleware
{
    /**
     * @param AuthenticatorInterface $authenticator
     * @return \Closure
     */
    public static function authenticateMiddleware(AuthenticatorInterface $authenticator)
    {
        return function (callable $handler) use ($authenticator) {
            return function (RequestInterface $request, array $options) use ($handler, $authenticator) {
                $request = $authenticator->signRequest($request);
                return $handler($request, $options);
            };
        };
    }
}
