<?php
namespace CALLR\API\Authentication;

/**
 * Login Password authentication
 *
 * Uses Basic to do so
 *
 * @author Baptiste Clavié <baptiste.clavie@callr.com>
 */
final class LoginPasswordAuth implements AuthenticationInterface
{
    /** @var string */
    private $login;

    /** @var string */
    private $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /** {@inheritDoc} */
    public function getHeaders()
    {
        return [];
    }

    /** {@inheritDoc} */
    public function applyCurlOptions($channel)
    {
        curl_setopt($channel, CURLOPT_USERPWD, "{$this->login}:{$this->password}");
    }
}

