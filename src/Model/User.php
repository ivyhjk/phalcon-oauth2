<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Model;

/**
 * A user model implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Model
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class User extends \Phalcon\Mvc\Model
{
    public function initialize()
    {
        $this->setSource('oauth2_users');
    }
}
