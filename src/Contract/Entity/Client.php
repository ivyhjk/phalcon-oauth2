<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Contract\Entity;

/**
 * A client entity representation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Contract\Entity
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
interface Client extends
    \League\OAuth2\Server\Entities\ClientEntityInterface,
    Entity
{

}
