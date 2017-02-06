<?php
namespace CALLR\API\Authentication;

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
}

