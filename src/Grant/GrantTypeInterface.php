<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Grant;

use Phalcon\Http\RequestInterface;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;

/**
 * A grant type representation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Grant
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
interface GrantTypeInterface extends \League\OAuth2\Server\Grant\GrantTypeInterface
{
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
    ) : ResponseTypeInterface;

    /**
     * The grant type should return true if it is able to response to an authorization request
     *
     * @param Phalcon\Http\RequestInterface $request A Phalcon request.
     *
     * @return bool
     */
    public function canRespondToPhalconAuthorizationRequest(
        RequestInterface $request
    );

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
    );

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
    );
}
