<?php

namespace Ivyhjk\Phalcon\OAuth2\Server;

use Phalcon\Http\RequestInterface;
use Phalcon\Http\ResponseInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use Ivyhjk\Phalcon\OAuth2\Server\ResponseType\BearerTokenResponse;
use Ivyhjk\Phalcon\OAuth2\Server\ResponseType\ResponseTypeInterface;

/**
 * An OAuth2 authorization server.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class AuthorizationServer extends \League\OAuth2\Server\AuthorizationServer
{
    /**
     * Get the enabled grant types.
     *
     * @return array<League\OAuth2\Server\Grant\GrantTypeInterface>
     */
    public function getEnabledGrantTypes() : array
    {
        return $this->enabledGrantTypes;
    }

    /**
     * Respond to a phalcon access token request.
     *
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     * @param Phalcon\Http\ResponseInterface $response A Phalcon response.
     *
     * @return Phalcon\Http\ResponseInterface
     */
    public function respondToPhalconAccessTokenRequest(
        RequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface
    {
        foreach ($this->getEnabledGrantTypes() as $grantType) {
            if ($grantType->canRespondToPhalconAccessTokenRequest($request)) {
                $tokenResponse = $grantType->respondToPhalconAccessTokenRequest(
                    $request,
                    $this->getResponseType(),
                    $this->grantTypeAccessTokenTTL[$grantType->getIdentifier()]
                );

                if ($tokenResponse instanceof ResponseTypeInterface) {
                    return $tokenResponse->generatePhalconHttpResponse($response);
                }
            }
        }

        throw OAuthServerException::unsupportedGrantType();
    }

    /**
     * Get the token type that grants will return in the HTTP response.
     *
     * @return Ivyhjk\Phalcon\OAuth2\Server\ResponseType\ResponseTypeInterface
     */
    protected function getResponseType()
    {
        if ($this->responseType instanceof ResponseTypeInterface === false) {
            $this->responseType = new BearerTokenResponse();
        }

        return parent::getResponseType();
    }
}
