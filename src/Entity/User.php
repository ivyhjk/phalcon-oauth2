<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Entity;

/**
 * A user entity implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Entity
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class User implements \League\OAuth2\Server\Entities\UserEntityInterface
{
    use \League\OAuth2\Server\Entities\Traits\EntityTrait;
}
