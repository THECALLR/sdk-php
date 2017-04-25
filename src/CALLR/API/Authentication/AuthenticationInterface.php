<?php
namespace CALLR\API\Authentication;

use InvalidArgumentException;

/**
 * Authentication interface
 *
 * @author Baptiste Clavié <baptiste.clavie@callr.com>
 */
interface AuthenticationInterface
{
    /**
     * Get the headers needed to auth
     *
     * @return string[]
     */
    public function getHeaders();

    /**
     * Apply some more curl options if needed
     *
     * @param resource $channel CURL channel to apply the options to
     */
    public function applyCurlOptions($channel);

    /**
     * Log as someone else
     *
     * @param string $type Type of login-as (user, customer, ...)
     * @param mixed $target Who to impersonnate
     *
     * @throws InvalidArgumentException Wrong value for $type
     */
    public function logAs($type, $target);
}
