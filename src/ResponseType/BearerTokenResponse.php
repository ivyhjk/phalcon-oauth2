<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\ResponseType;

use Phalcon\Http\ResponseInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

/**
 * A bearer response implementation for phalcon.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\ResponseType
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class BearerTokenResponse extends
    \League\OAuth2\Server\ResponseTypes\BearerTokenResponse
    implements \Ivyhjk\Phalcon\OAuth2\Server\ResponseType\ResponseTypeInterface
{
    /**
     * Get the associated access token entity.
     *
     * @return League\OAuth2\Server\Entities\AccessTokenEntityInterface
     */
    public function getAccessToken() : AccessTokenEntityInterface
    {
        return $this->accessToken;
    }

    /**
     * Get the associated refresh token entity.
     *
     * @return mixed[null|League\OAuth2\Server\Entities\RefreshTokenEntityInterface]
     */
    public function getRefreshToken() : ?RefreshTokenEntityInterface
    {
        return $this->refreshToken;
    }

    /**
     * Get the associated private key.
     *
     * @return League\OAuth2\Server\CryptKey
     */
    public function getPrivateKey() : CryptKey
    {
        return $this->privateKey;
    }

    /**
     * Generate a new phalcon HTTP response.
     *
     * @param Phalcon\Http\ResponseInterface $response A Phalcon response.
     *
     * @return Phalcon\Http\ResponseInterface
     */
    public function generatePhalconHttpResponse(
        ResponseInterface $response
    ) : ResponseInterface
    {
        $accessToken = $this->getAccessToken();
        $refreshToken = $this->getRefreshToken();

        $expireDateTime = $accessToken->getExpiryDateTime()->getTimestamp();
        $jwtAccessToken = $accessToken->convertToJWT($this->getPrivateKey());

        $responseParams = [
            'token_type'   => 'Bearer',
            'expires_in'   => $expireDateTime - (new \DateTime())->getTimestamp(),
            'access_token' => (string) $jwtAccessToken
        ];

        if ($refreshToken instanceof RefreshTokenEntityInterface) {
            $refreshTokenResponse = $this->encrypt(json_encode([
                'client_id'        => $accessToken->getClient()->getIdentifier(),
                'refresh_token_id' => $refreshToken->getIdentifier(),
                'access_token_id'  => $accessToken->getIdentifier(),
                'scopes'           => $accessToken->getScopes(),
                'user_id'          => $accessToken->getUserIdentifier(),
                'expire_time'      => $refreshToken->getExpiryDateTime()->getTimestamp()
            ]));

            $responseParams['refresh_token'] = $refreshTokenResponse;
        }

        $responseParams = array_merge(
            $this->getExtraParams($accessToken),
            $responseParams
        );

        $response->setStatusCode(200);
        $response->setHeader('pragma', 'no-cache');
        $response->setHeader('cache-control', 'no-store');
        $response->setHeader('content-type', 'application/json; charset=UTF-8');
        $response->setJsonContent($responseParams);

        return $response;
    }
}
