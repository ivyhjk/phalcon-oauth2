<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use Ivyhjk\Phalcon\OAuth2\Server\Entity\User as UserEntity;
use Ivyhjk\Phalcon\OAuth2\Server\Model\Client as ClientModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\User as UserModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\UserClient as UserClientModel;

/**
 * A user repository.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Repository
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class User extends BaseRepository implements
    \League\OAuth2\Server\Repositories\UserRepositoryInterface
{
    /**
     * Get a user entity.
     *
     * @param string                $username
     * @param string                $password
     * @param string                $grantType    The grant type used
     * @param ClientEntityInterface $clientEntity
     *
     * @return UserEntityInterface
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $builder = $this->getNewBuilder()
            ->columns([
                'User.id',
                'User.username',
                'User.password'
            ])
            ->addFrom(UserModel::class, 'User')
            ->where('User.username = :username:', compact('username'))
            ->limit(1);

        $builder
            ->innerJoin(UserClientModel::class, 'UserClient.user_id = User.id', 'UserClient')
            ->innerJoin(ClientModel::class, 'Client.id = UserClient.client_id', 'Client')
            ->andWhere('Client.identifier = :client_id:', [
                'client_id' => $clientEntity->getIdentifier()
            ]);

        $query = $builder->getQuery();
        $result = $query->getSingleResult();

        if (
            ! $result
            || ! $this->getSecurity()->checkHash($password, $result->password)
        ) {
            throw OAuthServerException::invalidCredentials();
        }

        $user = new UserEntity();
        $user->setIdentifier($result->id);

        return $user;
    }
}
