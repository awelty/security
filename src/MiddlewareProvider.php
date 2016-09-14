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
    public static function signRequestMiddleware(SignatureProviderInterface $signatureProvider)
    {
        return function (callable $handler) use ($signatureProvider) {
            return function (RequestInterface $request, array $options) use ($handler, $signatureProvider) {
                $request = $signatureProvider->sign($request);
                return $handler($request, $options);
            };
        };
    }
}
