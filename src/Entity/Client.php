<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Entity;

/**
 * A client entity implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Entity
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class Client extends Entity implements
    \Ivyhjk\Phalcon\OAuth2\Server\Contract\Entity\Client
{
    use \League\OAuth2\Server\Entities\Traits\ClientTrait;
    use \League\OAuth2\Server\Entities\Traits\EntityTrait;

    /**
     * Set a new client name.
     *
     * @param string $name The new client name.
     *
     * @return void
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }
}
