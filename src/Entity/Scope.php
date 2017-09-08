<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Entity;

/**
 * A scope entity implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Entity
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class Scope implements \League\OAuth2\Server\Entities\ScopeEntityInterface
{
    use \League\OAuth2\Server\Entities\Traits\EntityTrait;

    public function jsonSerialize()
    {
        return [
            'identifier' => $this->getIdentifier()
        ];
    }
}
