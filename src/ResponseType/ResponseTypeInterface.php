<?php

namespace Ivyhjk\Phalcon\OAuth2\Server\ResponseType;

use Phalcon\Http\ResponseInterface;

/**
 * A response type interface for phalcon implementation.
 *
 * @since v1.0.0
 * @version v1.0.0
 * @package Ivyhjk\Phalcon\OAuth2\Server\ResponseType
 * @author Elvis Munoz <elvis.munoz.f@gmail.com>
 * @copyright Copyright (c) 2017, Elvis Munoz
 * @license https://opensource.org/licenses/MIT MIT License
 */
interface ResponseTypeInterface extends
    \League\OAuth2\Server\ResponseTypes\ResponseTypeInterface
{
    /**
     * Generate a new phalcon HTTP response.
     *
     * @param Phalcon\Http\ResponseInterface $response A Phalcon response.
     *
     * @return Phalcon\Http\ResponseInterface
     */
    public function generatePhalconHttpResponse(
        ResponseInterface $response
    ) : ResponseInterface;
}
