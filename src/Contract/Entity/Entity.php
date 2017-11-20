<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Contract\Entity;

/**
 * An access token implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Contract\Entity
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
interface Entity
{
    /**
     * Get the associated numeric identifier.
     *
     * @return int
     */
    public function getId() : int;
}
