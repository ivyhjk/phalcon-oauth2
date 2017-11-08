<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Grant;

use Phalcon\Http\RequestInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant as LeaguePasswordGrant;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

/**
 * A password grant implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Grant
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class PasswordGrant extends LeaguePasswordGrant implements GrantTypeInterface
{
    use GrantTypeTrait;

    /**
     * Get the associated client repository.
     *
     * @return League\OAuth2\Server\Repositories\ClientRepositoryInterface
     */
    public function getClientRepository() : ClientRepositoryInterface
    {
        return $this->clientRepository;
    }

    /**
     * Get the associated user repository.
     *
     * @return League\OAuth2\Server\Repositories\UserRepositoryInterface
     */
    public function getUserRepository() : UserRepositoryInterface
    {
        return $this->userRepository;
    }

    /**
     * Get the associated user repository.
     *
     * @return League\OAuth2\Server\Repositories\ScopeRepositoryInterface
     */
    public function getScopeRepository() : ScopeRepositoryInterface
    {
        return $this->scopeRepository;
    }

    /**
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     * @param League\OAuth2\Server\Entities\ClientEntityInterface $client
     *
     * @throws OAuthServerException
     *
     * @return League\OAuth2\Server\Entities\UserEntityInterface
     */
    protected function validatePhalconUser(RequestInterface $request, ClientEntityInterface $client)
    {
        $username = $this->getPhalconRequestParameter('username', $request);

        if ($username === null) {
            throw OAuthServerException::invalidRequest('username');
        }

        $password = $this->getPhalconRequestParameter('password', $request);

        if ($password === null) {
            throw OAuthServerException::invalidRequest('password');
        }

        $user = $this->getUserRepository()->getUserEntityByUserCredentials(
            $username,
            $password,
            $this->getIdentifier(),
            $client
        );

        if ($user instanceof UserEntityInterface === false) {
            // TODO: Add a request event for phalcon.
            // $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }

    /**
     * Respond to an incoming request.
     *
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     * @param League\OAuth2\Server\ResponseTypes\ResponseTypeInterface $responseType
     * @param \DateInterval $accessTokenTTL
     *
     * @return League\OAuth2\Server\ResponseTypes\ResponseTypeInterface
     */
    public function respondToPhalconAccessTokenRequest(
        RequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ) : ResponseTypeInterface
    {
        $client = $this->validatePhalconClient($request);
        // Validate request
        $scopes = $this->validateScopes($this->getPhalconRequestParameter('scope', $request));
        $user = $this->validatePhalconUser($request, $client);

        // Finalize the requested scopes
        $scopes = $this->getScopeRepository()->finalizeScopes(
            $scopes,
            $this->getIdentifier(),
            $client,
            $user->getIdentifier()
        );

        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    /**
     * The grant type should return true if it is able to response to an authorization request
     *
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     *
     * @return bool
     */
    public function canRespondToPhalconAuthorizationRequest(
        RequestInterface $request
    ) : bool
    {
        throw new \Exception('PasswordGrant::canRespondToPhalconAuthorizationRequest');
    }

    /**
     * If the grant can respond to an authorization request this method should be called to validate the parameters of
     * the request.
     *
     * If the validation is successful an AuthorizationRequest object will be returned. This object can be safely
     * serialized in a user's session, and can be used during user authentication and authorization.
     *
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     *
     * @return AuthorizationRequest
     */
    public function validatePhalconAuthorizationRequest(
        RequestInterface $request
    ) {
        throw new \Exception('PasswordGrant::validatePhalconAuthorizationRequest');
    }
}
