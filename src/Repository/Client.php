<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use Ivyhjk\Phalcon\OAuth2\Server\Entity\Client as ClientEntity;
use Ivyhjk\Phalcon\OAuth2\Server\Model\Client as ClientModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\ClientGrant as ClientGrantModel;
use Ivyhjk\Phalcon\OAuth2\Server\Model\Grant as GrantModel;

/**
 * A client repository.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Repository
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class Client extends BaseRepository implements
    \League\OAuth2\Server\Repositories\ClientRepositoryInterface
{
    /**
     * Get a client.
     *
     * @param string      $clientIdentifier   The client's identifier
     * @param string      $grantType          The grant type used
     * @param null|string $clientSecret       The client's secret (if sent)
     * @param bool        $mustValidateSecret If true the client must attempt to validate the secret if the client
     *                                        is confidential
     *
     * @return League\OAuth2\Server\Entities\ClientEntityInterface
     */
    public function getClientEntity(
        $clientIdentifier,
        $grantType,
        $clientSecret = null,
        $mustValidateSecret = true
    ) : ClientEntityInterface
    {
        $builder = $this->getNewBuilder()
            ->columns([
                'Client.id',
                'Client.identifier',
                'Client.secret',
                'Client.name'
            ])
            ->addFrom(ClientModel::class, 'Client')
            ->where(
                'Client.identifier = :clientIdentifier:',
                compact('clientIdentifier')
            )
            ->limit(1);

        $builder
            ->innerJoin(
                ClientGrantModel::class,
                'ClientGrant.client_id = Client.id',
                'ClientGrant'
            )
            ->innerJoin(
                GrantModel::class,
                'Grant.id = ClientGrant.grant_id',
                'Grant'
            )
            ->andWhere('Grant.identifier = :grantType:', compact('grantType'));

        $query = $builder->getQuery();
        $result = $query->getSingleResult();

        if (
            ! $result
            || (
                $mustValidateSecret === true
                && ! $this->getSecurity()->checkHash(
                    $clientSecret,
                    $result->secret
                )
            )
        ) {
            throw OAuthServerException::invalidClient();
        }

        $client = new ClientEntity($result->id);
        $client->setName($result->name);
        $client->setIdentifier($result->identifier);

        return $client;
    }
}
