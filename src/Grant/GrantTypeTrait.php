<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Grant;

use Phalcon\Http\RequestInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

/**
 * Grant type common methods.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Grant
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
trait GrantTypeTrait
{
    /**
     * The grant type should return true if it is able to respond to this request.
     *
     * For example most grant types will check that the $_POST['grant_type'] property matches it's identifier property.
     *
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     *
     * @return bool
     */
    public function canRespondToPhalconAccessTokenRequest(
        RequestInterface $request
    ) {
        $body = $request->getJsonRawBody();

        return (
            $body !== null
            && property_exists($body, 'grant_type')
            && $body->grant_type === $this->getIdentifier()
        );
    }

    /**
     * Retrieve HTTP Basic Auth credentials with the Authorization header
     * of a request. First index of the returned array is the username,
     * second is the password (so list() will work). If the header does
     * not exist, or is otherwise an invalid HTTP Basic header, return
     * [null, null].
     *
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     *
     * @return string[]|null[]
     */
    protected function getPhalconBasicAuthCredentials(RequestInterface $request)
    {
        $header = $request->getHeader('Authorization');

        if (!$header) {
            return [null, null];
        }

        var_dump($header);exit();

        $header = $request->getHeader('Authorization')[0];
        if (strpos($header, 'Basic ') !== 0) {
            return [null, null];
        }

        if (!($decoded = base64_decode(substr($header, 6)))) {
            return [null, null];
        }

        if (strpos($decoded, ':') === false) {
            return [null, null]; // HTTP Basic header without colon isn't valid
        }

        return explode(':', $decoded, 2);
    }

    /**
     * Retrieve request parameter.
     *
     * @param string                 $parameter
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     * @param mixed                  $default
     *
     * @return null|string
     */
    protected function getPhalconRequestParameter($parameter, RequestInterface $request, $default = null)
    {
        $body = $request->getJsonRawBody();

        if ($body === null || ! property_exists($body, $parameter)) {
            return $default;
        }

        return $body->{$parameter};
    }

    /**
     * Validate the client.
     *
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     *
     * @return ClientEntityInterface
     * @throws League\OAuth2\Server\Exception\OAuthServerException
     */
    protected function validatePhalconClient(RequestInterface $request)
    {
        list($basicAuthUser, $basicAuthPassword) = $this
            ->getPhalconBasicAuthCredentials($request);

        $clientId = $this->getPhalconRequestParameter(
            'client_id',
            $request,
            $basicAuthUser
        );

        if ($clientId === null) {
            throw OAuthServerException::invalidRequest('client_id');
        }

        // If the client is confidential require the client secret
        $clientSecret = $this->getPhalconRequestParameter('client_secret', $request, $basicAuthPassword);

        $client = $this->getClientRepository()->getClientEntity(
            $clientId,
            $this->getIdentifier(),
            $clientSecret,
            true
        );

        if ($client instanceof ClientEntityInterface === false) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request));
            throw OAuthServerException::invalidClient();
        }

        // If a redirect URI is provided ensure it matches what is pre-registered
        $redirectUri = $this->getPhalconRequestParameter('redirect_uri', $request, null);
        if ($redirectUri !== null) {
            if (
                is_string($client->getRedirectUri())
                && (strcmp($client->getRedirectUri(), $redirectUri) !== 0)
            ) {
                $this->getEmitter()->emit(new RequestEvent(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request));
                throw OAuthServerException::invalidClient();
            } elseif (
                is_array($client->getRedirectUri())
                && in_array($redirectUri, $client->getRedirectUri()) === false
            ) {
                $this->getEmitter()->emit(new RequestEvent(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request));
                throw OAuthServerException::invalidClient();
            }
        }

        return $client;
    }

    /**
     * Get the associated client repository.
     *
     * @return League\OAuth2\Server\Repositories\ClientRepositoryInterface
     */
    abstract public function getClientRepository() : ClientRepositoryInterface;
}
