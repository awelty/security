<?php

namespace Awelty\Component\Security;

use Psr\Http\Message\RequestInterface;

/**
 * Provider of security Middleware (for guzzle)
 */
final class MiddlewareProvider
{
    /**
     * @param AuthenticatorInterface $authenticator
     * @return \Closure
     */
    public static function signRequestMiddleware(SignRequestInterface $signRequest)
    {
        return function (callable $handler) use ($authenticator) {
            return function (RequestInterface $request, array $options) use ($handler, $authenticator) {
                $request = $signRequest->sign($request);
                return $handler($request, $options);
            };
        };
    }
}
