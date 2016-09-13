<?php

namespace Awelty\Component\Security;

use Psr\Http\Message\RequestInterface;

class HmacAuthenticator implements AuthenticatorInterface
{
    /**
     * Header à mettre dans une request pour ne pas signer avec le contenu du body
     */
    const SIGNATURE_SKIP_BODY_HEADER = 'X-Signature-Skip-Body';

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
    public function signRequest(RequestInterface $request)
    {
        if ($request->hasHeader(self::SIGNATURE_SKIP_BODY_HEADER)) {
            $body = null;
            $request = $request->withoutHeader(self::SIGNATURE_SKIP_BODY_HEADER);
        } else {
            $body = $request->getBody()->getContents();
        }

        $datetime = new \DateTime();

        $plainSignature = $request->getMethod().urldecode($request->getRequestTarget()).$datetime->format(\DateTime::ISO8601).$body;

        return $request
            ->withHeader('X-Public-Key', $this->publicKey)
            ->withHeader('X-Datetime', $datetime->format(\DateTime::ISO8601))
            ->withHeader('X-Signature', hash_hmac($this->algo, $plainSignature, $this->privateKey));
    }
}