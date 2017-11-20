<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Entity;

/**
 * A refresh token entity implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Entity
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class RefreshToken extends Token implements
    \Ivyhjk\Phalcon\OAuth2\Server\Contract\Entity\RefreshToken
{
    use \League\OAuth2\Server\Entities\Traits\EntityTrait;
    use \League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;
}
