<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use Ivyhjk\Phalcon\OAuth2\Server\Model\Client as ClientModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\Grant as GrantModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\Scope as ScopeModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\ScopeClient as ScopeClientModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\ScopeGrant as ScopeGrantModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\ScopeUser as ScopeUserModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\User as UserModel;
use Ivyhjk\Phalcon\OAuth2\Server\Entity\Scope as ScopeEntity;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * A scope repository.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Repository
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class Scope extends BaseRepository implements
    \League\OAuth2\Server\Repositories\ScopeRepositoryInterface
{
    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return ScopeEntityInterface
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $result = $this->getNewBuilder()
            ->columns([
                'Scope.id',
                'Scope.identifier'
            ])
            ->addFrom(ScopeModel::class, 'Scope')
            ->where('Scope.identifier = :identifier:', compact('identifier'))
            ->limit(1)
            ->getQuery()
            ->getSingleResult();

        if ( ! $result) {
            throw OAuthServerException::invalidScope($identifier);
        }

        $scope = new ScopeEntity();
        $scope->setIdentifier($result->identifier);

        return $scope;
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string                 $grantType
     * @param ClientEntityInterface  $clientEntity
     * @param null|string            $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        $builder = $this->getNewBuilder()
            ->columns([
                'Scope.id',
                'Scope.identifier',
            ])
            ->addFrom(ScopeModel::class, 'Scope');

        $scopesIdentifiers = [];

        foreach ($scopes as $scope) {
            $scopesIdentifiers[] = $scope->getIdentifier();
        }

        $builder->inWhere('Scope.identifier', $scopesIdentifiers);

        // TODO: Maybe a validation for "limit_scopes_to_grants".
        $builder
            ->innerJoin(ScopeGrantModel::class, 'ScopeGrant.scope_id = Scope.id', 'ScopeGrant')
            ->innerJoin(GrantModel::class, 'Grant.id = ScopeGrant.grant_id', 'Grant')
            ->andWhere('Grant.identifier = :grantType:', compact('grantType'));

        // TODO: Maybe a validation for "limit_clients_to_scopes".
        $builder
            ->innerJoin(ScopeClientModel::class, 'ScopeClient.scope_id = Scope.id', 'ScopeClient')
            ->innerJoin(ClientModel::class, 'Client.id = ScopeClient.client_id', 'Client')
            ->andWhere('Client.identifier = :client_identifier:', [
                'client_identifier' => $clientEntity->getIdentifier()
            ]);

        // TODO: Maybe a validation for "limit_users_to_scopes".
        $builder
            ->innerJoin(ScopeUserModel::class, 'ScopeUser.scope_id = Scope.id', 'ScopeUser')
            ->innerJoin(UserModel::class, 'User.id = ScopeUser.user_id', 'User')
            ->AndWhere('User.id = :userIdentifier:', compact('userIdentifier'));

        $query = $builder->getQuery();
        $result = $query->execute();

        if ( ! $result || $result->count() <= 0) {
            $scope = current($scopes);

            throw OAuthServerException::invalidScope($scope->getIdentifier());
        }

        $entities = [];

        foreach ($result as $scope) {
            $entity = new ScopeEntity();
            $entity->setIdentifier($scope->identifier);

            $entities[] = $entity;
        }

        return $entities;
    }
}
