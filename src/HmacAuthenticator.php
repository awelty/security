<?php

namespace Awelty\Component\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Auth hmac pour utiliser avec le composant symfony/security
 */
class HmacAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var string
     */
    private $algo;

    public function __construct($algo)
    {
        $this->algo = $algo;
    }

    public function getCredentials(Request $request)
    {
        $publicKey  = $request->headers->get('X-Public-Key');
        $datetime   = $request->headers->get('X-Datetime');
        $signature  = $request->headers->get('X-Signature');

        if ($publicKey && $datetime && $signature) {
            return [
                'publicKey' => $publicKey,
                'datetime'  => new \DateTime($datetime),
                'signature' => $signature,
                'request'   => $request,
            ];
        }

        return null;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['publicKey']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        /** @var Request $request */
        $request = $credentials['request'];

        $plainSignature = $request->getMethod().urldecode($request->getRequestTarget()).$credentials['datetime']->format(\DateTime::ISO8601);

        $signature = hash_hmac($this->algo, $plainSignature, $user->getPassword());

        return $signature === $credentials['signature'];
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => $exception->getMessage(), // TODO translation
        ], Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'error' => 'Authentification required',
        ], Response::HTTP_FORBIDDEN);
    }
}