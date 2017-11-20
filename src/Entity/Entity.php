<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Entity;

/**
 * An entity implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Entity
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
class Entity implements \Ivyhjk\Phalcon\OAuth2\Server\Contract\Entity\Entity
{
    /**
     * Generate a new client entity.
     *
     * @param int $id The numeric identifier
     *
     * @return void
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Get the associated numeric identifier.
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }
}
