<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\Repository;

use Phalcon\Security;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * A base repository.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\Repository
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
abstract class BaseRepository
{
    /**
     * Phalcon security.
     *
     * @var mixed[null|Phalcon\Security]
     */
    private $security;

    /**
     * Get the phalcon security.
     *
     * @return Phalcon\Security
     */
    public function getSecurity() : Security
    {
        if ($this->security === null) {
            $this->security = new Security();
        }

        return $this->security;
    }

    /**
     * Get a new phalcon database builder.
     *
     * @return Phalcon\Mvc\Model\Query\Builder
     */
    public function getNewBuilder() : Builder
    {
        return new Builder();
    }
}
