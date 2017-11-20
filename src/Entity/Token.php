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
abstract class Token
{
    protected $id = null;

    /**
     * Get the associated numeric identifier.
     *
     * @return int
     */
    public function getId() : int
    {
        if ($this->id === null) {
            throw new \RuntimeException('Identifier is not setted.');
        }

        return $this->id;
    }

    /**
     * Set a new identifier.
     *
     * @param int $id The new identifier.
     *
     * @return void
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }
}
