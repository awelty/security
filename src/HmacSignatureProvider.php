<?php

namespace Awelty\Component\Security;

use Psr\Http\Message\RequestInterface;

/**
 * signe des request en hmac
 */
class HmacSignatureProvider implements SignatureProviderInterface
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var
     */
    private $algo;

    /**
     * HmacAuthenticator constructor.
     * @param string $publicKey
     * @param string $privateKey
     * @param string $algo md5, sha1, ...
     */
    public function __construct($publicKey, $privateKey, $algo)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->algo = $algo;
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function sign(RequestInterface $request)
    {
        $datetime = new \DateTime();

        $plainSignature = $request->getMethod().urldecode($request->getRequestTarget()).$datetime->format(\DateTime::ISO8601);

        return $request
            ->withHeader('X-Public-Key', $this->publicKey)
            ->withHeader('X-Datetime', $datetime->format(\DateTime::ISO8601))
            ->withHeader('X-Signature', hash_hmac($this->algo, $plainSignature, $this->privateKey));
    }
}
