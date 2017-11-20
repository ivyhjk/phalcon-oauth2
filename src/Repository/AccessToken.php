<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Repository;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Ivyhjk\Phalcon\OAuth2\Server\Entity\AccessToken as AccessTokenEntity;
use Ivyhjk\Phalcon\OAuth2\Server\Model\AccessToken as AccessTokenModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\Scope as ScopeModel;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * An access token repository.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Repository
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class AccessToken extends BaseRepository implements
    \League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface
{
    /**
     * Create a new access token
     *
     * @param ClientEntityInterface  $clientEntity
     * @param ScopeEntityInterface[] $scopes
     * @param mixed                  $userIdentifier
     *
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);

        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     *
     * @return void
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $scopeIdentifiers = [];

        foreach ($accessTokenEntity->getScopes() as $scope) {
            $scopeIdentifiers[] = $scope->getIdentifier();
        }

        $scopeModels = ScopeModel::query()
            ->inWhere('identifier', $scopeIdentifiers)
            ->execute();

        $accessToken = new AccessTokenModel();
        $accessToken->identifier = $accessTokenEntity->getIdentifier();
        $accessToken->user_id = $accessTokenEntity->getUserIdentifier();
        $accessToken->client_id = $accessTokenEntity->getClient()->getId();
        $accessToken->scopes = $scopeModels;
        $accessToken->revoked = 0;
        $accessToken->expires_at = $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s');
        $accessToken->created_at = date('Y-m-d H:i:s');
        $accessToken->save();

        if ( ! $accessToken->save()) {
            throw OAuthServerException::serverError(
                'Access token token can not be persisted.'
            );
        }

        $accessTokenEntity->setId($accessToken->id);
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId)
    {
        throw new \Exception('AccessToken::revokeAccessToken');
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId)
    {
        throw new \Exception('AccessToken::isAccessTokenRevoked');
    }
}
