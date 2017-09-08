<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Repository;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use Ivyhjk\Phalcon\OAuth2\Server\Entity\RefreshToken as RefreshTokenEntity;
use Ivyhjk\Phalcon\OAuth2\Server\Model\AccessToken as AccessTokenModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\RefreshToken as RefreshTokenModel;

/**
 * A refresh token repository.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Repository
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class RefreshToken extends BaseRepository implements
    \League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface
{
    /**
     * Creates a new refresh token
     *
     * @return League\OAuth2\Server\Entities\RefreshEntityInterface
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }

    /**
     * Create a new refresh token_name.
     *
     * @param League\OAuth2\Server\Entities\RefreshTokenEntityInterface $refreshTokenEntity
     *
     * @return void
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws League\OAuth2\Server\Exception\OAuthServerException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $accessTokenModel = $this->getNewBuilder()
            ->columns([
                'AccessToken.id',
                'AccessToken.identifier',
            ])
            ->addFrom(AccessTokenModel::class, 'AccessToken')
            ->where('AccessToken.identifier = :identifier:', [
                'identifier' => $refreshTokenEntity->getAccessToken()->getIdentifier()
            ])
            ->limit(1)
            ->getQuery()
            ->getSingleResult();

        $refreshToken = new RefreshTokenModel();
        $refreshToken->identifier = $refreshTokenEntity->getIdentifier();

        $refreshToken->access_token_id = $accessTokenModel->id;

        $refreshToken->expire_time = $refreshTokenEntity
            ->getExpiryDateTime()
            ->format('Y-m-d H:i:s');

        if ( ! $refreshToken->save()) {
            var_dump($refreshToken->getMessages());exit();
            throw OAuthServerException::serverError(
                'Refresh token can not be persisted.'
            );
        }
    }

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId)
    {
        throw new \Exception('RefreshToken::revokeRefreshToken');
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        throw new \Exception('RefreshToken::isRefreshTokenRevoked');
    }
}
