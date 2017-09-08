<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Model;

/**
 * An access token model implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Model
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class AccessToken extends \Phalcon\Mvc\Model
{
    public function initialize()
    {
        $this->setSource('oauth2_access_tokens');

        $this->hasManyToMany(
            'id',
            AccessTokenScope::class,
            'access_token_id',
            'scope_id',
            Scope::class,
            'id',
            [
                'alias' => 'scopes'
            ]
        );
    }
}
